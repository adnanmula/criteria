<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Tests;

use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ValidationTest extends TestCase
{
    public function testValidateOrderAsc(): void
    {
        $s = new Sorting(
            new Order(
                new FilterField('id'),
                OrderType::ASC,
            ),
            new Order(
                new FilterField('field1'),
                OrderType::DESC,
            ),
        );

        self::assertTrue($s->has('id'));
        self::assertTrue($s->has('field1'));
        self::assertFalse($s->has('field2'));

        self::assertEquals($s->get('id')?->type(), OrderType::ASC);
        self::assertEquals($s->get('field1')?->type(), OrderType::DESC);
        self::assertNull($s->get('field2'));
    }

    public static function dataInvalidPagination(): array
    {
        return [
            [10, 0],
            [4, 4],
            [-1, -1],
            [1, null],
            [0, null],
            [-1, null],
            [-10, null],
            [null, 0],
            [null, -1],
            [null, -15],
        ];
    }

    #[DataProvider('dataInvalidPagination')]
    public function testInvalidCriteria(?int $offset, ?int $limit): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Criteria(offset: $offset, limit: $limit);
    }

    public static function dataValidPagination(): array
    {
        return [
            [1, 1, true],
            [null, 1, true],
            [1, null, true],
            [0, null, true],
            [null, null, false],
            [null, 1, false],
        ];
    }

    #[DataProvider('dataValidPagination')]
    public function testValidPagination(?int $offset, ?int $limit, bool $withSorting): void
    {
        $sorting = $withSorting
            ? new Sorting(new Order(new FilterField('id'), OrderType::ASC))
            : null;

        $c = new Criteria(offset: $offset, limit: $limit, sorting: $sorting);

        self::assertEquals($offset, $c->offset());
        self::assertEquals($limit, $c->limit());
    }

    public function testPaginationWithoutSorting(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Criteria(offset: 1, limit: 4, sorting: null);
    }

    public function testTextSearchOperatorValueValidation(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Filter(
            new FilterField('random_string_or_null'),
            new IntFilterValue(4),
            FilterOperator::CONTAINS,
        );
    }

    public function testComparisonOperatorValueValidation(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Filter(
            new FilterField('random_string_or_null'),
            new ArrayElementFilterValue('a'),
            FilterOperator::EQUAL,
        );
    }

    public function testComparisonOperatorValueValidation2(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Filter(
            new FilterField('asd'),
            new StringArrayFilterValue('a', 'b'),
            FilterOperator::GREATER_OR_EQUAL,
        );
    }

    public function testCollectionOperatorValueValidation(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Filter(
            new FilterField('asd'),
            new IntFilterValue(4),
            FilterOperator::IN,
        );
    }

    public function testCollectionOperatorValueValidation2(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Filter(
            new FilterField('asd'),
            new StringFilterValue('a'),
            FilterOperator::NOT_IN,
        );
    }

    public function testJsonArrayOperatorValueValidation(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Filter(
            new FilterField('asd'),
            new StringArrayFilterValue('a', 'b'),
            FilterOperator::IN_ARRAY,
        );
    }

    public function testJsonArrayOperatorValueValidation2(): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Filter(
            new FilterField('asd'),
            new IntFilterValue(4),
            FilterOperator::NOT_IN_ARRAY,
        );
    }
}
