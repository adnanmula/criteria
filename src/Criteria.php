<?php declare(strict_types=1);

namespace AdnanMula\Criteria;

use AdnanMula\Criteria\FilterGroup\FilterGroup;
use AdnanMula\Criteria\Sorting\Sorting;

final class Criteria
{
    private ?int $offset;
    private ?int $limit;
    private ?Sorting $sorting;
    /** @var array<FilterGroup> */
    private array $filterGroups;

    public function __construct(?int $offset, ?int $limit, ?Sorting $sorting, FilterGroup ...$filterGroups)
    {
        if (null !== $offset && null === $sorting) {
            throw new \InvalidArgumentException('Order by must be specified when using offset to avoid inconsistent results');
        }

        $this->offset = $offset;
        $this->limit = $limit;
        $this->sorting = $sorting;
        $this->filterGroups = $filterGroups;
    }

    public function withoutPagination(): self
    {
        return new self(null, null, $this->sorting, ...$this->filterGroups);
    }

    public function with(FilterGroup $group): self
    {
        return new self(
            $this->offset,
            $this->limit,
            $this->sorting,
            $group,
            ...$this->filterGroups,
        );
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
