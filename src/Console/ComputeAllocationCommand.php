<?php

declare(strict_types=1);

namespace Test\RigStats\Console;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function PHPUnit\Framework\assertEquals;

class ComputeAllocationCommand extends Command
{
    protected static $defaultName = "compute:allocation";
    protected static $defaultDescription = "Computes layer split allocation from extraction data files.";

    protected function configure(): void
    {
        $this
            ->addArgument("inputFilename", InputArgument::REQUIRED, "Input data stored in xlsx compatible worksheet file.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('serialize_precision', 14);
        assertEquals(ini_get('serialize_precision'), 14);
        $inputFilename = $input->getArgument("inputFilename");
        if (!(is_file($inputFilename) && is_readable($inputFilename))) {
            $output->writeln("Cannot read from $inputFilename");
            return Command::INVALID;
        }
        $merge = $this->load($inputFilename);
        $errors = $this->validate($merge);
        if (count($errors)) {
            $this->outputErrors($errors, $output);
            return Command::INVALID;
        }
        $this->output($this->compute($merge), $output);
        return Command::SUCCESS;
    }

    function load(string $inputFilename): array
    {
        $inputSpreadsheet = IOFactory::load($inputFilename);
        $rates = $inputSpreadsheet->getSheetByName("rates")->toArray();
        $splits = $inputSpreadsheet->getSheetByName("splits")->toArray();
        $merge = [];
        array_shift($rates);
        foreach ($rates as $row) {
            $merge[$row[0]][intval($row[1])] = [
                'oil' => floatval($row[2]),
                'gas' => floatval($row[3]),
                'water' => floatval($row[4]),
                'splits' => [],
            ];
        }
        array_shift($splits);
        foreach ($splits as $row) {
            $merge[$row[0]][intval($row[1])]['splits'][intval($row[2])] = [
                'oil' => floatval($row[3]),
                'gas' => floatval($row[4]),
                'water' => floatval($row[5]),
            ];
        }
        return $merge;
    }

    const EPSILON = 1e-5;

    private function validate(array $merge): array
    {
        $errors = [];
        foreach ($merge as $date => $wells) {
            foreach ($wells as $well => $wellData) {
                $sums = [
                    'oil' => 0.0,
                    'gas' => 0.0,
                    'water' => 0.0,
                ];
                foreach ($wellData['splits'] as $layerData) {
                    $sums['oil'] += $layerData['oil'];
                    $sums['gas'] += $layerData['gas'];
                    $sums['water'] += $layerData['water'];
                }
                foreach ($sums as $fluid => $sum) {
                    $error = $sum - 100;
                    if (abs($error) > self::EPSILON) {
                        $errors[] = [
                            $date,
                            $well,
                            sprintf("Split data sum error by %.2f%% for $fluid", $error),
                        ];
                    }
                }
            }
        }
        if (!empty($errors)) {
            array_unshift($errors, [
                'dt',
                'well_id',
                'error',
            ]);
        }
        return $errors;
    }

    private function outputErrors(array $errors, OutputInterface $output)
    {
        $output->writeln("Input data contains errors.");
        $output->writeln("Writing errors to console:");
        foreach ($errors as $error) {
            $output->writeln(join("; ", $error));
        }
        $output->writeln("Writing errors to errors.xlsx");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('errors');
        foreach ($errors as $index => $row) {
            foreach ($row as $rowIndex => $rowData) {
                $sheet->setCellValue([$rowIndex + 1, $index + 1], $rowData);
            }
        }
        (new Xlsx($spreadsheet))->save('output/errors.xlsx');
    }

    private function compute(array $merge): array
    {
        $computed = [];
        foreach ($merge as $date => $wells) {
            foreach ($wells as $well => $wellData) {
                foreach ($wellData['splits'] as $layer => $layerData) {
                    $computed[] = [
                        'dt' => \DateTimeImmutable::createFromFormat("Y-m-d", $date),
                        'wellId' => $well,
                        'layerId' => $layer,
                        'oilRate' => $wellData['oil'] * ($layerData['oil'] / 100.0),
                        'gasRate' => $wellData['gas'] * ($layerData['gas'] / 100.0),
                        'waterRate' => $wellData['water'] * ($layerData['water'] / 100.0),
                    ];
                }
            }
        }
        return $computed;
    }

    private function output(array $computed, OutputInterface $output)
    {
        $output->writeln("Writing computation to output.xlsx");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('allocation');
        $sheet->setCellValue([1, 1], 'dt');
        $sheet->setCellValue([2, 1], 'well_id');
        $sheet->setCellValue([3, 1], 'layer_id');
        $sheet->setCellValue([4, 1], 'oil_rate');
        $sheet->setCellValue([5, 1], 'gas_rate');
        $sheet->setCellValue([6, 1], 'water_rate');
        foreach ($computed as $index => $row) {
            $index += 2;
            $sheet->setCellValue([1, $index], $row['dt']->format('Y-m-d H:i:s'));
            $sheet->setCellValue([2, $index], $row['wellId']);
            $sheet->setCellValue([3, $index], $row['layerId']);
            $sheet->setCellValue([4, $index], $row['oilRate']);
            $sheet->setCellValue([5, $index], $row['gasRate']);
            $sheet->setCellValue([6, $index], $row['waterRate']);
        }
        (new Xlsx($spreadsheet))->save('output/computation.xlsx');
        $output->writeln("Writing computation to output.json");
        file_put_contents("output/computation.json", json_encode([
            'allocation' => [
                'data' => array_map(fn (array $row) => [
                    'wellId' => $row['wellId'],
                    'dt' => $row['dt']->format('Y-m-d\TH:i:s'),
                    'layerId' => $row['layerId'],
                    'oilRate' => $row['oilRate'],
                    'gasRate' => $row['gasRate'],
                    'waterRate' => $row['waterRate'],
                ], $computed)
            ]
        ], JSON_PRETTY_PRINT));
    }
}