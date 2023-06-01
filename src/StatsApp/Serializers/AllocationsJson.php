<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\RigModel\Fluids\FluidSplitRate;
use RigStats\RigModel\RateAllocation\Allocation;
use RigStats\RigModel\RateAllocation\Allocations;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;

final readonly class AllocationsJson implements Serializer
{
    public function __construct(private Allocations $data)
    {
    }

    public function serialize(): Json
    {
        return new Json([
            'allocations' => [
                'data' => array_map(fn(Allocation $row) =>
                array_merge(
                    [
                        'wellId' => $row->layer->well->id,
                        'dt' => $row->at->format('Y-m-d\TH:i:s'),
                        'layerId' => $row->layer->id,
                    ],
                    array_reduce(
                        $row->rates,
                        fn(array $rates, FluidSplitRate $rate) => [
                            ...$rates,
                            "{$rate->split->type->value}Rate" => $rate->getSplitRateValue()->value,
                        ],
                        []
                    )
                ), $this->data->allocations)
            ]
        ]);
    }
}
