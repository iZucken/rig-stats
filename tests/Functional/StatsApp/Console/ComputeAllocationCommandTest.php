<?php

namespace Functional\StatsApp\Console;

use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;
use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactoryCollection;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactoryCollection;
use RigStats\StatsApp\Console\ComputeAllocationCommand;
use RigStats\StatsApp\Serializers\AllocationSeriesFactory;
use RigStats\StatsApp\Serializers\ExtractionDaySeriesFactory;
use RigStats\StatsApp\Serializers\InvalidDayRatesMultiFactory;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \RigStats\StatsApp\Console\ComputeAllocationCommand
 */
class ComputeAllocationCommandTest extends TestCase
{
    private CommandTester $sut;

    protected function setUp(): void
    {
        $this->sut = new CommandTester(new ComputeAllocationCommand(
            new SerializerFactoryCollection([
                new AllocationSeriesFactory(),
                new InvalidDayRatesMultiFactory(),
            ]),
            new DeserializerFactoryCollection([
                new ExtractionDaySeriesFactory(),
            ]),
        ));
    }

    protected function tearDown(): void
    {
        // todo: see why worksheet cache is not cleared automatically; too bad its global
        Settings::getCache()->clear();
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

    public function testItRunsOnValidDataWithCustomPath() {
        $tmpName = tempnam('/tmp', 'output.');
        $this->sut->execute([
            'inputFilename' => __DIR__ . '/../../../examples/rates_splits/valid.xlsx',
            'outputBasename' => $tmpName,
        ]);
        $this->assertEquals(0, $this->sut->getStatusCode(), $this->sut->getDisplay());
        $this->assertFileExists($tmpName . '.xlsx');
        unlink($tmpName . '.xlsx');
    }
}
