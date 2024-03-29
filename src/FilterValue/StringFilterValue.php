<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterValue;

final class StringFilterValue implements FilterValueInterface
{
    public function __construct(
        private readonly string $value,
    ) {}

    public function value(): string
    {
        return $this->value;
    }
}
