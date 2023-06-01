<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\Infrastructure\Types\TypeDescriber;

final readonly class WellFluidErrors
{
    /**
     * @param WellFluidError[] $errors
     */
    public function __construct(public array $errors)
    {
        foreach ($errors as $error) {
            if (!($error instanceof WellFluidError)) {
                throw new \LogicException("Invalid collection element " . TypeDescriber::describe($error));
            }
        }
    }
}
