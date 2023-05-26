<?php

declare(strict_types=1);

namespace RigStats\FlatData;

final readonly class ArrayFlattenableList implements FlattenableList
{
    public function __construct(private array $content)
    {
    }

    public function all(): array
    {
        return $this->content;
    }

    public function count(): int
    {
        return count($this->content);
    }
}
