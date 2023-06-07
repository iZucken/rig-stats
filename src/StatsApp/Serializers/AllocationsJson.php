<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\RigModel\RateAllocation\Allocation;
use RigStats\RigModel\RateAllocation\Allocations;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;

/**
 * @template-implements Serializer<Allocations, Json>
 */
final readonly class AllocationsJson implements Serializer
{
    public function __construct(private Allocations $data)
    {
    }

    public function serialize(): Json
    {
        return new Json([
            'allocations' => [
                'data' => array_map(function (Allocation $row) {
                    $rates = [];
                    foreach ($row->rates as $fluid => $rate) {
                        $rates["{$fluid->value}Rate"] = $rate->value;
                    }
                    return [
                        'wellId' => $row->layer->well->id,
                        'dt' => $row->at->format('Y-m-d\TH:i:s'),
                        'layerId' => $row->layer->id,
                        ...$rates,
                    ];
                }, $this->data->allocations->toArray())
            ]
        ]);
    }
}
