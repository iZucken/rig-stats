<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\Rig\WellId;

final readonly class WellFluidError
{
    public function __construct(
        public \DateTimeInterface $at,
        public WellId $well,
        public FluidType $fluid,
        public string $error,
    ) {
    }
}
