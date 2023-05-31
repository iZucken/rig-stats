<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\Rig\WellId;

final readonly class InvalidDayRates
{
    public function __construct(
        public \DateTimeInterface $day,
        public WellId $well,
        public FluidType $fluid,
        public string $error,
    ) {
    }
}
