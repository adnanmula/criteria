<?php declare(strict_types=1);

namespace AdnanMula\Criteria\FilterField;

final readonly class JsonKeyFilterField implements FilterFieldInterface
{
    public function __construct(
        private string $name,
        private int|string $index,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function value(?FieldMapping $mapping = null): string
    {
        $name = $this->name;

        if (null !== $mapping && $mapping->has($name)) {
            /** @var string $name */
            $name = $mapping->get($name);
        }

        if (\is_string($this->index)) {
            return $name . '->>\'' . $this->index . '\'';
        }

        return $name . '->>' . $this->index;
    }
}
