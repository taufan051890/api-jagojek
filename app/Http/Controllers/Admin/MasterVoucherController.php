<?php
/**
 * Copyright  (c)
 *
 * By : Farih Nazihullah
 * Created At  9/5/20, 11:18 AM
 *
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Master\Voucher;
use App\Traits\FileUpload;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class MasterVoucherController extends Controller
{
    use FileUpload, JsonResponse;

    public function getList(Request $request)
    {
        $feature = $request->get('feature') ?? null;
        $type = $request->get('type') ?? null;

        $select = [
            'feature',
            'type',
            'code',
            'banner',
            'start_at',
            'expired_at',
            'discount as discount_percentage',
            'minimum_order',
            'maximum_discount'
        ];

        $data = Voucher::select($select)
            ->where('status',true)
            ->where('expired_at','>=',Carbon::now());

        if($type)
        {
            $data->where('type',$type);
        }

        if($feature)
        {
            $data->where('feature',$feature);
        }

        return $this->json200($data->get());
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'feature' => 'required',
            'type' => 'required',
            'code' => 'required|unique:master.vouchers',
            'banner' => 'required|image',
            'minimum_order' => 'required|numeric',
            'maximum_discount' => 'required|numeric',
            'discount' => 'required|numeric',
            'start_at' => 'required|date_format:Y-m-d',
            'expired_at' => 'required|date_format:Y-m-d'
        ]);


        $voucher = new Voucher();
        $data = $request->all();
        $data['code'] = Str::upper($data['code']);
        $data['banner'] = $this->upload($request->file('banner'),'upload/voucher/');
        $voucher->fill($data);
        $voucher->save();

        return $voucher->refresh();
    }

    public function update(Request $request)
    {
        // TODO : UPDATE VOUCHER
    }

    public function destroy(Request $request)
    {
        // TODO : DELETE VOUCHER
    }
}
