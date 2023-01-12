<?php declare(strict_types=1);

namespace AdnanMula\Criteria;

use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Sorting\Sorting;

final class Criteria
{
    private ?Sorting $sorting;
    private array $filters;
    private ?int $offset;
    private ?int $limit;

    public function __construct(?Sorting $sorting, ?int $offset, ?int $limit, Filters ...$filters)
    {
        if (null !== $offset && null === $sorting) {
            throw new \InvalidArgumentException('Order by must be specified when using offset to avoid inconsistent results');
        }

        $this->sorting = $sorting;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->filters = $filters;
    }

    public function sorting(): ?Sorting
    {
        return $this->sorting;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    /** @return array<Filters> */
    public function filters(): array
    {
        return $this->filters;
    }
}
