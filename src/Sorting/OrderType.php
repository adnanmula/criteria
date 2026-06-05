<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Sorting;

enum OrderType: string
{
    case ASC = 'asc';
    case ASC_NULLS_FIRST = 'asc nulls first';
    case ASC_NULLS_LAST = 'asc nulls last';
    case DESC = 'desc';
    case DESC_NULLS_FIRST = 'desc nulls first';
    case DESC_NULLS_LAST = 'desc nulls last';
}
