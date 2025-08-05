# Criteria

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://www.php.net/)
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

- PHP 8.1 or higher
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
// Create a criteria object with pagination, sorting and filters
$criteria = new Criteria(
    10, // Offset
    20, // Limit
    new Sorting(
        new Order(
            new FilterField('name'),
            OrderType::ASC,
        ),
        new Order(
            new FilterField('created_at'),
            OrderType::DESC, 
        ),
    ),
    // Combine multiple filter groups with AND/OR logic
    new AndFilterGroup( // How this group connects to other groups (AND)
        FilterType::OR, // How filters within this group are combined (OR)
        new Filter(
            new FilterField('id'),
            new StringFilterValue('abc123'),
            FilterOperator::EQUAL,
        ),
        new Filter(
            new FilterField('status'),
            new StringArrayFilterValue('active', 'pending', 'review'),
            FilterOperator::IN,
        ),
        ...$moreFilters,
    ),
    new OrFilterGroup(   // How this group connects to other groups (OR)
        FilterType::AND, // How filters within this group are combined (AND)
        new Filter(
            new FilterField('json_field'),
            new ArrayElementFilterValue('value'),
            FilterOperator::IN_ARRAY,
        ),
        new Filter(
            new FilterField('amount'),
            new IntFilterValue(3),
            FilterOperator::LESS_OR_EQUAL,
        ),
    ),
);

// Copy a criteria removing pagination
$criteriaWithoutPagination = $criteria->withoutPagination();
// Copy a criteria removing pagination and sorting
$criteriaWithoutFilters = $criteria->withoutPaginationAndSorting();
// Copy a criteria removing filters
$criteriaWithoutFilters = $criteria->withoutFilters();
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
$result = $query->execute()->fetchAllAssociative();
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
