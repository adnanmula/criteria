<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterField;

class JsonKeyFilterField implements FilterFieldInterface
{
    public function __construct(
        private string $name,
        private readonly int|string $index,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function index(): int|string
    {
        return $this->index;
    }
}
