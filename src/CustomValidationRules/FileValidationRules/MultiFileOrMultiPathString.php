<?php

namespace  ValidatorLib\CustomValidationRules\FileValidationRules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MultiFileOrMultiPathString implements Rule
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
    protected function checkArrayValue(array $value) : bool
    {
        foreach ($value as $file)
        {
            if($file instanceof UploadedFile)
            {
                if(!$this->checkFileExtension($file))
                {
                    return false;
                }
                continue;
            }
            return false;
        }
        return true;
    }

    protected function checkJsonStringValue(string $jsonString )  :bool
    {
        return json_decode($jsonString , true);
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
            return $this->checkJsonStringValue($value);
        }
        if(is_array($value))
        {
            return $this->checkArrayValue($value);
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
        return ":attribute 's value must be array Of Files OR File paths JSON string";
    }
}
