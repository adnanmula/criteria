<?php declare(strict_types=1);

namespace AdnanMula\Criteria;

use AdnanMula\Criteria\Filter\CompositeFilter;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FieldMapping;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\IntArrayFilterValue;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;

final class DbalCriteriaAdapter
{
    private int $parameterIndex;

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly FieldMapping $fieldMapping = new FieldMapping(),
    ) {
        $this->parameterIndex = 0;
    }

    public function execute(Criteria $criteria): void
    {
        $this->applyFilters($criteria);
        $this->applySorting($criteria);
        $this->applyPagination($criteria);
    }

    private function applyFilters(Criteria $criteria): void
    {
        $filters = $criteria->filters();

        foreach ($filters->filters() as $filter) {
            $expression = null;

            if ($filter instanceof Filter) {
                $expression = $this->buildExpression($filter);
            }

            if ($filter instanceof CompositeFilter) {
                $expression = $this->buildCompositeExpression($filter);
            }

            if ($expression === null) {
                continue;
            }

            match ($filters->filtersGlue()) {
                FilterType::AND => $this->queryBuilder->andWhere($expression),
                FilterType::OR => $this->queryBuilder->orWhere($expression),
            };
        }
    }

    private function buildCompositeExpression(CompositeFilter $compositeFilter): ?CompositeExpression
    {
        $expressions = [];

        foreach ($compositeFilter->filters() as $filter) {
            if ($filter instanceof Filter) {
                $expressions[] = $this->buildExpression($filter);
            }

            if ($filter instanceof CompositeFilter) {
                $expression = $this->buildCompositeExpression($filter);

                if (null !== $expression) {
                    $expressions[] = $expression;
                }
            }
        }

        if (0 === \count($expressions)) {
            return null;
        }

        return FilterType::OR === $compositeFilter->filtersGlue()
            ? $this->queryBuilder->expr()->or(...$expressions)
            : $this->queryBuilder->expr()->and(...$expressions);
    }

    private function applySorting(Criteria $criteria): void
    {
        if (null !== $criteria->sorting()) {
            foreach ($criteria->sorting()->order() as $order) {
                $this->queryBuilder->addOrderBy(
                    $order->field()->value($this->fieldMapping),
                    $order->type()->name,
                );
            }
        }
    }

    private function applyPagination(Criteria $criteria): void
    {
        if (null !== $criteria->offset()) {
            $this->queryBuilder->setFirstResult($criteria->offset());
        }

        if (null !== $criteria->limit()) {
            $this->queryBuilder->setMaxResults($criteria->limit());
        }
    }

    private function buildExpression(Filter $filter): string
    {
        ++$this->parameterIndex;

        $parameterName = \str_replace('.', '', $filter->field()->name()) . $this->parameterIndex;

        $this->queryBuilder->setParameter(
            $parameterName,
            $this->mapParameterValue($filter),
            $this->mapType($filter),
        );

        $field = $filter->field()->value($this->fieldMapping);
        $value = ':' . $parameterName;

        return match ($filter->operator()) {
            FilterOperator::EQUAL => $this->queryBuilder->expr()->eq($field, $value),
            FilterOperator::NOT_EQUAL => $this->queryBuilder->expr()->neq($field, $value),
            FilterOperator::GREATER => $this->queryBuilder->expr()->gt($field, $value),
            FilterOperator::GREATER_OR_EQUAL => $this->queryBuilder->expr()->gte($field, $value),
            FilterOperator::LESS => $this->queryBuilder->expr()->lt($field, $value),
            FilterOperator::LESS_OR_EQUAL => $this->queryBuilder->expr()->lte($field, $value),
            FilterOperator::CONTAINS => $this->queryBuilder->expr()->like($field, $value),
            FilterOperator::NOT_CONTAINS => $this->queryBuilder->expr()->notLike($field, $value),
            FilterOperator::CONTAINS_INSENSITIVE => $field . ' ilike ' . $value,
            FilterOperator::NOT_CONTAINS_INSENSITIVE => $field . ' not ilike ' . $value,
            FilterOperator::IN => $this->queryBuilder->expr()->in($field, $value),
            FilterOperator::NOT_IN => $this->queryBuilder->expr()->notIn($field, $value),
            FilterOperator::IS_NULL => $this->queryBuilder->expr()->isNull($field),
            FilterOperator::IS_NOT_NULL => $this->queryBuilder->expr()->isNotNull($field),
            FilterOperator::IN_ARRAY => $field . '::jsonb @> ' . $value . '::jsonb',
            FilterOperator::NOT_IN_ARRAY => 'not ' . $field . '::jsonb @> ' . $value . '::jsonb',
        };
    }

    private function mapParameterValue(Filter $filter): mixed
    {
        $containOperators = [FilterOperator::CONTAINS, FilterOperator::CONTAINS_INSENSITIVE, FilterOperator::NOT_CONTAINS, FilterOperator::NOT_CONTAINS_INSENSITIVE];

        if (in_array($filter->operator(), $containOperators, true)) {
            if (false === $filter->value() instanceof StringFilterValue) {
                throw new \InvalidArgumentException('Text search operators must use StringFilterValue');
            }

            return '%' . $filter->value()->value() . '%';
        }

        return $filter->value()->value();
    }

    private function mapType(Filter $filter): int
    {
        if (FilterOperator::IN === $filter->operator() || FilterOperator::NOT_IN === $filter->operator()) {
            if ($filter->value() instanceof IntArrayFilterValue) {
                return ArrayParameterType::INTEGER;
            }

            return ArrayParameterType::STRING;
        }

        if ($filter->operator()->isNullCheck()) {
            return ParameterType::NULL;
        }

        if ($filter->value() instanceof IntFilterValue) {
            return ParameterType::INTEGER;
        }

        return ParameterType::STRING;
    }
}
