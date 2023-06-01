<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Console;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactory;
use RigStats\Infrastructure\SerializationFramework\IO\JsonToFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\PlaintextToSymfonyOutputInterfaceWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToSingleCsvFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToXlsxFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriterFactory;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;
use RigStats\Infrastructure\Types\TypeDescriber;
use RigStats\RigModel\Extraction\Extractions;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Types\ClassType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ComputeAllocationCommand extends Command
{
    public function __construct(
        private readonly SerializerFactory $serializers,
        private readonly DeserializerFactory $deserializers,
    ) {
        parent::__construct();
    }

    protected static $defaultName = "compute:allocation";
    protected static $defaultDescription = "Computes layer split allocation from extraction data files.";

    protected function configure(): void
    {
        $this
            ->addArgument(
                "inputFilename",
                InputArgument::REQUIRED,
                "Input data stored in xlsx compatible worksheet file.",
            )
            ->addArgument(
                "outputBasename",
                InputArgument::OPTIONAL,
                "Basename for output files when using file writable formats.",
                'output/compute',
            )
            ->addOption(
                "writer",
                "w",
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                "Output writers. Output data is written using every compatible writer. Supports: stdio, csv, xlsx, json.",
                [],
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // todo: ideally do something about this global state, maybe push it into serializer context
        ini_set('serialize_precision', 14);
        $sourceType = new ClassType(Extractions::class);
        $inputFilename = $input->getArgument("inputFilename");
        if (!(is_file($inputFilename) && is_readable($inputFilename))) {
            $output->writeln("Cannot read from $inputFilename.");
            return Command::INVALID;
        }
        $outputBasename = $input->getArgument("outputBasename");
        // todo: options to toggle output types
        $writers = [
            'stdio' => new PlaintextToSymfonyOutputInterfaceWriterFactory($output),
            'json' => new JsonToFileWriterFactory($outputBasename),
            'xlsx' => new SpreadsheetToXlsxFileWriterFactory($outputBasename),
            'csv' => new SpreadsheetToSingleCsvFileWriterFactory($outputBasename),
        ];
        $chosenWriters = $input->getOption("writer");
        if (count($extras = array_diff($chosenWriters, array_keys($writers)))) {
            $output->writeln(
                "There is no `--writer` for " . join(", ", $extras)
                . "; supported `--writer` options are: " . join(", ", array_keys($writers)) . "."
            );
            return Command::INVALID;
        }
        $writers = array_intersect_key($writers, array_combine($chosenWriters, $chosenWriters));
        $serialized = new PhpSpreadsheet(IOFactory::load($inputFilename));
        $deserializer = $this->deserializers->deserializable($serialized, $sourceType);
        if (!$deserializer) {
            $output->writeln("{$serialized->describe()} is not deserializable into any known type.");
            return Command::INVALID;
        }
        $loadedModel = $deserializer->deserialize();
        if (!($loadedModel instanceof Extractions)) {
            // todo: test when other data type is possible
            $output->writeln(
                "This program only supports " . Extractions::class
                . ", but got " . TypeDescriber::describe($loadedModel)
            );
            return Command::INVALID;
        }
        $computed = $loadedModel->intoAllocationDaysOrInvalidRates();
        $output->writeln("Computation complete into " . TypeDescriber::describe($computed));
        $this->writeAll($computed, $output, $writers);
        return Command::SUCCESS;
    }

    /**
     * @param mixed $data
     * @param OutputInterface $output
     * @param SerializedWriterFactory[] $writers
     * @return void
     */
    private function writeAll(
        mixed $data,
        OutputInterface $output,
        array $writers
    ) {
        foreach ($writers as $writer) {
            $atLeastOneFormat = false;
            foreach ($writer->formats() as $writerFormat) {
                if ($serializable = $this->serializers->serializable($data, $writerFormat)) {
                    $serialized = $serializable->serialize();
                    if ($writable = $writer->writable($serialized)) {
                        $output->writeln($writable->describe());
                        $writable->write();
                        $atLeastOneFormat = true;
                    }
                }
            }
            if (!$atLeastOneFormat) {
                $output->writeln(
                    "No compatible output for " . get_class($writer)
                    . " on " . TypeDescriber::describe($data)
                );
            }
        }
    }
}
