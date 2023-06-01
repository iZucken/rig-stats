<?php

declare(strict_types=1);

namespace RigStats\RigModel\RateAllocation;

use RigStats\Infrastructure\Types\TypeDescriber;

final readonly class Allocations
{
    /**
     * @param Allocation[] $allocations
     */
    public function __construct(public array $allocations)
    {
        if (empty($allocations)) {
            return;
        }
        $reference = $allocations[0];
        foreach ($allocations as $allocation) {
            if (!($allocation instanceof Allocation)) {
                throw new \InvalidArgumentException("Invalid collection element " . TypeDescriber::describe($allocation));
            }
            if (!$allocation->comparable($reference)) {
                throw new \InvalidArgumentException("Encountered incompatible generic elements");
            }
        }
    }
}
