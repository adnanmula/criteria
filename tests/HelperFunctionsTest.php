<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Tests;

use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\CompositeFilter;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use PHPUnit\Framework\TestCase;

final class HelperFunctionsTest extends TestCase
{
    use DbConnectionTrait;

    public static function setUpBeforeClass(): void
    {
        self::setUpDb();
    }

    public function testWithoutSorting(): void
    {
        $c = new Criteria(
            offset: 0,
            limit: 10,
            sorting: new Sorting(new Order(new FilterField('id'), OrderType::DESC)),
        );

        $result = $this->search($c->withoutPaginationAndSorting());

        self::assertCount(100, $result);
        self::assertEquals(1, $result[0]['id']);
    }

    public function testWithoutPagination(): void
    {
        $c = new Criteria(
            offset: 1,
            limit: 4,
            sorting: new Sorting(new Order(new FilterField('id'), OrderType::ASC)),
        );

        $result = $this->search($c->withoutPagination());

        self::assertCount(100, $result);
    }

    public function testWithoutFilters(): void
    {
        $c = new Criteria(
            new Filters(
                FilterType::AND,
                new Filter(new FilterField('id'), new IntFilterValue(10), FilterOperator::EQUAL),
            ),
            0,
            10,
            new Sorting(new Order(new FilterField('id'), OrderType::DESC)),
        );

        $result = $this->search($c->withoutFilters());

        self::assertCount(10, $result);
        self::assertEquals(100, $result[0]['id']);
    }

    public function testAddOneMoreFilter(): void
    {
        $c = new Criteria();

        $c = $c->with(
            new Filter(new FilterField('id'), new IntFilterValue(10), FilterOperator::EQUAL),
        );

        $result = $this->search($c->withoutPagination());

        self::assertCount(1, $result);
    }

    public function testAddOneTwoFilters(): void
    {
        $c = new Criteria();

        $c = $c->with(
            new CompositeFilter(
                FilterType::AND,
                new CompositeFilter(
                    FilterType::OR,
                    new Filter(new FilterField('id'), new IntFilterValue(10), FilterOperator::EQUAL),
                    new Filter(new FilterField('id'), new IntFilterValue(40), FilterOperator::EQUAL),
                ),
                new Filter(new FilterField('id'), new IntFilterValue(30), FilterOperator::GREATER),
            ),
        );

        $result = $this->search($c->withoutPagination());

        self::assertCount(1, $result);
    }
}
