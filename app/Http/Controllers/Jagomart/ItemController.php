<?php
namespace App\Http\Controllers\Jagomart;

use App\Http\Controllers\Controller;
use App\Models\Jagomart\Category;
use App\Models\Jagomart\Item;
use App\Services\Jagomart\JagomartRequest;
use App\Traits\FileUpload;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    use JsonResponse;
    use FileUpload;
    public $outlet_id;

    public function __construct()
    {
    	$this->outlet_id = request()->user->outlet_id;
    }

    public function getListCategory(){
        $data = DB::table('jagomart.category')
            ->select('jagomart.category.*')
            ->addSelect(DB::raw('COALESCE((SELECT count(category_id) as total_item FROM jagomart.item
            WHERE category_id = jagomart.category.id GROUP BY category_id),0) as total_item'))
            ->where('outlet_id',$this->outlet_id);

        $active = clone $data;
        $inactive = clone $data;

        $active = $active->where('status',true)
            ->get();

        $inactive = $inactive->where('status',false)
            ->get();

        return $this->json200(['active'=>$active,'inactive'=>$inactive]);
    }

    public function createCategory(Request $request){
        //Validate Here


        //Begin Insert Data
        $category = Category::firstOrNew([
            'name' => $request->input('name'),
            'outlet_id' => $this->outlet_id,
        ]);

        if($category->id){
            return $this->json500('Kategori Item sudah ada.');
        }

        $category->status = $request->input('status')==1;

        if($category->save()){
            return $this->json200($category);
        }else{
            return $this->json500('Gagal membuat kategori baru');
        }

    }

    public function editCategory(Request $request){
        //Validate Data
        $check_unique = Category::where('name',$request->input('name'))
            ->where('outlet_id',$this->outlet_id)
            ->where('status',$request->input('status'))
            ->count();
        if($check_unique > 0){
            return $this->json500('Kategori sudah ada.');
        }

        //Begin Update Data
        $category = Category::where('outlet_id',$this->outlet_id)->find($request->input('id_category'));

        if($category){
            $category->name = $request->input('name');
            $category->status = $request->input('status') == 1;
            $category->save();
            return $this->json200($category);
        }else{
            return $this->json500('Kategori tidak ditemukan.');
        }
    }

    public function createItem(Request $request, JagomartRequest $valid){
        //Validate here
        $check = $valid->validateNewItem($request);

        if(is_string($check)){
            return $check;
        }

        $check_category = Category::where('outlet_id',$this->outlet_id)
            ->find($request->input('category_id'));
        if(!$check_category){
            return $this->json401();
        }

        //Begin Insert Data
        $item = new Item();
        $item->category_id = $request->input('category_id');
        $item->name = $request->input('name');
        $item->price = $request->input('price');
        $item->description = $request->input('description');
        $item->stock = $request->input('stock');
        $item->status = $request->input('status') == 1;

        if($request->hasFile('preview')){
            $item->preview = $this->upload($request->file('preview'),'jagomart/item/preview/');
        }

        if($item->save()){
            $item->refresh();
            return $this->json200($item);
        }else{
            return $this->json500('Gagal menambah item baru.');
        }

    }

    public function listItem(Request $request){
        $category = $request->get('category');

        $data = Item::select(DB::raw('*'));
        $data->where('category_id',$category);

        if($request->get('limit')){
            $offset = $request->get('offset') ?? 0;
            $data->limit($request->get('limit'));
            $data->offset($offset);
        }

        if($request->get('sort_by')){
            $order = explode('_',$request->get('sort_by'));
            $data->orderBy($order[1],$order[0]);
        }

        $active = clone $data;
        $inactive = clone $data;

        $active = $active->where('status',true)->get();
        $inactive = $inactive->where('status',false)->get();


        return $this->json200(['active'=>$active, 'inactive'=>$inactive]);
    }

}
