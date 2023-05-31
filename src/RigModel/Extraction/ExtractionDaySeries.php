<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\RigModel\RateAllocation\AllocationDaySeries;

final readonly class ExtractionDaySeries
{
    /**
     * @var ExtractionDay[] $list
     */
    public function __construct(private array $list)
    {
        // todo: maybe invalid day sequence
        // todo: maybe inconsistent types per day
    }

    public function toAllocations(): AllocationDaySeries
    {
        $errors = array_filter(array_map(fn($record) => $record->validate(), $this->list));
        if (count($errors)) {
            throw new ExtractionDataCorruptionException($errors);
        }
        return new AllocationDaySeries(
            array_values(array_merge(...array_map(fn($record) => $record->toAllocationDays(), $this->list)))
        );
    }
}
