<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

final class ExtractionDataCorruptionException extends \RuntimeException
{
    /**
     * @param InvalidDayRates[] $errors
     */
    public function __construct(public readonly array $errors)
    {
        parent::__construct("Computation is not possible due to corrupted data.");
    }
}