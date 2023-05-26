<?php

declare(strict_types=1);

namespace RigStats\Extraction;

use RigStats\FlatData\ArrayFlattenableList;
use RigStats\RateAllocation\AllocationDaySeries;

final class ExtractionDaySeries
{
    /**
     * @var ExtractionDay[] $list
     */
    public function __construct(private readonly array $list)
    {
        // todo: maybe invalid day sequence
        // todo: maybe inconsistent types per day
    }

    public function toAllocations(): AllocationDaySeries
    {
        $errors = array_filter(array_map(fn($record) => $record->validate(), $this->list));
        if (count($errors)) {
            throw new ExtractionDataCorruptionException(new ArrayFlattenableList($errors));
        }
        return new AllocationDaySeries(
            array_merge(...array_map(fn($record) => $record->toAllocationDays(), $this->list))
        );
    }
}
