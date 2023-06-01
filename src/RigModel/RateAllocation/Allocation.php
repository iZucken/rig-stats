<?php

declare(strict_types=1);

namespace RigStats\RigModel\RateAllocation;

use RigStats\RigModel\Fluids\FluidSplitRate;
use RigStats\RigModel\Rig\LayerId;

final readonly class Allocation
{
    public function __construct(
        public \DateTimeInterface $at,
        public LayerId $layer,
        /**
         * @var FluidSplitRate[]
         */
        public array $rates,
    ) {
        $types = array_reduce($rates, fn ($a, $r) => [$r->type->name => $r->type->name, ...$a], []);
        if (count($rates) - count($types) !== 0) {
            throw new \InvalidArgumentException("Unexpected duplicate rate readings.");
        }
    }

    public function comparable(Allocation $reference): bool
    {
        return empty(array_diff(
            array_map(fn ($rate) => $rate->type->name, $this->rates),
            array_map(fn ($rate) => $rate->type->name, $reference->rates),
        ));
    }
}
