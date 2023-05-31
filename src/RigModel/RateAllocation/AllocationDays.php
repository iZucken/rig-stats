<?php

declare(strict_types=1);

namespace RigStats\RigModel\RateAllocation;

final readonly class AllocationDays
{
    /**
     * @param AllocationDay[] $days
     */
    public function __construct(public array $days)
    {
        // todo: maybe invalid day sequence
        // todo: maybe inconsistent types per day
    }
}
