<?php declare(strict_types=1);

namespace AdnanMula\Criteria;

use AdnanMula\Criteria\Filter\CompositeFilter;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Sorting\Sorting;

final readonly class Criteria
{
    public function __construct(
        private Filters $filters = new Filters(),
        private ?int $offset = null,
        private ?int $limit = null,
        private ?Sorting $sorting = null,
    ) {
        if (null !== $offset) {
            if (0 > $offset) {
                throw new \InvalidArgumentException('Invalid offset');
            }
        }

        if (null !== $limit) {
            if (0 >= $limit) {
                throw new \InvalidArgumentException('Invalid limit');
            }
        }

        if (null !== $offset && null === $sorting) {
            throw new \InvalidArgumentException('Order by must be specified when using offset to avoid inconsistent results');
        }
    }

    public function with(Filter|CompositeFilter ...$filters): self
    {
        return new self(
            $this->filters->with(...$filters),
            $this->offset,
            $this->limit,
            $this->sorting,
        );
    }

    public function withoutPagination(): self
    {
        return new self($this->filters, null, null, $this->sorting);
    }

    public function withoutPaginationAndSorting(): self
    {
        return new self($this->filters, null, null, null);
    }

    public function withoutFilters(): self
    {
        return new self(new Filters(), $this->offset, $this->limit, $this->sorting);
    }

    public function filters(): Filters
    {
        return $this->filters;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function sorting(): ?Sorting
    {
        return $this->sorting;
    }
}
