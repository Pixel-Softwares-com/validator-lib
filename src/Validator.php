<?php

namespace ValidatorLib;

use AuthorizationManagement\PermissionExaminers\PermissionExaminer;
use ValidatorLib\Traits\ValidationRulesHandler;
use ValidatorLib\CustomFormRequest\BaseFormRequest;
use Exception;
use Illuminate\Contracts\Validation\Validator as ValidationResultOb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use ValidatorLib\Helpers\Helpers;


abstract class Validator
{
    use ValidationRulesHandler;

    protected array $data = [];
    protected BaseFormRequest $requestFormOb;
    protected bool $inInitializing = false;

    /**
     * @param string|BaseFormRequest $requestForm
     * @param Request|array|null $request
     */
    public function __construct(string | BaseFormRequest $requestForm , Request | array | null  $request = null)
    {
        $this->startInitialization();

        $this->setRequestData($request);
        $this->changeRequestClass($requestForm);
        $this->authorizeRequest();
        
        $this->finishInitialization();
    }

    protected function startInitialization() : void
    {
        $this->inInitializing = true;
    }

    protected function finishInitialization() : void
    {
        $this->inInitializing = false;
    }

    protected function isInInitialization() : bool
    {
        return $this->inInitializing;
    }

    protected function IsBaseFormRequest(string | BaseFormRequest $requestForm) : bool
    {
        return $requestForm instanceof BaseFormRequest;
    }
    protected function setBaseRequestForm(BaseFormRequest $requestForm) : Validator
    {
        $this->requestFormOb = $requestForm;
        // Reset rules to reload from new FormRequest (except during initialization)
        if (!$this->isInInitialization()) {
            $this->resetRules();
        }
        return $this;
    }
    /**
     * @param string |  BaseFormRequest $requestForm
     * @return $this
     */
    public function changeRequestClass(string | BaseFormRequest $requestForm): self
    {
        if ($this->IsBaseFormRequest($requestForm))
        {
            return $this->setBaseRequestForm($requestForm);
        }

        /** If It Not BaseFormRequest object ... it is a string  */

        if (!class_exists($requestForm))
        {
            throw new Exception("The Given Request Class Is Invalid Class !");
        }
        /** initializing a new BaseRequestForm object */
        $requestForm = new $requestForm();

        /**  Now We need to check if it is BaseFormRequest Object */
        if (!$this->IsBaseFormRequest($requestForm))
        {
            throw new Exception("The Given Request Class Is Invalid Request  Form Class !");
        }
        return $this->setBaseRequestForm($requestForm);
    }

    /**
     * @return void
     *
     * Authorize before validation .... normally this action is called after resolving the formRequest object
     * (( during the injecting it to the controller method  )) ... but it is implementing ValidatesWhenResolved contract
     * and does the validation during the injection of the request form object and avoid us to doing the on  our own steps .
     */
    protected function authorizeRequest() : void
    {
        if( method_exists($this->requestFormOb, 'authorize') )
        {
            if(!$this->requestFormOb->authorize())
            {
                throw PermissionExaminer::getUnAuthenticatingException();
            }
        }
    }

    /**
     * @return BaseFormRequest
     */
    public function getRequestFormOb(): BaseFormRequest
    {
        return $this->requestFormOb;
    }


    /**
     * @param Request|array|null $data
     * @return $this
     */
    public function setRequestData( Request | array  | null $data): self
    {
        if ($data instanceof Request)
        {
            $data = $data->all();
        }
        if(!$data)
        {
            $data = request()->all();
        }
        $this->data = $data;
        // Reset rules to reload with new data (rules may depend on data)
        // But skip during initialization (constructor)
        if (!$this->isInInitialization()) {
            $this->resetRules();
        }
        return $this;
    }

    public function addToRequestData(string $key , mixed $value) : self
    {
        $this->data[$key] = $value;
        // Reset rules to reload with new data (rules may depend on data)
        $this->resetRules();
        return $this;
    }

    /**
     * Reset validation rules to be reloaded from FormRequest
     * @return $this
     */
    protected function resetRules(): self
    {
        $this->currentUsedRules = [];
        $this->setDefaultRules = true;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequestData(): array
    {
        return $this->data;
    }

    /**
     * @return ValidationResultOb|null
     * @throws Exception
     */
    protected function getValidationResultOb(): ValidationResultOb | null
    {
        if (empty($this->currentUsedRules) && $this->setDefaultRules)
        {
            $this->setAllRules();
        }
        
        if (empty($this->currentUsedRules)) {
            return null;
        }
        $validationMessages = $this->requestFormOb->messages();
        return ValidatorFacade::make($this->data, $this->currentUsedRules, $validationMessages);
    }

    /**
     * @param ValidationResultOb $validatorResultOb
     * @return array|bool|JsonResponse|RedirectResponse
     */
    abstract protected function ErrorResponder(ValidationResultOb $validatorResultOb): array | bool | JsonResponse | RedirectResponse;

    protected function validateResponder(?ValidationResultOb $validatorResultOb = null): array | bool | JsonResponse | RedirectResponse
    {
        //This happens when no validation is required (when rules Array is empty = the validation operation is done)
        if ($validatorResultOb == null) {
            return true;
        }

        //this happen
        if ($validatorResultOb->fails()) {
            return $this->ErrorResponder($validatorResultOb);
        }

        //Validation is done
        return true;
    }

    /**
     * @return array|bool|JsonResponse|RedirectResponse
     * @throws Exception
     */
    public function validate(): array | bool | JsonResponse | RedirectResponse
    {
        return $this->validateResponder($this->getValidationResultOb());
    }
}
