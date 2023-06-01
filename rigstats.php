#!/usr/bin/env php
<?php
declare(strict_types=1);

use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactoryCollection;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactoryCollection;
use RigStats\StatsApp\Serializers\AllocationsFactory;
use RigStats\StatsApp\Serializers\ExtractionsFactory;
use RigStats\StatsApp\Serializers\WellFluidErrorsFactory;

require 'vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();
$app->add(
    new \RigStats\StatsApp\Console\ComputeAllocationCommand(
    // note: in a bigger app these dependencies could be autowired by DI container
        new SerializerFactoryCollection([
            new AllocationsFactory(),
            new WellFluidErrorsFactory(),
        ]),
        new DeserializerFactoryCollection([
            new ExtractionsFactory(1e-5),
        ]),
    )
);
$app->run();
