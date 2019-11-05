<?php


namespace App\Services;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\AbstractDecoder;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

trait ParttimeDiskProcess
{

    public static function getAvatarDirectory(){
        return Storage::disk('parttime_avatars');
    }

    public static function getParttimeImageDirectory($parttime_id){
        $disk = Storage::disk("parttime_images");
        $disk->makeDirectory($parttime_id);
        return $disk;
    }

    /*
     * @deprecated
     * @param $parttime_id
     * @param File[] $files
     * @param array $mimetypes
     * @return int The count of saved files
     *
    public static function saveParttimeImageDirectory($parttime_id, array $files, array $mimetypes = [
        'image/jpg', 'image/png', 'image/gif'
    ]){
        $disk = self::getParttimeImageDirectory($parttime_id);
        $collection = collect($files);
        $imgNum = 0;
        $disk->delete("/$parttime_id");
        while ($imgNum < self::getMaxImageUpload() &&
            ($file = $collection->shift()) != null
        ) {
            $imageName = "img_$imgNum";
            if (
                isset($mimetypes[$file->getMimeType()]) &&
                is_int(strpos($file->getFilename(), 'img'))
            ) {
                $imgNum++;
                $disk->put(
                    sprintf("/$parttime_id/$imageName.%s", $file->getExtension()),
                    file_get_contents($file->getRealPath())
                );
            }
        }
        return $imgNum;
    }
    */

    public static function saveAvatarImages($uid, UploadedFile $file){
        $disk = self::getAvatarDirectory();
        $filename = "$uid.jpg";
        /**
         * 用 Image::make() 创建一个 Image 对象
         *
         * Facades\Image 对应 @see ImageManager::make()
         *
         * Image::make(mixed) 参数接受多个类型
         * 常用: URL,Binary,Stream,FilePath,base64
         * 可接受 Laravel File类型！！！！
         * 源码 @see AbstractDecoder::init()
         *
         * @var Image $image
         */
        $image = \Intervention\Image\Facades\Image::make($file);

        # 输出的类型可以根据后缀判断，如果没有就检查MIME，还是没有就默认JPEG
        # JPEG 标准质量：60
        $disk->put($filename, $image->encode('jpg', 60)->getEncoded());
    }

    /**
     * @param $parttime_id
     * @param array $files
     * @return array
     */
    public static function saveParttimeImages($parttime_id, array $files){
        $disk = self::getParttimeImageDirectory($parttime_id);
        $relativePath = "/$parttime_id";
        $fileNames = [];
        foreach($files as $index => $fileObject){
            /** @var UploadedFile $fileObject */
            $fileName = "img_$index.jpg";
            $disk->put(
                "$relativePath/$fileName",
                \Intervention\Image\Facades\Image::make($fileObject)->encode('jpg', 60)->getEncoded()
            );
            $fileNames[] = $fileName;
        }
        return $fileNames;
    }
}