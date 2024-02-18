<?php

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

    public function value(): string
    {
        if (\is_int($this->index)) {
            return $this->name . '->>' . $this->index;
        }

        return $this->name . '->\'' . $this->index . '\'';
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
