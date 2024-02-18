<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterValue;

final class StringArrayFilterValue implements FilterValueInterface
{
    private readonly array $value;

    public function __construct(string ...$value)
    {
        $this->value = $value;
    }

    /** @return array<string> */
    public function value(): array
    {
        return $this->value;
    }
}
