<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterGroup;

use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;

abstract class FilterGroup implements FilterGroupInterface
{
    private readonly FilterType $filtersGlue;
    private readonly array $filters;

    public function __construct(FilterType $filtersGlue, Filter ...$filters)
    {
        $this->filtersGlue = $filtersGlue;
        $this->filters = $filters;
    }

    abstract function expressionType(): FilterType;

    public function filtersGlue(): FilterType
    {
        return $this->filtersGlue;
    }

    /** @return array<Filter> */
    public function filters(): array
    {
        return $this->filters;
    }
}
