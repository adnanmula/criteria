<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterField;

interface FilterFieldInterface
{
    public function name(): string;
    public function value(?FieldMapping $mapping = null): string;
}
