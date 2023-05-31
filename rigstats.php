#!/usr/bin/env php
<?php
declare(strict_types=1);

use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactoryCollection;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactoryCollection;
use RigStats\StatsApp\Serializers\AllocationSeriesFactory;
use RigStats\StatsApp\Serializers\ExtractionDaySeriesFactory;
use RigStats\StatsApp\Serializers\InvalidDayRatesMultiFactory;

require 'vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();
$app->add(
    new \RigStats\StatsApp\Console\ComputeAllocationCommand(
    // note: in a bigger app these dependencies could be autowired by DI container
        new SerializerFactoryCollection([
            new AllocationSeriesFactory(),
            new InvalidDayRatesMultiFactory(),
        ]),
        new DeserializerFactoryCollection([
            new ExtractionDaySeriesFactory(),
        ]),
    )
);
$app->run();
