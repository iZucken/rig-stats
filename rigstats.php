#!/usr/bin/env php
<?php
declare(strict_types=1);

use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactoryCollection;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactoryCollection;
use RigStats\StatsApp\Serializers\AllocationDaysFactory;
use RigStats\StatsApp\Serializers\ExtractionDaysFactory;
use RigStats\StatsApp\Serializers\WellFluidDayErrorsFactory;

require 'vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();
$app->add(
    new \RigStats\StatsApp\Console\ComputeAllocationCommand(
    // note: in a bigger app these dependencies could be autowired by DI container
        new SerializerFactoryCollection([
            new AllocationDaysFactory(),
            new WellFluidDayErrorsFactory(),
        ]),
        new DeserializerFactoryCollection([
            new ExtractionDaysFactory(1e-5),
        ]),
    )
);
$app->run();
