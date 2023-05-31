<?php

declare(strict_types=1);

namespace RigStats\RigModel\Fluids;

final readonly class FluidSplit
{
    public FluidType $type;
    public float $value;

    public const EPSILON = 1e-5;

    public function __construct(FluidType $type, float $value)
    {
        if ($value < 0.0 || $value > 100.0) {
            throw new \InvalidArgumentException("Invalid split value $value");
        }
        $this->type = $type;
        $this->value = $value;
    }
}
