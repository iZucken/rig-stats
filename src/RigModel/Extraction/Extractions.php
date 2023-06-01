<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\Infrastructure\Types\TypeDescriber;
use RigStats\RigModel\RateAllocation\Allocations;

final readonly class Extractions
{
    /**
     * @var Extraction[] $extractions
     */
    public function __construct(private array $extractions)
    {
        if (empty($extractions)) {
            return;
        }
        $reference = $extractions[0];
        foreach ($extractions as $extraction) {
            if (!($extraction instanceof Extraction)) {
                throw new \InvalidArgumentException("Invalid collection element " . TypeDescriber::describe($extraction));
            }
            if (!$extraction->comparable($reference)) {
                throw new \InvalidArgumentException("Encountered incompatible generic elements");
            }
        }
    }

    /**
     * @throws WellFluidErrors
     */
    public function intoAllocationDaysOrInvalidRates(): WellFluidErrors|Allocations
    {
        $errors = array_values(array_merge(...array_map(fn($record) => $record->getInvalidRates(), $this->extractions)));
        if (count($errors)) {
            return new WellFluidErrors($errors);
        }
        return new Allocations(
            array_values(array_merge(...array_map(fn($record) => $record->toAllocationDays(), $this->extractions)))
        );
    }
}
