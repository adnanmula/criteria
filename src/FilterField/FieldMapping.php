<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterField;

final readonly class FieldMapping
{
    /** @param array<string, string> $mappings */
    public function __construct(
        private array $mappings = [],
    ) {}

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->mappings);
    }

    public function get(string $name): ?string
    {
        return $this->mappings[$name] ?? null;
    }
}
