<?php
namespace App\Http\Controllers\Jagofood;

use App\Http\Controllers\Controller;
use App\Models\Jagofood\Food;
use App\Traits\FileUpload;
use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jagofood\Category;

class FoodController extends Controller
{
    use JsonResponse, FileUpload;

    public $outlet_id;

    public function __construct()
    {
    	$this->outlet_id = request()->user->outlet_id;
    }

    public function getListCategory(){
        $data = DB::table('jagofood.category')
            ->select('jagofood.category.*')
            ->addSelect(DB::raw('COALESCE((SELECT count(category_id) as total_food FROM jagofood.food
            WHERE category_id = jagofood.category.id GROUP BY category_id),0) as total_food'))
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
            return $this->json500('Kategori Menu sudah ada.');
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

    public function createFood(Request $request){
        //Validate here

        $check_category = Category::where('outlet_id',$this->outlet_id)
            ->find($request->input('category_id'));
        if(!$check_category){
            return $this->json401();
        }

        try {
            //Begin Insert Data
            $food = new Food();
            $food->category_id = $request->input('category_id');
            $food->name = $request->input('name');
            $food->price = $request->input('price');
            $food->description = $request->input('description');
            $food->status = $request->input('status') == 1;

            if ($request->hasFile('preview')) {

                //Logic Upload Image
                $food->preview = $this->upload($request->file('preview'),'jagofood/item/preview/');

        } else {
                $food->preview = 'https://pakorapalace.ca/img/placeholders/xcomfort_food_placeholder.png,qv=1.pagespeed.ic.x100Yi-Swz.png';
            }

            if ($food->save()) {
                return $this->json200($food);
            } else {
                return $this->json500('Gagal menambah food baru.');
            }
        }catch(\Exception $e){
            return $this->json500($e->getMessage());
        }

    }

    public function listFood(Request $request){
        $category = $request->get('category');

        $check_category = Category::where('outlet_id',$this->outlet_id)
            ->find($request->get('category'));
        if(!$check_category){
            return $this->json401();
        }

        $data = DB::table('jagofood.food');
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

    // public function getFoodPromo() {

    // }

}
