<?php

declare(strict_types=1);

namespace RigStats\Console;

use RigStats\FlatData\Flattenable;
use RigStats\FlatData\FlattenableList;
use RigStats\FlatRender\FlatWrapper;

final class AllocationJsonDataWrapper implements FlatWrapper
{
    public function wrapList(FlattenableList $data): array
    {
        // todo: faulty design
        return [
            'allocation' => [
                'data' => array_map(fn(array $row) => [
                    'wellId' => $row['wellId'],
                    'dt' => $row['dt']->format('Y-m-d\TH:i:s'),
                    'layerId' => $row['layerId'],
                    'oilRate' => $row['oilRate'],
                    'gasRate' => $row['gasRate'],
                    'waterRate' => $row['waterRate'],
                ], array_map(fn (Flattenable $row) => $row->flatten(), $data->all()))
            ]
        ];
    }
}
