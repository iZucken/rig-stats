<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\RigModel\Fluids\FluidRate;
use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\RateAllocation\AllocationDay;

final readonly class ExtractionDay
{
    public function __construct(
        public \DateTimeInterface $day,
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
        // todo: maybe not a day...
        // todo: maybe duplicate rate types
        // todo: maybe invalid type intersection
    }

    /**
     * @return WellFluidDayError[]
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
                $errors[] = new WellFluidDayError(
                    $this->day,
                    $this->layers[0]->layer->well,
                    FluidType::from($fluid),
                    sprintf("Split data sum error by %.2f%%", $error),
                );
            }
        }
        return $errors;
    }

    /**
     * @return AllocationDay[]
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
                $relativeRates[] = new FluidRate($split->type, $relatedRate->value * $split->value / 100.0);
            }
            $days[] = new AllocationDay($this->day, $layer->layer, $relativeRates);
        }
        return $days;
    }
}
