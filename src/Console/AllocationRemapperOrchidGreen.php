<?php

declare(strict_types=1);

namespace RigStats\Console;

use RigStats\FlatData\ArrayFlattenableList;
use RigStats\FlatData\DictFlattened;
use RigStats\FlatData\FlattenableList;
use RigStats\FlatRender\FlatWrapper;
use RigStats\Fluids\FluidRate;
use RigStats\RateAllocation\AllocationDay;
use RigStats\RateAllocation\AllocationDaySeries;

final class AllocationRemapperOrchidGreen implements FlatWrapper
{
    public function wrapList(FlattenableList $data): FlattenableList
    {
        if ($data instanceof AllocationDaySeries) {
            return new ArrayFlattenableList(
                array_map(fn(AllocationDay $row) => new DictFlattened(
                    array_merge(
                        [
                            'wellId' => $row->layer->well->id,
                            'dt' => $row->day->format('Y-m-d\TH:i:s'),
                            'layerId' => $row->layer->id,
                        ],
                        array_reduce(
                            $row->rates,
                            fn(array $rates, FluidRate $rate) => [
                                "{$rate->type->value}Rate" => $rate->value,
                                ...$rates
                            ],
                            []
                        )
                    )
                ), $data->all())
            );
        }
        return $data;
    }
}
