<?php declare(strict_types=1);

namespace AdnanMula\Criteria\Tests;

use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

trait DbConnectionTrait
{
    private static string $table = 'testdata';
    private static Connection $connection;

    private static function setUpDb(): void
    {
        self::$connection = DriverManager::getConnection(self::connectionParams());
        self::createTestTable(self::$connection);
        self::createTestData(self::$connection);
    }

    private static function connectionParams(): array
    {
        /** @var array<int, string> $envFile */
        $envFile = \file(
            \dirname(__DIR__) . '/.env',
            \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES,
        );

        $variables = [];

        foreach ($envFile as $variable) {
            [$key, $value] = \explode("=", $variable, 2);
            $variables[$key] = $value;
        }

        $connectionDsn = \parse_url($variables['DATABASE_URL']);

        return [
            'dbname' => \ltrim($connectionDsn['path'] ?? '', '/'),
            'user' => $connectionDsn['user'] ?? null,
            'password' => $connectionDsn['pass'] ?? null,
            'host' => $connectionDsn['host'] ?? null,
            'port' => 5432,
            'driver' => 'pdo_pgsql',
        ];
    }

    private static function createTestTable(Connection $connection): void
    {
        $connection->executeStatement(\sprintf('drop table if exists %s', self::$table));

        $connection->executeStatement(\sprintf(
            'CREATE TABLE %s (
                id int not null,
                random_string_or_null character varying(128) null,
                always_null character varying(128) null,
                random_numbers int,
                array_of_strings jsonb null,
                primary key(id)
            )',
            self::$table,
        ));
    }

    private static function createTestData(Connection $connection): void
    {
        for ($i = 1; $i < 101; ++$i) {
            $randomString = \random_int(0, 2) === 0 ? null : \bin2hex(\random_bytes(\random_int(5, 15)));
            $json = null;

            if ($i === 1) {
                $randomString = null;
                $json = '["aaa", "bbb"]';
            }

            if ($i === 2) {
                $randomString = 'imnotrandom';
                $json = '["aaa", "bbb", "ccc"]';
            }

            if ($i === 3) {
                $randomString = 'imnotrandomtoo';
            }

            $connection->insert(self::$table, [
                'id' => $i,
                'random_string_or_null' => $randomString,
                'always_null' => null,
                'random_numbers' => \random_int(0, 10000),
                'array_of_strings' => $json,
            ]);
        }
    }

    private function search(Criteria $criteria): array
    {
        $builder = self::$connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::$table, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        return $query->executeQuery()->fetchAllAssociative();
    }
}
