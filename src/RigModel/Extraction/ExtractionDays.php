<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\RigModel\RateAllocation\AllocationDays;

final readonly class ExtractionDays
{
    /**
     * @var ExtractionDay[] $list
     */
    public function __construct(private array $list)
    {
        // todo: maybe invalid day sequence
        // todo: maybe inconsistent types per day
    }

    /**
     * @throws WellFluidDayErrors
     */
    public function intoAllocationDaysOrInvalidRates(): WellFluidDayErrors|AllocationDays
    {
        $errors = array_values(array_merge(...array_map(fn($record) => $record->getInvalidRates(), $this->list)));
        if (count($errors)) {
            return new WellFluidDayErrors($errors);
        }
        return new AllocationDays(
            array_values(array_merge(...array_map(fn($record) => $record->toAllocationDays(), $this->list)))
        );
    }
}
