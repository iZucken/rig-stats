<?php

declare(strict_types=1);

namespace RigStats\FlatData;

final readonly class DictFlattened implements Flattenable
{
    /**
     * @param array<string, scalar> $contained
     */
    public function __construct(public array $contained)
    {
    }

    public function flatten(): array
    {
        return $this->contained;
    }
}
