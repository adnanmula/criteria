<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Filter;

final readonly class Filters
{
    /** @var array<Filter|CompositeFilter> */
    private array $filters;

    public function __construct(
        private FilterType $filtersGlue = FilterType::AND,
        Filter|CompositeFilter ...$filters,
    ) {
        $this->filters = $filters;
    }

    public function filtersGlue(): FilterType
    {
        return $this->filtersGlue;
    }

    /** @return array<Filter|CompositeFilter> */
    public function filters(): array
    {
        return $this->filters;
    }

    public function with(Filter|CompositeFilter ...$filter): self
    {
        return new self(
            $this->filtersGlue,
            ...$this->filters,
            ...$filter,
        );
    }
}
