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
    private CommandTester $sut;

    protected function setUp(): void
    {
        $this->sut = new CommandTester(new ComputeAllocationCommand);
    }

    public function testItFailsOnInvalidFile() {
        $this->sut->execute([
            'inputFilename' => 'foobar',
        ]);
        $this->assertEquals(2, $this->sut->getStatusCode(), $this->sut->getDisplay());
    }

    public function testItFailsOnUnknownSpreadsheetType() {
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/unknownSpreadsheet.xlsx',
        ]);
        $this->assertEquals(2, $this->sut->getStatusCode(), $this->sut->getDisplay());
    }

    public function testItFailsOnInvalidData() {
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/invalid.xlsx',
        ]);
        $this->assertEquals(2, $this->sut->getStatusCode(), $this->sut->getDisplay());
    }

    public function testItRunsOnValidData() {
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/valid.xlsx',
        ]);
        $this->assertEquals(0, $this->sut->getStatusCode(), $this->sut->getDisplay());
    }
}
