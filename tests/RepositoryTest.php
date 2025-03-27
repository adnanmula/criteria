<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Tests;

use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    use DbConnectionTrait;

    public static function setUpBeforeClass(): void
    {
        self::setUpDb();
    }

    public function testNoFilters(): void
    {
        $c = new Criteria(null, null, null);

        $result = $this->search($c);

        self::assertCount(100, $result);
    }

    public function testOrderAsc(): void
    {
        $c = new Criteria(
            null,
            null,
            new Sorting(
                new Order(
                    new FilterField('id'),
                    OrderType::ASC,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount(100, $result);

        $currentId = 1;

        foreach ($result as $row) {
            self::assertEquals($currentId, $row['id']);
            $currentId++;
        }
    }

    public function testOrderDesc(): void
    {
        $c = new Criteria(
            null,
            null,
            new Sorting(
                new Order(
                    new FilterField('id'),
                    OrderType::DESC,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount(100, $result);

        $currentId = 100;

        foreach ($result as $row) {
            self::assertEquals($currentId, $row['id']);
            $currentId--;
        }
    }

    public function testFilterEquals(): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new IntFilterValue(24), FilterOperator::EQUAL),
            ),
        );

        $result = $this->search($c);

        self::assertCount(1, $result);
        self::assertEquals(24, $result[0]['id']);
    }

    public function testFilterIsNull(): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('always_null'), new NullFilterValue(), FilterOperator::IS_NULL),
            ),
        );

        $result = $this->search($c);

        self::assertCount(100, $result);
    }

    public function testFilterIsNotNull(): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('random_string_or_null'), new NullFilterValue(), FilterOperator::IS_NOT_NULL),
            ),
        );

        $result = $this->search($c);

        self::assertGreaterThanOrEqual(1, \count($result));
    }

    public function testFilterGreaterThan(): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new IntFilterValue(50), FilterOperator::GREATER),
            ),
        );

        $result = $this->search($c);

        self::assertCount(50, $result);
    }

    public function testFilterContains(): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(
                    new FilterField('random_string_or_null'),
                    new StringFilterValue('imnotrandom'),
                    FilterOperator::CONTAINS,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount(2, $result);
    }

    public static function dataFilterNotContains(): array
    {
        return [
            ['imnotrandom', 98],
            ['imnotRANDOM', 100],
        ];
    }

    #[DataProvider('dataFilterNotContains')]
    public function testFilterNotContains(string $string, int $count): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::OR,
                new Filter(
                    new FilterField('random_string_or_null'),
                    new StringFilterValue($string),
                    FilterOperator::NOT_CONTAINS,
                ),
                new Filter(
                    new FilterField('random_string_or_null'),
                    new NullFilterValue(),
                    FilterOperator::IS_NULL,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount($count, $result);
    }

    public static function dataFilterNotContainsInsensitive(): array
    {
        return [
            ['imnotrandom', 98],
            ['imnotRANDOM', 98],
        ];
    }

    #[DataProvider('dataFilterNotContainsInsensitive')]
    public function testFilterNotContainsInsensitive(string $string, int $count): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::OR,
                new Filter(
                    new FilterField('random_string_or_null'),
                    new StringFilterValue($string),
                    FilterOperator::NOT_CONTAINS_INSENSITIVE,
                ),
                new Filter(
                    new FilterField('random_string_or_null'),
                    new NullFilterValue(),
                    FilterOperator::IS_NULL,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount($count, $result);
    }

    public function testFilterInArray(): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(
                    new FilterField('random_string_or_null'),
                    new StringArrayFilterValue('imnotrandom', 'imnotrandomtoo'),
                    FilterOperator::IN,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount(2, $result);
    }

    public function testFilterNotInArray(): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::OR,
                new Filter(
                    new FilterField('random_string_or_null'),
                    new StringArrayFilterValue('imnotrandom'),
                    FilterOperator::NOT_IN,
                ),
                new Filter(
                    new FilterField('random_string_or_null'),
                    new NullFilterValue(),
                    FilterOperator::IS_NULL,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount(99, $result);
    }

    public function testPaginationWithoutSorting(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->search(new Criteria(1, 4, null));
    }

    public function testWithoutPagination(): void
    {
        $c = new Criteria(
            1,
            4,
            new Sorting(new Order(new FilterField('id'), OrderType::ASC)),
        );

        $result = $this->search($c->withoutPagination());

        self::assertCount(100, $result);
    }

    public function testWithoutSorting(): void
    {
        $c = new Criteria(
            0,
            10,
            new Sorting(new Order(new FilterField('id'), OrderType::DESC)),
        );

        $result = $this->search($c->withoutPaginationAndSorting());

        self::assertCount(100, $result);
        self::assertEquals(1, $result[0]['id']);
    }

    public function testWithoutFilters(): void
    {
        $c = new Criteria(
            0,
            10,
            new Sorting(new Order(new FilterField('id'), OrderType::DESC)),
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new IntFilterValue(10), FilterOperator::EQUAL),
            ),
        );

        $result = $this->search($c->withoutFilters());

        self::assertCount(10, $result);
        self::assertEquals(100, $result[0]['id']);
    }

    public function testAddOneMoreFilter(): void
    {
        $c = new Criteria(null, null, null);

        $c = $c->with(
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new IntFilterValue(10), FilterOperator::EQUAL),
            ),
        );

        $result = $this->search($c->withoutPagination());

        self::assertCount(1, $result);
    }

    public function testAddOneTwoFilters(): void
    {
        $c = new Criteria(null, null, null);

        $c = $c->with(
            new AndFilterGroup(
                FilterType::OR,
                new Filter(new FilterField('id'), new IntFilterValue(10), FilterOperator::EQUAL),
                new Filter(new FilterField('id'), new IntFilterValue(40), FilterOperator::EQUAL),
            ),
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new IntFilterValue(30), FilterOperator::GREATER),
            ),
        );

        $result = $this->search($c->withoutPagination());

        self::assertCount(1, $result);
    }

    public static function dataFilterInJsonArray(): array
    {
        return [
            ['aaa', 2],
            ['bbb', 2],
            ['ccc', 1],
            ['ddd', 0],
        ];
    }

    #[DataProvider('dataFilterInJsonArray')]
    public function testFilterInJsonArray(string $key, int $expected): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(
                    new FilterField('array_of_strings'),
                    new ArrayElementFilterValue($key),
                    FilterOperator::IN_ARRAY,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount($expected, $result);
    }

    public static function dataFilterNotInJsonArray(): array
    {
        return [
            ['aaa', 0],
            ['bbb', 0],
            ['ccc', 1],
            ['ddd', 2],
        ];
    }

    #[DataProvider('dataFilterNotInJsonArray')]
    public function testFilterNotInJsonArray(string $key, int $expected): void
    {
        $c = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(
                    new FilterField('array_of_strings'),
                    new ArrayElementFilterValue($key),
                    FilterOperator::NOT_IN_ARRAY,
                ),
            ),
        );

        $result = $this->search($c);

        self::assertCount($expected, $result);
    }
}
