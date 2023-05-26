<?php

declare(strict_types=1);

namespace RigStats\RateAllocation;

use RigStats\FlatData\ArrayFlattenableList;
use RigStats\FlatData\FlattenableList;
use RigStats\FlatData\NaiveArrayFlattenable;

final class AllocationComputer
{
    const EPSILON = 1e-5;

    public function validate(array $data): FlattenableList
    {
        $errors = [];
        foreach ($data as $date => $wells) {
            foreach ($wells as $well => $wellData) {
                $sums = [
                    'oil' => 0.0,
                    'gas' => 0.0,
                    'water' => 0.0,
                ];
                foreach ($wellData['splits'] as $layerData) {
                    $sums['oil'] += $layerData['oil'];
                    $sums['gas'] += $layerData['gas'];
                    $sums['water'] += $layerData['water'];
                }
                foreach ($sums as $fluid => $sum) {
                    $error = $sum - 100;
                    if (abs($error) > AllocationComputer::EPSILON) {
                        $errors[] = new NaiveArrayFlattenable([
                            'dt' => $date,
                            'well_id' => $well,
                            'error' => sprintf("Split data sum error by %.2f%% for $fluid", $error),
                        ]);
                    }
                }
            }
        }
        return new ArrayFlattenableList($errors);
    }

    public function compute(array $data): FlattenableList
    {
        $errors = $this->validate($data);
        if ($errors->count()) {
            throw new InvalidDataException($errors);
        }
        $computed = [];
        foreach ($data as $date => $wells) {
            foreach ($wells as $well => $wellData) {
                foreach ($wellData['splits'] as $layer => $layerData) {
                    $computed[] = new NaiveArrayFlattenable([
                        'dt' => \DateTimeImmutable::createFromFormat("Y-m-d", $date),
                        'wellId' => $well,
                        'layerId' => $layer,
                        'oilRate' => $wellData['oil'] * ($layerData['oil'] / 100.0),
                        'gasRate' => $wellData['gas'] * ($layerData['gas'] / 100.0),
                        'waterRate' => $wellData['water'] * ($layerData['water'] / 100.0),
                    ]);
                }
            }
        }
        return new ArrayFlattenableList($computed);
    }
}
