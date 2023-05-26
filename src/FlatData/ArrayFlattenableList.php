<?php

declare(strict_types=1);

namespace RigStats\FlatData;

/**
 * @template T
 */
final readonly class ArrayFlattenableList implements FlattenableList
{
    /**
     * @param T[] $content
     */
    public function __construct(private array $content)
    {
    }

    /**
     * @return array<int, T & Flattenable>
     */
    public function all(): array
    {
        return $this->content;
    }

    public function count(): int
    {
        return count($this->content);
    }
}
