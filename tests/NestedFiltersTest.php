<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Tests;

use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\CompositeFilter;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use PHPUnit\Framework\TestCase;

final class NestedFiltersTest extends TestCase
{
    use DbConnectionTrait;

    public static function setUpBeforeClass(): void
    {
        self::setUpDb();
    }

    public function testNestedCompositeFilters1(): void
    {
        $fieldMapping = [
            'domainId' => 'id',
            'otherField' => 'always_null',
        ];

        $c = new Criteria(
            new Filters(
                FilterType::AND,
                new Filter(new FilterField('otherField'), new NullFilterValue(), FilterOperator::IS_NULL),
                new CompositeFilter(
                    FilterType::OR,
                    new Filter(new FilterField('domainId'), new IntFilterValue(3), FilterOperator::EQUAL),
                    new Filter(new FilterField('random_string_or_null'), new StringFilterValue('imnotrandom'), FilterOperator::EQUAL),
                    new CompositeFilter(
                        FilterType::OR,
                        new Filter(new FilterField('domainId'), new IntFilterValue(3), FilterOperator::EQUAL),
                        new Filter(new FilterField('random_string_or_null'), new StringFilterValue('imnotrandom'), FilterOperator::EQUAL),
                        new Filter(new FilterField('domainId'), new IntFilterValue(50), FilterOperator::GREATER_OR_EQUAL),
                    ),
                ),
                new Filter(new FilterField('random_numbers'), new IntFilterValue(1000), FilterOperator::GREATER_OR_EQUAL),
            ),
        );

        $result = $this->search($c, $fieldMapping);

        self::assertGreaterThan(0, \count($result));
    }
}
