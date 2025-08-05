<?php declare(strict_types=1);

namespace AdnanMula\Criteria;

use AdnanMula\Criteria\FilterGroup\FilterGroup;
use AdnanMula\Criteria\Sorting\Sorting;

final class Criteria
{
    /** @var array<FilterGroup> */
    private readonly array $filterGroups;

    public function __construct(
        private readonly ?int $offset,
        private readonly ?int $limit,
        private readonly ?Sorting $sorting,
        FilterGroup ...$filterGroups,
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

        $this->filterGroups = $filterGroups;
    }

    public function with(FilterGroup ...$groups): self
    {
        return new self(
            $this->offset,
            $this->limit,
            $this->sorting,
            ...$this->filterGroups,
            ...$groups,
        );
    }

    public function withoutPagination(): self
    {
        return new self(null, null, $this->sorting, ...$this->filterGroups);
    }

    public function withoutPaginationAndSorting(): self
    {
        return new self(null, null, null, ...$this->filterGroups);
    }

    public function withoutFilters(): self
    {
        return new self($this->offset, $this->limit, $this->sorting);
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

    /** @return array<FilterGroup> */
    public function filterGroups(): array
    {
        return $this->filterGroups;
    }
}
