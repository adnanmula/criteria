<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Filter;

final readonly class CompositeFilter
{
    /** @var array<Filter|self> */
    private array $filters;

    public function __construct(
        private FilterType $filtersGlue,
        Filter|self ...$filters,
    ) {
        $this->filters = $filters;
    }
    public function filtersGlue(): FilterType
    {
        return $this->filtersGlue;
    }

    /** @return array<Filter|self> */
    public function filters(): array
    {
        return $this->filters;
    }
}
