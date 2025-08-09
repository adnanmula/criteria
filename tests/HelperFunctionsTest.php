<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Tests;

use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HelperFunctionsTest extends TestCase
{
    public function testOrderAsc(): void
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

    public static function dataInvalidCriteria(): array
    {
        return [
            [-1, null],
            [null, -1],
            [-1, -1],
            [10, 0],
            [1, null],
            [null, 0],
        ];
    }

    #[DataProvider('dataInvalidCriteria')]
    public function testInvalidCriteria(?int $offset, ?int $limit): void
    {
        self::expectException(\InvalidArgumentException::class);

        new Criteria($offset, $limit, null);
    }

    public static function dataValidCriteria(): array
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

    #[DataProvider('dataValidCriteria')]
    public function testValidCriteria(?int $offset, ?int $limit, bool $withSorting): void
    {
        $sorting = $withSorting
            ? new Sorting(new Order(new FilterField('id'), OrderType::ASC))
            : null;

        $c = new Criteria($offset, $limit, $sorting);

        self::assertEquals($offset, $c->offset());
        self::assertEquals($limit, $c->limit());
    }
}
