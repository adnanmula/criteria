<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterField;

final class FilterField implements FilterFieldInterface
{
    public function __construct(
        private string $name,
    ) {}

    public function name(): string
    {
        return $this->name;
    }
}
