<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

trait FileUpload {


    /**
     * @param $files
     * @param null $path example : jagomart/file/
     * @return string
     */
    function upload($files, $path=null){
        try{
            $url = config('upload.url');
            $folder = config('upload.folder');

            $path = Str::of($path)->replace('/',DIRECTORY_SEPARATOR);

            $ext = $files->getClientOriginalExtension();

            $file_name = time().Str::random(16).'.'.$ext;
            $destination_path = $folder . $path;

            if(!file_exists($destination_path)){
                File::makeDirectory($destination_path,0775, true);
            }

            $files->move($destination_path, $file_name);

            return $url.$path.$file_name;

        }catch (\Exception $e){
            return $e->getMessage();
        }

    }

    function delete(){
        $url = config('upload.url');
        $folder = config('upload.path');
    }

}
