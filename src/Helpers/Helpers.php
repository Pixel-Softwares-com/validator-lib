<?php

namespace ValidatorLib\Helpers;

use Exception;
use Illuminate\Support\MessageBag;

class Helpers
{
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