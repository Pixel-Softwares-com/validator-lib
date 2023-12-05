<?php

namespace  ValidatorLib\CustomValidationRules\FileValidationRules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class SingleFileOrSinglePathString implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    protected function checkFileExtension(UploadedFile $file) : bool
    {
        return in_array(
                            Str::lower($file->extension()) ,
                            /**
                             * @TODO Needs to check zip file extension handling (for backup system)
                             */
                            ["jpg" , "jpeg" , "png"  ,"bmp", "gif", "svg", "webp", "mp4" , "xlsx" , "csv" , "xls" ,  "json" , "docx" , "pdf"]
                        );
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(is_string( $value ))
        {
            return true;
        }
        if($value instanceof UploadedFile)
        {
            return $this->checkFileExtension($value);
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ":attribute 's value must be a file or a single file path's string";
    }
}
