<?php

namespace ValidatorLib;

use ValidatorLib\ValidatorHelpers\Helpers;
use Illuminate\Contracts\Validation\Validator as ValidationResultOb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class JSONValidator extends Validator
{

    /**
     * @param ValidationResultOb $validatorResultOb
     * @return array|bool|JsonResponse|RedirectResponse
     */
    protected function ErrorResponder(ValidationResultOb $validatorResultOb) : array | bool | JsonResponse | RedirectResponse
    {
        $errors = Helpers::getErrorsIndexedArray($validatorResultOb->errors());
        $exceptionClass = Helpers::getExceptionClass();
        throw new $exceptionClass(join(" , " , $errors) , 406);
    }

}
