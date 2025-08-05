<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterValue;

enum FilterOperator
{
    case EQUAL;
    case NOT_EQUAL;
    case GREATER;
    case GREATER_OR_EQUAL;
    case LESS;
    case LESS_OR_EQUAL;
    case CONTAINS;
    case NOT_CONTAINS;
    case CONTAINS_INSENSITIVE;
    case NOT_CONTAINS_INSENSITIVE;
    case IN;
    case NOT_IN;
    case IS_NULL;
    case IS_NOT_NULL;
    case IN_ARRAY;
    case NOT_IN_ARRAY;

    public function isComparisonOperator(): bool
    {
        return $this === self::EQUAL
            || $this === self::NOT_EQUAL
            || $this === self::GREATER
            || $this === self::GREATER_OR_EQUAL
            || $this === self::LESS
            || $this === self::LESS_OR_EQUAL;
    }

    public function isInOperator(): bool
    {
        return $this === self::IN
            || $this === self::NOT_IN;
    }

    public function isContainsOperator(): bool
    {
        return $this === self::CONTAINS
            || $this === self::CONTAINS_INSENSITIVE
            || $this === self::NOT_CONTAINS
            || $this === self::NOT_CONTAINS_INSENSITIVE;
    }

    public function isNullOperator(): bool
    {
        return $this === self::IS_NULL
            || $this === self::IS_NOT_NULL;
    }
}
