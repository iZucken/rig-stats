<?php

declare(strict_types=1);

namespace RigStats\RigModel\Fluids;

use InvalidArgumentException;

final readonly class Rate
{
    public float $value;

    public function __construct(float $value)
    {
        if ($value < 0.0) {
            throw new InvalidArgumentException("Invalid rate value $value");
        }
        $this->value = $value;
    }
}
