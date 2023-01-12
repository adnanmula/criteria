# Criteria

Useful classes to build dynamic queries, made for postgres.

## Installation

Install via [composer](https://getcomposer.org/)

```shell
composer require adnanmula/criteria
```

## Usage

```php
//Use the criteria class to build queries

$criteria = new Criteria(
    new Sorting(
        new Order(
            new FilterField('name'),
            OrderType::ASC,
        ),
        new Order(
            new FilterField('name'),
            OrderType::DESC,
        ),
    ),
    10, //Offset
    20, //Limit
    new Filters(
        FilterType::AND,
        FilterType::OR,
        new Filter(new FilterField('id'), new StringFilterValue('id'), FilterOperator::EQUAL),
        new Filter(new FilterField('field'), new StringArrayFilterValue('value1', 'value2', 'value3'), FilterOperator::IN),
        ...$moreFilters
    ),
    new Filters(
        FilterType::OR,
        FilterType::AND,
        new Filter(new FilterField('json_field'), new ArrayElementFilterValue('value'), FilterOperator::IN_ARRAY),
        new Filter(new FilterField('amount'), new IntFilterValue(3), FilterOperator::LESS_OR_EQUAL),
    ),
    ...$moreFilterGroups,
);
```

```php
//Example of repository

$query = $this->connection->createQueryBuilder()->select('a.fields')
    ->from('table', 'a');

(new DbalCriteriaAdapter($builder))->execute($criteria);

$result = $query->execute()->fetchAllAssociative();
```

