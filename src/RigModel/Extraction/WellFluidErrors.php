<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use InvalidArgumentException;
use RigStats\Infrastructure\Types\TypeDescriber;
use SplFixedArray;

final readonly class WellFluidErrors
{
    /**
     * @param SplFixedArray<WellFluidError> $errors
     */
    public function __construct(public SplFixedArray $errors)
    {
        foreach ($errors as $error) {
            if (!($error instanceof WellFluidError)) {
                throw new InvalidArgumentException("Invalid collection element " . TypeDescriber::describe($error));
            }
        }
    }
}
