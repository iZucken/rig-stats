<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\RigModel\Fluids\FluidRate;
use RigStats\RigModel\Fluids\FluidSplitRate;
use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\RateAllocation\Allocation;

final readonly class Extraction
{
    public function __construct(
        public \DateTimeInterface $at,
        /**
         * @var FluidRate[]
         */
        public array $rates,
        /**
         * @var ExtractionLayer[]
         */
        public array $layers,
        public float $epsilon,
    ) {
        $types = array_reduce($rates, fn ($a, $r) => [$r->type->name => $r->type->name, ...$a], []);
        if (count($rates) - count($types) !== 0) {
            throw new \InvalidArgumentException("Unexpected duplicate rate readings.");
        }
    }

    /**
     * @return WellFluidError[]
     */
    public function getInvalidRates(): array
    {
        $sums = array_reduce(
            $this->rates,
            fn(array $sums, FluidRate $rate) => [$rate->type->value => 0.0, ...$sums],
            [],
        );
        foreach ($this->layers as $layerData) {
            foreach ($layerData->splits as $split) {
                $sums[$split->type->value] += $split->value;
            }
        }
        $errors = [];
        foreach ($sums as $fluid => $sum) {
            $error = $sum - 100;
            if (abs($error) > $this->epsilon) {
                $errors[] = new WellFluidError(
                    $this->at,
                    $this->layers[0]->layer->well,
                    FluidType::from($fluid),
                    sprintf("Split data sum error by %.2f%%", $error),
                );
            }
        }
        return $errors;
    }

    /**
     * @return Allocation[]
     */
    public function toAllocationDays(): array
    {
        $days = [];
        foreach ($this->layers as $layer) {
            $relativeRates = [];
            foreach ($layer->splits as $split) {
                $relatedRate = array_values(
                    array_filter($this->rates, fn($rate) => $rate->type === $split->type)
                )[0];
                $relativeRates[] = new FluidSplitRate($split, $relatedRate);
            }
            $days[] = new Allocation($this->at, $layer->layer, $relativeRates);
        }
        return $days;
    }

    public function comparable(Extraction $reference): bool
    {
        return empty(array_diff(
            array_map(fn ($rate) => $rate->type->name, $this->rates),
            array_map(fn ($rate) => $rate->type->name, $reference->rates),
        ));
    }
}
