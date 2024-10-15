<?php

namespace  ValidatorLib\CustomValidationRules\FileValidationRules\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait FileExtensionCheckingMethods
{

    protected array $allowedExtenstions = ["jpg" , "jpeg" , "png"  ,"bmp", "gif", "svg", "webp", "mp4" , "xlsx" , "csv" , "xls" ,  "json" , "docx" , "pdf"];
 
    
    public function allowFilesWithExtensions(array $extensions )  : self
    {
        $this->allowedExtenstions = $extensions;
        return $this;
    }
    public function allowImageFilesOnly() : self
    {
        $this->allowedExtenstions = ["jpg" , "jpeg" , "png"  , "gif", "svg", "webp"];
        return $this;
    }

    protected function checkFileExtension(UploadedFile $file) : bool
    {
        return in_array(
                            Str::lower($file->extension()) ,
                            /**
                             * @TODO Needs to check zip file extension handling (for backup system)
                             */
                            $this->allowedExtenstions
                        );
    }


}