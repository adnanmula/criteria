<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Filter;

use AdnanMula\Criteria\FilterField\FilterFieldInterface;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\FilterValue;

final class Filter
{
    public function __construct(
        private readonly FilterFieldInterface $field,
        private readonly FilterValue $value,
        private readonly FilterOperator $operator,
    ) {}

    public function field(): FilterFieldInterface
    {
        return $this->field;
    }

    public function value(): FilterValue
    {
        return $this->value;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }
}
