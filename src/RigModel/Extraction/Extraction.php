<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use DateTimeInterface;
use RigStats\RigModel\Fluids\PerFluidMap;
use RigStats\RigModel\Fluids\Rate;
use RigStats\RigModel\RateAllocation\Allocation;

final readonly class Extraction
{
    /**
     * @codeCoverageIgnore
     * @param DateTimeInterface $at
     * @param PerFluidMap<ExtractionStats> $fluidStats
     * @param float $epsilon
     */
    public function __construct(
        public DateTimeInterface $at,
        public PerFluidMap $fluidStats,
        public float $epsilon,
    ) {
    }

    /**
     * @return WellFluidError[]
     */
    public function getWellFluidErrors(): array
    {
        $errors = [];
        foreach ($this->fluidStats as $fluid => $stat) {
            $sum = array_sum(array_map(fn($el) => $el->split->value, $stat->layers->toArray()));
            $error = $sum - 100;
            if (abs($error) > $this->epsilon) {
                $errors[] = new WellFluidError(
                    $this->at,
                    $stat->layers[0]->layer->well,
                    $fluid,
                    sprintf("Split data sum error by %.2f%%", $error),
                );
            }
        }
        return $errors;
    }

    /**
     * @return Allocation[]
     */
    public function getAllocations(): array
    {
        $layers = [];
        foreach ($this->fluidStats as $fluid => $stat) {
            foreach ($stat->layers as $split) {
                $layers[$split->layer->id] = $layers[$split->layer->id] ?? new Allocation(
                    $this->at,
                    $split->layer,
                    new PerFluidMap(Rate::class),
                );
                $layers[$split->layer->id]->rates->add(
                    $fluid,
                    new Rate($stat->rate->value * $split->split->value / 100.0)
                );
            }
        }
        return array_values($layers);
    }

    public function sameDimensions(Extraction $reference): bool
    {
        return $this->fluidStats->sameDimensions($reference->fluidStats)
                // todo: maybe epsilon comparison should also have an epsilon ;)
            && $this->epsilon === $reference->epsilon;
    }
}
