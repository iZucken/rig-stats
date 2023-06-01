<?php

declare(strict_types=1);

namespace RigStats\RigModel\Fluids;

final readonly class FluidSplitRate
{
    public FluidType $type;

    public function __construct(public FluidSplit $split, public FluidRate $rate)
    {
        if ($split->type !== $rate->type) {
            throw new \InvalidArgumentException("Incompatible types $split->type and $rate->type");
        }
        $this->type = $split->type;
    }

    public function getSplitRateValue(): FluidRate
    {
        return new FluidRate($this->split->type, $this->rate->value * $this->split->value / 100.0);
    }
}
