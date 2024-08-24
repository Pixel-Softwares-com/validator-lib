<?php

namespace ValidatorLib\Helpers;

use Exception;
use Illuminate\Support\MessageBag;

class Helpers
{
    static public function getExceptionClass() : string
    {
        $customExceptionClass = config("validator-lib-config.custom-exception-class");
        return is_subclass_of($customExceptionClass , Exception::class)
               ? $customExceptionClass
               : Exception::class;
    }

    static public function getErrorsIndexedArray(MessageBag $bag): array
    {
        $errorBagArray = $bag->toArray();
        $array = [];
        foreach ($errorBagArray as $messages) {
            foreach ($messages as $message) {
                $array[] = $message;
            }
        }
        return $array;
    }

}