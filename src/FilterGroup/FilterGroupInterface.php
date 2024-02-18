<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterGroup;

use AdnanMula\Criteria\Filter\FilterType;

interface FilterGroupInterface
{
    public function expressionType(): FilterType;

    public function filtersGlue(): FilterType;

    /** @return array<FilterGroup> */
    public function filters(): array;
}
