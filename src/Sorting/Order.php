<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Sorting;

use AdnanMula\Criteria\FilterField\FilterFieldInterface;

final class Order
{
    public function __construct(
        private readonly FilterFieldInterface $field,
        private readonly OrderType $type,
    ) {}

    public function field(): FilterFieldInterface
    {
        return $this->field;
    }

    public function type(): OrderType
    {
        return $this->type;
    }
}
