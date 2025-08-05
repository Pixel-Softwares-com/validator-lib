<?php

namespace  ValidatorLib\Traits;

use ValidatorLib\Validator;
use Exception;

trait ValidationRulesHandler
{

    protected array $allRules = [];
    protected bool $setDefaultRules = true;

    /////////////////////////////////////////////////////
    //NOTE :
    // If You Want To change Data Array , And Your Validation Rules Needs Some values from That Data Array
    // Every Rules determination methods must be called after data array Setting method
    /////////////////////////////////////////////////////

    /**
     * @param array $ValidationRuleKeyDetailPairs
     * @return Validator|ValidationRulesHandler
     * Keys and validation rule arrays which will not be checked if they are found in the all rules array (that coming from FromRequest's rules method)
     */
    public function applyCustomRuleSet(array $ValidationRuleKeyDetailPairs) : self
    {
        $this->allRules = $ValidationRuleKeyDetailPairs;
        return $this;
    }
    /**
     * @param array $ValidationRuleKeys
     * @return Validator|ValidationRulesHandler
     * @throws  Exception
     * This Method is used to set Request Key Validations array
     * It is useful to set it by this method when you want to set it later (not in constructor)
     *
     * Expects a partial rule set (which is contained in the total rule set)
     */
    public function OnlyRules(array $ValidationRuleKeys): self
    {
        $newRules = [];
        $this->AllRules();
        foreach ($ValidationRuleKeys as  $rule)
        {
            if (is_string($rule) && array_key_exists($rule, $this->allRules))
            {
                $newRules[$rule] = $this->allRules[$rule];
            }
        }
        $this->allRules = $newRules;
        return $this;
    }

    /**
     * @param array $NonDesiredValidationRuleKeys
     * @return Validator|ValidationRulesHandler
     *
     * Expects a partial rule set (which is contained in the total rule set)
     */
    public function ExceptRules(array $NonDesiredValidationRuleKeys): self
    {
        $this->AllRules();
        foreach ($NonDesiredValidationRuleKeys as $rule)
        {
            if (is_string($rule) && array_key_exists($rule, $this->allRules))
            {
                unset($this->allRules[$rule]) ;
            }
        }
        return $this;
    }

    /**
     * @return Validator|ValidationRulesHandler
     */
    public function AllRules() : self
    {
        $this->allRules = $this->requestFormOb->rules($this->data);
        $this->setDefaultRules = false;
        return $this;
    }

    /**
     * @return Validator|ValidationRulesHandler
     */
    public function UnsetRulesAndCancel() : self
    {
        $this->allRules = [];
        $this->setDefaultRules = false;
        return $this;
    }

    public function applyBailRule(array $onValidationRuleKeys = []) : self
    {
        /** if there is no specific rule set ... bail rule will be applied on the all current used rules */
        if(empty($onValidationRuleKeys)){$onValidationRuleKeys = array_keys($this->allRules);}
        foreach ($onValidationRuleKeys as $key)
        {
            if(array_key_exists($key , $this->allRules))
            {
                $keyRuleset = $this->allRules[$key];
                if(is_array($keyRuleset))
                {
                    array_unshift($keyRuleset ,"bail"   );
                }

                if (is_string($keyRuleset))
                {
                    $keyRuleset = "bail|" . $keyRuleset;
                }
                $this->allRules[$key] = $keyRuleset;
            }
        }
        return $this;
    }

    public function getCurrentlyUsedRule() : array
    {
        return $this->allRules;
    }
}
