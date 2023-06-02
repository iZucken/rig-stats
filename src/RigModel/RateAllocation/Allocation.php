<?php

declare(strict_types=1);

namespace RigStats\RigModel\RateAllocation;

use DateTimeInterface;
use RigStats\RigModel\Fluids\PerFluidMap;
use RigStats\RigModel\Fluids\Rate;
use RigStats\RigModel\Rig\LayerId;

final readonly class Allocation
{
    /**
     * @param DateTimeInterface $at
     * @param LayerId $layer
     * @param PerFluidMap<Rate> $rates
     */
    public function __construct(
        public DateTimeInterface $at,
        public LayerId $layer,
        public PerFluidMap $rates,
    ) {
    }

    public function sameDimensions(Allocation $ref): bool
    {
        return $this->rates->sameDimensions($ref->rates);
    }
}
