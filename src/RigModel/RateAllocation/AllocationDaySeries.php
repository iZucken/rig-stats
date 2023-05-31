<?php

declare(strict_types=1);

namespace RigStats\RigModel\RateAllocation;

/**
 * @template T
 */
class AllocationDaySeries
{
    /**
     * @param AllocationDay[] $days
     */
    public function __construct(public readonly array $days)
    {
    }

    /**
     * @return array<int, T>
     */
    public function all(): array
    {
        return $this->days;
    }
}
