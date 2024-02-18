<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterGroup;

use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;

interface FilterGroupInterface
{
    public function expressionType(): FilterType;

    public function filtersGlue(): FilterType;

    /** @return array<Filter> */
    public function filters(): array;
}
