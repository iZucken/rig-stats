<?php

declare(strict_types=1);

namespace RigStats\RigModel\Fluids;

use InvalidArgumentException;
use Iterator;
use RigStats\Infrastructure\Types\TypeDescriber;

/**
 * Map of `T` over unique `FluidType`
 * @template T
 * @template-implements Iterator<FluidType, T>
 */
final class PerFluidMap implements Iterator
{
    private array $map = [];
    private int $iterated = 0;

    /**
     * @param class-string<T> $type
     */
    public function __construct(private readonly string $type)
    {
    }

    /**
     * @param T $value
     */
    public function add(FluidType $type, $value): void
    {
        if (!($value instanceof $this->type)) {
            throw new InvalidArgumentException(
                "Value must be instance of $this->type, got " . TypeDescriber::describe($value)
            );
        }
        if (isset($this->map[$type->value])) {
            throw new InvalidArgumentException("Type $type->value already registered in this collection");
        }
        $this->map[$type->value] = $value;
    }

    /**
     * @return T
     */
    public function get(FluidType $type): mixed
    {
        return $this->map[$type->value];
    }

    public function sameDimensions(PerFluidMap $ref): bool
    {
        return $this->type === $ref->type
            && array_keys($this->map) === array_keys($ref->map);
    }

    /**
     * @return FluidType[]
     */
    public function keys(): array
    {
        return array_map(FluidType::from(...), array_keys($this->map));
    }

    /**
     * @return T[]
     */
    public function values(): array
    {
        return array_values($this->map);
    }

    /**
     * @return T
     */
    public function current(): mixed
    {
        return current($this->map);
    }

    public function next(): void
    {
        $this->iterated++;
        next($this->map);
    }

    public function key(): FluidType
    {
        return FluidType::from(key($this->map));
    }

    public function valid(): bool
    {
        return $this->iterated < count($this->map);
    }

    public function rewind(): void
    {
        $this->iterated = 0;
        reset($this->map);
    }
}
