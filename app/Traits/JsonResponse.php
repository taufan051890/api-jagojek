<?php

namespace App\Traits;

trait JsonResponse{

    /*
     * 401 Unatuhorized
     * 403 Not Found
     * 200 Success
     * 422 Form Error
     * 500 Server Error
     * */

    private function json200($param,$option=null){
        return response()->json(['code'=>200,'data'=>$param,'error'=>null],200,[],$option);
    }

    private function json401(){
        return response()->json(['code'=>401,'data'=>null,'error'=>'Unauthorized.'],401);
    }

    private function json403(){
        return response()->json(['code'=>403,'data'=>null,'error'=>'URL not Found.'],403);
    }

    private function json422($param){
        return response()->json(['code'=>422,'data'=>null,'error'=>$param],422);
    }

    private function json500($param = null){
        if($param==null){
            $param = 'Internal Server Error';
        }
        return response()->json(['code'=>500,'data'=>null,'error'=>$param],500);
    }

}
