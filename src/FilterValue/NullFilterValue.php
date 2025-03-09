<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterValue;

final class NullFilterValue implements FilterValueInterface
{
    public function __construct()
    {}

    public function value(): mixed
    {
        return null;
    }
}
