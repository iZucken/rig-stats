<?php

declare(strict_types=1);

namespace RigStats\RateAllocation;

use RigStats\FlatData\FlattenableList;

class InvalidDataException extends \RuntimeException
{
    public function __construct(public readonly FlattenableList $errors)
    {
        parent::__construct("Computation is not possible due to corrupted data.");
    }
}