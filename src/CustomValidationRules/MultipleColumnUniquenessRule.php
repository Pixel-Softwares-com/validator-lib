<?php

namespace  ValidatorLib\CustomValidationRules;

use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rules\Unique;


class MultipleColumnUniquenessRule extends Unique
{
    protected array $requestData = [];

    public function setData($data = []) : self
    {
        $this->requestData = $data;
        return $this;
    }

    public function __construct(string $table,  array $requestData = [] ,...$compositeUniqueIndexOtherColumns )
    {
        parent::__construct($table);
        $this->setData($requestData);
        $this->setCompositeUniqueIndexOtherColumnConditions($compositeUniqueIndexOtherColumns);
    }

    /**
     * @param array $compositeUniqueIndexOtherColumns
     * @return void
     * This method will loop on the other columns , while the main attribute will be checked by the Unique parent methods
     */
    protected function setCompositeUniqueIndexOtherColumnConditions(array $compositeUniqueIndexOtherColumns) : void
    {
        $this->where(function (Builder $query) use($compositeUniqueIndexOtherColumns)
        {
            foreach ($compositeUniqueIndexOtherColumns as $column)
            {
                $query->where($column , "=" , $this->requestData[$column] ?? null );
            }
        });
    }


}
