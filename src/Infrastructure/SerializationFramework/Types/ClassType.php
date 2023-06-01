<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Types;

/**
 * @template-extends Type<class-string>
 */
final readonly class ClassType implements Type
{
    /**
     * @param class-string $className
     */
    public function __construct(public string $className)
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Undefined $className");
        }
    }

    public function equals(Type $other): bool
    {
        if ($other instanceof ClassType) {
            return $this->className === $other->className;
        }
        return false;
    }

    public function describe(): string
    {
        return "class $this->className";
    }
}
