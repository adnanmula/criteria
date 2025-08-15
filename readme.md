# Criteria

[![PHP Version](https://img.shields.io/badge/PHP-%3E=8.4-777BB4.svg)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A powerful PHP library for building dynamic database queries with a fluent interface, specifically optimized for PostgreSQL.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Creating a Criteria Object](#creating-a-criteria-object)
    - [Using with Doctrine DBAL](#using-with-doctrine-dbal)
    - [Filter Operators](#filter-operators)
    - [Filter Fields](#filter-fields)
    - [Filter Values](#filter-values)
- [License](#license)

## Requirements

- PHP 8.4 or higher
- Doctrine DBAL 3.5 or higher

## Installation

Install via [Composer](https://getcomposer.org/):

```shell
composer require adnanmula/criteria
```

## Usage

### Creating a Criteria Object

The `Criteria` class is the main entry point for building queries:

```php
// Create a criteria object with filters, pagination and sorting
$criteria = new Criteria(
    filters: new Filters(
        FilterType::AND, // How top-level filters are combined
        // Simple filter
        new Filter(
            new FilterField('status'),
            new StringArrayFilterValue('active', 'pending', 'review'),
            FilterOperator::IN,
        ),
        // Filter composed of other filters
        new CompositeFilter(
            FilterType::OR, // How each expression of this composite filter is combined
            new Filter(
                new FilterField('id'),
                new StringFilterValue('abc123'),
                FilterOperator::EQUAL,
            ),
            new Filter(
                new FilterField('id'),
                new StringFilterValue('asdasd'),
                FilterOperator::EQUAL,
            ),
        ),
        // Composite filters can contain other composite filters (example: (id = 'abc123') OR (amount <= 3 AND json_field @> '["value"]')
        new CompositeFilter(
            FilterType::OR,
            new Filter(
                new FilterField('id'),
                new StringFilterValue('abc123'),
                FilterOperator::EQUAL,
            ),
            new CompositeFilter(
                FilterType::AND,
                new Filter(
                    new FilterField('amount'),
                    new IntFilterValue(3),
                    FilterOperator::LESS_OR_EQUAL,
                ),
                new Filter(
                    new FilterField('json_field'),
                    new ArrayElementFilterValue('value'),
                    FilterOperator::IN_ARRAY,
                ),
            ),
        ),
        // You can add any number of filters or composite filters
    ),
    offset: 10,
    limit: 20,
    sorting: new Sorting(
        new Order(
            new FilterField('name'),
            OrderType::ASC,
        ),
        new Order(
            new FilterField('created_at'),
            OrderType::DESC,
        ),
    ),
);

// Helper methods
// Copy a criteria adding any number of filters
$originalCriteria = new Criteria();
$newCriteria = new Criteria()->with(
    new Filter(new FilterField('otherField'), new NullFilterValue(), FilterOperator::IS_NULL),
    new CompositeFilter(
        FilterType::OR,
        new Filter(new FilterField('domainId'), new IntFilterValue(3), FilterOperator::EQUAL),
        new Filter(new FilterField('random_string_or_null'), new StringFilterValue('imnotrandom'), FilterOperator::EQUAL),
    ),
);

// Copy a criteria removing pagination
$newCriteria = $criteria->withoutPagination();
// Copy a criteria removing pagination and sorting
$newCriteria = $criteria->withoutPaginationAndSorting();
// Copy a criteria removing filters
$newCriteria = $criteria->withoutFilters();
```

### Using with Doctrine DBAL

The library integrates with Doctrine DBAL's QueryBuilder:

```php
// Get a query builder from your Doctrine DBAL connection
$builder = $this->connection->createQueryBuilder();

// Build your base query
$query = $builder->select('a.fields')
    ->from('table', 'a');

// Apply criteria to the query builder
(new DbalCriteriaAdapter($builder))->execute($criteria);

// Execute the query
$result = $query->executeQuery()->fetchAllAssociative();
```

### Filter Operators

The library supports various filter operators:

- Comparison: `EQUAL`, `NOT_EQUAL`, `GREATER`, `GREATER_OR_EQUAL`, `LESS`, `LESS_OR_EQUAL`
- Text search: `CONTAINS`, `NOT_CONTAINS`, `CONTAINS_INSENSITIVE`, `NOT_CONTAINS_INSENSITIVE`
- Collection: `IN`, `NOT_IN`
- Null checks: `IS_NULL`, `IS_NOT_NULL`
- JSON array operations: `IN_ARRAY`, `NOT_IN_ARRAY`

### Filter Fields

- `FilterField`: Standard field for most column types
- `JsonKeyFilterField`: For accessing JSON/JSONB fields with specific keys

### Filter Values

Value types for different data types:

- `StringFilterValue`: For string values
- `IntFilterValue`: For integer values
- `StringArrayFilterValue`: For arrays of strings (used with IN and NOT_IN operators)
- `IntArrayFilterValue`: For arrays of integers (used with IN and NOT_IN operators)
- `ArrayElementFilterValue`: For JSON array operations
- `NullFilterValue`: For NULL checks

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
