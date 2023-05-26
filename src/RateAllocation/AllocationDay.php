<?php

declare(strict_types=1);

namespace RigStats\RateAllocation;

use RigStats\FlatData\Flattenable;
use RigStats\Fluids\FluidRate;
use RigStats\Rig\LayerId;

final readonly class AllocationDay implements Flattenable
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

    public function flatten(): array
    {
        return array_merge(
            [
                'dt' => $this->day->format("Y-m-d H:i:s"),
                'well_id' => $this->layer->well->id,
                'layer_id' => $this->layer->id,
            ],
            array_reduce(
                $this->rates,
                fn(array $rates, FluidRate $rate) => ["{$rate->type->value}_rate" => $rate->value, ...$rates],
                []
            ),
        );
    }
}
