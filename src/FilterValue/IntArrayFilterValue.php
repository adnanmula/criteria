<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterValue;

final class IntArrayFilterValue implements FilterValueInterface
{
    /** @var array<int> */
    private readonly array $value;

    public function __construct(int ...$value)
    {
        $this->value = $value;
    }

    /** @return array<int> */
    public function value(): array
    {
        return $this->value;
    }
}
