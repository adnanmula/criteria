<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterValue;

final class IntFilterValue implements FilterValueInterface
{
    public function __construct(
        private readonly int $value,
    ) {}

    public function value(): int
    {
        return $this->value;
    }
}
