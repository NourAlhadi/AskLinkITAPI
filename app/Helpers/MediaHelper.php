<?php
/**
 * Created by PhpStorm.
 * User: nouralhadi
 * Date: 4/18/19
 * Time: 1:07 AM
 */

namespace App\Helpers;


use Illuminate\Http\UploadedFile as File;
use Illuminate\Support\Facades\Storage;

class MediaHelper{

    /**
     * @param $file File
     * @param $path string|null
     * @return string
     */
    public function uploadFile($file,$path = null){
        Storage::disk('local')->put($path,$file);
        return $file->hashName();
    }

    /**
     * @param $file string
     * @param $path string
     * @return string
     */
    public function getUploadedFileUrl($file,$path){
        return Storage::disk('local')->url($path . DIRECTORY_SEPARATOR . $file);
    }

    /**
     * @param $file string
     * @param $path string
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getUploadedFile($file,$path){
        $path = $path . DIRECTORY_SEPARATOR . $file;
        return Storage::disk('local')->get($path);
    }
}