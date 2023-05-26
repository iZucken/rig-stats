<?php

declare(strict_types=1);

namespace RigStats\RateAllocation;

use RigStats\FlatData\Flattenable;
use RigStats\FlatData\FlattenableList;

/**
 * @template T
 */
class AllocationDaySeries implements FlattenableList
{
    /**
     * @param AllocationDay[] $days
     */
    public function __construct(public readonly array $days)
    {
    }

    /**
     * @return array<int, T & Flattenable>
     */
    public function all(): array
    {
        return $this->days;
    }
}
