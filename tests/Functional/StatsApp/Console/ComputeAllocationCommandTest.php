<?php

namespace Functional\StatsApp\Console;

use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;
use RigStats\RigModel\Extraction\WellFluidErrors;
use RigStats\RigModel\RateAllocation\Allocations;
use RigStats\StatsApp\Console\ComputeAllocationCommand;
use RigStats\StatsApp\Serializers\AllocationsFactory;
use RigStats\StatsApp\Serializers\ExtractionsFactory;
use RigStats\StatsApp\Serializers\WellFluidErrorsFactory;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \RigStats\StatsApp\Console\ComputeAllocationCommand
 */
class ComputeAllocationCommandTest extends TestCase
{
    private CommandTester $sut;

    protected function setUp(): void
    {
        $this->sut = new CommandTester(
            new ComputeAllocationCommand(
                [
                    new AllocationsFactory(),
                    new WellFluidErrorsFactory(),
                ],
                [
                    new ExtractionsFactory(1e-5),
                ],
            )
        );
    }

    protected function tearDown(): void
    {
        // todo: see why worksheet cache is not cleared automatically; too bad its global; partially solved by using small sample files
        Settings::getCache()->clear();
    }

    public function testItFailsOnInvalidFile()
    {
        $this->sut->execute([
            'inputFilename' => 'foobar',
        ]);
        $this->assertEquals("Failed to read the input into any known container type.\n", $this->sut->getDisplay());
        $this->assertEquals(2, $this->sut->getStatusCode(), $this->sut->getDisplay());
    }

    public function testItFailsOnUnknownSpreadsheetType()
    {
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/unknownSpreadsheet.xlsx',
        ]);
        $this->assertEquals(2, $this->sut->getStatusCode(), $this->sut->getDisplay());
        $this->assertEquals("php-spreadsheet (rates) is not deserializable into any known type.\n", $this->sut->getDisplay());
    }

    public function testItRunsOnInvalidWellStructuredData()
    {
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/invalid_lite.xlsx',
        ]);
        $this->assertEquals(0, $this->sut->getStatusCode(), $this->sut->getDisplay());
        $this->assertStringContainsString("Computation complete", $this->sut->getDisplay());
    }

    public function testItFailsOnInvalidWriterOptions()
    {
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/valid_lite.xlsx',
            '--writer' => ['oops'],
        ]);
        $this->assertEquals(2, $this->sut->getStatusCode(), $this->sut->getDisplay());
        $this->assertEquals(
            "There is no `--writer` for oops; supported `--writer` options are: stdio, json, xlsx, csv.\n",
            $this->sut->getDisplay()
        );
    }

    public function testItRunsWithWarningsOnMissingWriter()
    {
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/valid_lite.xlsx',
            '--writer' => ['stdio'],
        ]);
        $this->assertEquals(2, $this->sut->getStatusCode(), $this->sut->getDisplay());
        $this->assertStringContainsString("No compatible output for", $this->sut->getDisplay());
    }

    public function testItRunsOnValidData()
    {
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/valid_lite.xlsx',
        ]);
        $this->assertEquals(0, $this->sut->getStatusCode(), $this->sut->getDisplay());
        $this->assertStringContainsString("Computation complete", $this->sut->getDisplay());
    }

    public function testItRunsOnValidDataWithCustomPath()
    {
        $tmpName = tempnam('/tmp', 'output.');
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/valid_lite.xlsx',
            'outputBasename' => $tmpName,
            '--writer' => ['xlsx'],
        ]);
        $this->assertEquals(0, $this->sut->getStatusCode(), $this->sut->getDisplay());
        $this->assertStringContainsString("Computation complete", $this->sut->getDisplay());
        $this->assertFileExists($tmpName . '.xlsx');
        unlink($tmpName . '.xlsx');
    }
}
