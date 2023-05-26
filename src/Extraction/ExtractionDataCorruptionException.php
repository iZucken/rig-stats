<?php

declare(strict_types=1);

namespace RigStats\Extraction;

use RigStats\FlatData\FlattenableList;

final class ExtractionDataCorruptionException extends \RuntimeException
{
    public function __construct(public readonly FlattenableList $errors)
    {
        parent::__construct("Computation is not possible due to corrupted data.");
    }
}