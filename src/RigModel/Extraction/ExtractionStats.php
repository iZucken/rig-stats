<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use InvalidArgumentException;
use RigStats\RigModel\Fluids\LayerSplit;
use RigStats\RigModel\Fluids\Rate;
use SplFixedArray;

final readonly class ExtractionStats
{
    /**
     * @param Rate $rate
     * @param SplFixedArray<LayerSplit> $layers
     */
    public function __construct(public Rate $rate, public SplFixedArray $layers)
    {
        $sample = $layers[0] ?? null;
        foreach ($layers as $layer) {
            if (!$layer->layer->well->equals($sample->layer->well)) {
                throw new InvalidArgumentException(
                    "Incompatible well IDs {$layer->layer->well} </> {$sample->layer->well}"
                );
            }
        }
        // todo: maybe duplicate layers
    }
}
