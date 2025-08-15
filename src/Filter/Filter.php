<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Filter;

use AdnanMula\Criteria\FilterField\FilterFieldInterface;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\FilterValueInterface;
use AdnanMula\Criteria\FilterValue\IntArrayFilterValue;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final readonly class Filter
{
    public function __construct(
        private FilterFieldInterface $field,
        private FilterValueInterface $value,
        private FilterOperator $operator,
    ) {
        if ($operator->isComparison()
            && false === $value instanceof StringFilterValue
            && false === $value instanceof IntFilterValue) {
            throw new \InvalidArgumentException('Comparison operators must use StringFilterValue or IntFilterValue');
        }

        if ($operator->isTextSearch() && false === $value instanceof StringFilterValue) {
            throw new \InvalidArgumentException('Text search operators must use StringFilterValue');
        }

        if ($operator->isCollection()
            && false === $value instanceof StringArrayFilterValue
            && false === $value instanceof IntArrayFilterValue) {
            throw new \InvalidArgumentException('Collection operators must use StringArrayFilterValue or IntArrayFilterValue');
        }

        if ($operator->isJsonArrayOperation() && false === $value instanceof ArrayElementFilterValue) {
            throw new \InvalidArgumentException('Json array operators must use ArrayElementFilterValue');
        }
    }

    public function field(): FilterFieldInterface
    {
        return $this->field;
    }

    public function value(): FilterValueInterface
    {
        return $this->value;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }
}
