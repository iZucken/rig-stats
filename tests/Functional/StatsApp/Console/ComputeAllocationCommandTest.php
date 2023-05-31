<?php

namespace Functional\StatsApp\Console;

use PHPUnit\Framework\TestCase;
use RigStats\StatsApp\Console\ComputeAllocationCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \RigStats\StatsApp\Console\ComputeAllocationCommand
 */
class ComputeAllocationCommandTest extends TestCase
{
    public function testItFailsOnInvalidData() {
        $tester = new CommandTester(new ComputeAllocationCommand);
        $tester->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/invalid.xlsx',
        ]);
        $this->assertEquals(2, $tester->getStatusCode(), $tester->getDisplay());
    }

    public function testItRunsOnValidData() {
        $tester = new CommandTester(new ComputeAllocationCommand);
        $tester->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/valid.xlsx',
        ]);
        $this->assertEquals(0, $tester->getStatusCode(), $tester->getDisplay());
    }
}
