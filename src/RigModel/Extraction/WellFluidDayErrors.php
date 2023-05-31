<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

final readonly class WellFluidDayErrors
{
    /**
     * @param WellFluidDayError[] $errors
     */
    public function __construct(public array $errors)
    {
        // todo: maybe inconsistent types per day
    }
}
