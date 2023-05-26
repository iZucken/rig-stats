#!/usr/bin/env php
<?php
declare(strict_types=1);

require 'vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();
$app->add(new \Test\RigStats\Console\ComputeAllocationCommand());
$app->run();
