<?php

declare(strict_types=1);

namespace RigStats\RigModel\RateAllocation;

use RigStats\RigModel\Fluids\FluidRate;
use RigStats\RigModel\Rig\LayerId;

final readonly class AllocationDay
{
    public function __construct(
        public \DateTimeInterface $day,
        public LayerId $layer,
        /**
         * @var FluidRate[]
         */
        public array $rates,
    ) {
        // todo: maybe not a day...
        // todo: maybe duplicate rate types
    }
}
