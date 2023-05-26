<?php

declare(strict_types=1);

namespace RigStats\Fluids;

enum FluidType: string
{
    case Oil = "oil";
    case Gas = "gas";
    case Water = "water";
}
