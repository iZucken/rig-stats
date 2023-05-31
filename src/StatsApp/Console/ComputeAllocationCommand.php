<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Console;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RigStats\Infrastructure\SerializationFramework\IO\JsonToFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\PlaintextToSymfonyOutputInterfaceWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToSingleCsvFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToXlsxFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriterFactory;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;
use RigStats\RigModel\Extraction\ExtractionDaySeries;
use RigStats\RigModel\Extraction\ExtractionDataCorruptionException;
use RigStats\Infrastructure\SerializationFramework\Deserialization\PlainDeserializerCollection;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Types\ClassType;
use RigStats\StatsApp\Serializers\AllocationSeriesFactory;
use RigStats\StatsApp\Serializers\ExtractionDaySeriesFactory;
use RigStats\StatsApp\Serializers\InvalidDayRatesMultiFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ComputeAllocationCommand extends Command
{
    protected static $defaultName = "compute:allocation";
    protected static $defaultDescription = "Computes layer split allocation from extraction data files.";

    protected function configure(): void
    {
        $this
            // todo: options to set output file
            ->addArgument(
                "inputFilename",
                InputArgument::REQUIRED,
                "Input data stored in xlsx compatible worksheet file."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // todo: ideally do something about this global state, maybe push it into serializer context
        ini_set('serialize_precision', 14);
        $sourceType = new ClassType(ExtractionDaySeries::class);
        $inputFilename = $input->getArgument("inputFilename");
        if (!(is_file($inputFilename) && is_readable($inputFilename))) {
            $output->writeln("Cannot read from $inputFilename");
            return Command::INVALID;
        }
        $deserializerProbes = new PlainDeserializerCollection([
            new ExtractionDaySeriesFactory
        ]);
        // todo: options to toggle output types
        $serializers = [
            new AllocationSeriesFactory,
            new InvalidDayRatesMultiFactory,
        ];
        $outputBasename = 'output/computation';
        $outputWriters = [
            'json' => new JsonToFileWriterFactory($outputBasename),
            'xlsx' => new SpreadsheetToXlsxFileWriterFactory($outputBasename),
            'csv' => new SpreadsheetToSingleCsvFileWriterFactory($outputBasename),
        ];
        $errorOutputBasename = 'output/computation';
        $errorWriters = [
            'stdio' => new PlaintextToSymfonyOutputInterfaceWriterFactory($output),
            'xlsx' => new SpreadsheetToXlsxFileWriterFactory($errorOutputBasename),
        ];
        $serialized = new PhpSpreadsheet(IOFactory::load($inputFilename));
        $deserializer = $deserializerProbes->deserializable($serialized, $sourceType);
        if (!$deserializer) {
            $output->writeln("{$serialized->describe()} is not deserializable into any known type.");
            return Command::INVALID;
        }
        $loadedModel = $deserializer->deserialize();
        if (!($loadedModel instanceof ExtractionDaySeries)) {
            // todo: test when other data type is possible
            $output->writeln("This program only supports " . ExtractionDaySeries::class . ", but got " . gettype($loadedModel));
            return Command::INVALID;
        }
        try {
            $compute = $loadedModel->toAllocations();
            $output->writeln("Computation complete.");
            $this->writeAll($compute, $output, $serializers, $outputWriters);
            return Command::SUCCESS;
        } catch (ExtractionDataCorruptionException $exception) {
            $output->writeln("Input data contains errors.");
            $this->writeAll($exception, $output, $serializers, $errorWriters);
            return Command::INVALID;
        }
    }

    /**
     * @param mixed $data
     * @param OutputInterface $output
     * @param SerializerFactory[] $serializers
     * @param SerializedWriterFactory[] $writers
     * @return void
     */
    private function writeAll(mixed $data, OutputInterface $output, array $serializers, array $writers)
    {
        foreach ($serializers as $serializerProbe) {
            foreach ($writers as $writer) {
                foreach ($writer->formats() as $writerFormat) {
                    if ($serializable = $serializerProbe->serializable($data, $writerFormat)) {
                        $serialized = $serializable->serialize();
                        if ($writable = $writer->writable($serialized)) {
                            $output->writeln($writable->describe());
                            $writable->write();
                        }
                    }
                }
            }
        }
    }
}
