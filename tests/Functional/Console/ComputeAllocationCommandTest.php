<?php

namespace Functional\Console;

use PHPUnit\Framework\TestCase;
use RigStats\Console\ComputeAllocationCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \RigStats\Console\ComputeAllocationCommand
 */
class ComputeAllocationCommandTest extends TestCase
{
    public function testItRunsOnValidData() {
        $tester = new CommandTester(new ComputeAllocationCommand);
        $tester->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/valid.xlsx',
        ]);
        $this->assertEquals(0, $tester->getStatusCode());
    }

    public function testItFailsOnInvalidData() {
        $tester = new CommandTester(new ComputeAllocationCommand);
        $tester->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/invalid.xlsx',
        ]);
        $this->assertEquals(2, $tester->getStatusCode());
    }
}
