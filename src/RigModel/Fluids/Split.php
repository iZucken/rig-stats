<?php

declare(strict_types=1);

namespace RigStats\RigModel\Fluids;

use InvalidArgumentException;

/**
 * Represents fraction of a whole in base 100
 */
final readonly class Split
{
    public float $value;

    public function __construct(float $value)
    {
        if ($value < 0.0 || $value > 100.0) {
            throw new InvalidArgumentException("Invalid split value $value");
        }
        $this->value = $value;
    }
}
