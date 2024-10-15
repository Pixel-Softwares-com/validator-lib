<?php

namespace  ValidatorLib\CustomValidationRules\FileValidationRules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use ValidatorLib\CustomValidationRules\FileValidationRules\Traits\FileExtensionCheckingMethods;

class SingleFileOrSinglePathString implements Rule
{
    use FileExtensionCheckingMethods;
  
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
