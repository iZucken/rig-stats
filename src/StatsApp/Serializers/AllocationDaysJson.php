<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\RigModel\Fluids\FluidRate;
use RigStats\RigModel\RateAllocation\AllocationDay;
use RigStats\RigModel\RateAllocation\AllocationDays;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;

final readonly class AllocationDaysJson implements Serializer
{
    public function __construct(private AllocationDays $data)
    {
    }

    public function serialize(): Json
    {
        return new Json([
            'allocations' => [
                'data' => array_map(fn(AllocationDay $row) =>
                array_merge(
                    [
                        'wellId' => $row->layer->well->id,
                        'dt' => $row->day->format('Y-m-d\TH:i:s'),
                        'layerId' => $row->layer->id,
                    ],
                    array_reduce(
                        $row->rates,
                        fn(array $rates, FluidRate $rate) => [
                            ...$rates,
                            "{$rate->type->value}Rate" => $rate->value,
                        ],
                        []
                    )
                ), $this->data->days)
            ]
        ]);
    }
}
