<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterValue;

final class IntFilterValue implements FilterValue
{
    public function __construct(
        private readonly int $value,
    ) {}

    public function value(): int
    {
        return $this->value;
    }
}
