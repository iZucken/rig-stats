<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use InvalidArgumentException;
use RigStats\Infrastructure\Types\TypeDescriber;
use RigStats\RigModel\RateAllocation\Allocations;
use SplFixedArray;

final readonly class Extractions
{
    /**
     * @param SplFixedArray<Extraction> $extractions
     */
    public function __construct(private SplFixedArray $extractions)
    {
        $reference = $extractions[0] ?? null;
        foreach ($extractions as $extraction) {
            if (!($extraction instanceof Extraction)) {
                throw new InvalidArgumentException("Invalid collection element " . TypeDescriber::describe($extraction));
            }
            if (!$extraction->sameDimensions($reference)) {
                throw new InvalidArgumentException("Encountered incompatible generic elements");
            }
        }
    }

    public function intoAllocationDaysOrInvalidRates(): WellFluidErrors|Allocations
    {
        $errors = array_merge(...array_map(fn($record) => $record->getWellFluidErrors(), $this->extractions->toArray()));
        if (count($errors)) {
            return new WellFluidErrors(SplFixedArray::fromArray($errors, false));
        }
        return new Allocations(
            SplFixedArray::fromArray(
                array_merge(...array_map(fn($record) => $record->getAllocations(), $this->extractions->toArray())),
                false,
            )
        );
    }
}
