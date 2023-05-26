<?php

declare(strict_types=1);

namespace RigStats\Extraction;

use RigStats\FlatData\ArrayFlattenableList;
use RigStats\FlatData\FlattenableList;
use RigStats\Fluids\FluidRate;
use RigStats\Fluids\FluidSplit;
use RigStats\Fluids\FluidType;
use RigStats\RateAllocation\AllocationDay;
use RigStats\FlatData\FlattenableErrorsException;

class ExtractionDaySeries
{
    /**
     * @var ExtractionDay[]
     */
    private array $list = [];

    public function push(ExtractionDay $data): void
    {
        $this->list[] = $data;
    }

    public function validateSeries(): FlattenableList
    {
        $errors = [];
        foreach ($this->list as $record) {
            $sums = [];
            foreach ($record->layers as $layerData) {
                foreach ($layerData->splits as $split) {
                    if (!isset($sums[$split->type->value])) {
                        $sums[$split->type->value] = 0.0;
                    }
                    $sums[$split->type->value] += $split->value;
                }
            }
            foreach ($sums as $fluid => $sum) {
                $error = $sum - 100;
                if (abs($error) > FluidSplit::EPSILON) {
                    $errors[] = new InvalidDayRates(
                        $record->day,
                        $record->well,
                        FluidType::from($fluid),
                        sprintf("Split data sum error by %.2f%%", $error),
                    );
                }
            }
        }
        return new ArrayFlattenableList($errors);
    }

    /**
     * @return ArrayFlattenableList<AllocationDay>
     */
    public function toAllocations(): ArrayFlattenableList
    {
        $errors = $this->validateSeries();
        if ($errors->count()) {
            throw new FlattenableErrorsException($errors);
        }
        $computed = [];
        foreach ($this->list as $record) {
            foreach ($record->layers as $layer) {
                $relativeRates = [];
                foreach ($layer->splits as $split) {
                    $relatedRate = array_values(
                        array_filter($record->rates, fn($rate) => $rate->type === $split->type)
                    )[0];
                    $relativeRates[] = new FluidRate($split->type, $relatedRate->value * $split->value / 100.0);
                }
                $computed[] = new AllocationDay($record->day, $layer->layer, $relativeRates);
            }
        }
        return new ArrayFlattenableList($computed);
    }
}
