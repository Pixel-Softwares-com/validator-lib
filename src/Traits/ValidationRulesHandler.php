<?php

namespace  ValidatorLib\Traits;

use ValidatorLib\Validator;
use Exception;

trait ValidationRulesHandler
{

    protected array $currentUsedRules = [];
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
        $this->currentUsedRules = $ValidationRuleKeyDetailPairs;
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
        $this->setAllRules();
        foreach ($ValidationRuleKeys as  $rule)
        {
            if (is_string($rule) && array_key_exists($rule, $this->currentUsedRules))
            {
                $newRules[$rule] = $this->currentUsedRules[$rule];
            }
        }
        $this->currentUsedRules = $newRules;
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
        $this->setAllRules();
        foreach ($NonDesiredValidationRuleKeys as $rule)
        {
            if (is_string($rule) && array_key_exists($rule, $this->currentUsedRules))
            {
                unset($this->currentUsedRules[$rule]) ;
            }
        }
        return $this;
    }

    /**
     * @return Validator|ValidationRulesHandler
     */
    public function setAllRules() : self
    {
        $this->currentUsedRules = $this->requestFormOb->rules($this->data);
        $this->setDefaultRules = false;
        return $this;
    }

    /**
     * @return Validator|ValidationRulesHandler
     */
    public function UnsetRulesAndCancel() : self
    {
        $this->currentUsedRules = [];
        $this->setDefaultRules = false;
        return $this;
    }

    public function applyBailRule(array $onValidationRuleKeys = []) : self
    {
        /** if there is no specific rule set ... bail rule will be applied on the all current used rules */
        if(empty($onValidationRuleKeys)){$onValidationRuleKeys = array_keys($this->currentUsedRules);}
        foreach ($onValidationRuleKeys as $key)
        {
            if(array_key_exists($key , $this->currentUsedRules))
            {
                $keyRuleset = $this->currentUsedRules[$key];
                if(is_array($keyRuleset))
                {
                    array_unshift($keyRuleset ,"bail"   );
                }

                if (is_string($keyRuleset))
                {
                    $keyRuleset = "bail|" . $keyRuleset;
                }
                $this->currentUsedRules[$key] = $keyRuleset;
            }
        }
        return $this;
    }

    public function getCurrentlyUsedRule() : array
    {
        return $this->currentUsedRules;
    }
}
