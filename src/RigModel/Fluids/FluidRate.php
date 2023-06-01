<?php

declare(strict_types=1);

namespace RigStats\RigModel\Fluids;

final readonly class FluidRate
{
    public FluidType $type;
    public float $value;

    public function __construct(FluidType $type, float $value)
    {
        if ($value < 0.0) {
            throw new \InvalidArgumentException("Invalid rate value $value");
        }
        $this->type = $type;
        $this->value = $value;
    }
}
