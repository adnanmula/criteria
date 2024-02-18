<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterGroup;

use AdnanMula\Criteria\Filter\FilterType;

final class AndFilterGroup extends FilterGroup
{
    public function expressionType(): FilterType
    {
        return FilterType::AND;
    }
}
