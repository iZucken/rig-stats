<?php

declare(strict_types=1);

namespace RigStats\FlatData;

final readonly class NaiveArrayFlattenable implements Flattenable
{
    public function __construct(private array $data)
    {
    }

    public function flatten(): array
    {
        return $this->data;
    }
}