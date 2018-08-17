<?php declare(strict_types=1);

namespace App\Command;

use App\Exceptions\DataRateUnitException;
use App\Exceptions\MetricsDataNotFoundException;
use App\Model\MetricsSummaryFromFileRequest;
use App\Service\MetricsSummaryService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AppAnalyseMetricsCommand
 *
 * @package App\Command
 */
class AppAnalyseMetricsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:analyse-metrics';

    /**
     * @var MetricsSummaryService
     */
    private $metricsSummaryService;

    public function __construct(MetricsSummaryService $metricsSummaryService)
    {
        parent::__construct(self::$defaultName);
        $this->metricsSummaryService = $metricsSummaryService;
    }

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Analyses the file\'s metrics to generate a report')
            ->addOption(
                'path-to-file',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the file containing the metrics to analyse',
                'resources/fixtures/1.json')
            ->addOption(
                'input-unit-symbol',
                null,
                InputOption::VALUE_REQUIRED,
                'The symbol representing the unit of the metrics in the file - i.e: B, Mbit, etc',
                'B')
            ->addOption(
                'output-unit-symbol',
                null,
                InputOption::VALUE_REQUIRED,
                'The symbol representing the desired unit of the metrics summary output - i.e: B, Mbit, etc',
                'Mbit');
    }

    /**
     * Detect slow-downs in the data and output them to stdout.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $metricsSummaryRequest = new MetricsSummaryFromFileRequest(
            $input->getOption('path-to-file'),
            $input->getOption('input-unit-symbol'),
            $input->getOption('output-unit-symbol')
        );

        if (!$metricsSummaryRequest->isPathValid()) {
            $output->writeln('Invalid path to metrics file. Please make sure this directory exists and has readable permissions.');
        }

        if (!$metricsSummaryRequest->isInputUnitValid() || !$metricsSummaryRequest->isOutputUnitValid()) {
            $output->writeln('Invalid units. Please make sure the unit symbols for both input and output metrics are correct.');
        }

        try {
            $output->writeln($this->metricsSummaryService->generateMetricsSummaryFromFile($metricsSummaryRequest));
        } catch (MetricsDataNotFoundException $e) {
            $output->writeln($e->getMessage());
        } catch (\RuntimeException $e) {
            $output->writeln($e->getMessage());
        } catch (DataRateUnitException $e) {
            $output->writeln($e->getMessage());
        } catch (\Exception $e) {
            $output->writeln('Unknown error when trying to generate the metrics summary.');
        }
    }
}