<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterGroup;

use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;

abstract class FilterGroup implements FilterGroupInterface
{
    /** @var array<Filter> */
    private readonly array $filters;

    public function __construct(
        private readonly FilterType $filtersGlue,
        Filter ...$filters,
    ) {
        $this->filters = $filters;
    }

    abstract public function expressionType(): FilterType;

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
