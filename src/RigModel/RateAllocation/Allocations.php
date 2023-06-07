<?php

declare(strict_types=1);

namespace RigStats\RigModel\RateAllocation;

use InvalidArgumentException;
use RigStats\Infrastructure\Types\TypeDescriber;
use SplFixedArray;

final readonly class Allocations
{
    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param SplFixedArray<Allocation> $allocations
     */
    public function __construct(public SplFixedArray $allocations)
    {
        if (count($allocations) === 0) {
            return;
        }
        $reference = $allocations[0];
        foreach ($allocations as $allocation) {
            if (!($allocation instanceof Allocation)) {
                throw new InvalidArgumentException("Invalid collection element " . TypeDescriber::describe($allocation));
            }
            if (!$allocation->sameDimensions($reference)) {
                throw new InvalidArgumentException("Encountered incompatible generic elements");
            }
        }
    }
}
