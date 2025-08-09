<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterField;

final readonly class FilterField implements FilterFieldInterface
{
    public function __construct(
        private string $name,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function value(FieldMapping $mapping = new FieldMapping()): string
    {
        return $mapping->get($this->name) ?? $this->name;
    }
}
