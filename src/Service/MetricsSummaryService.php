<?php declare(strict_types=1);

namespace App\Service;

use App\Exceptions\MetricsDataNotFoundException;
use App\Model\MetricsSummaryFromFileRequest;

class MetricsSummaryService
{
    /**
     * Returns a metrics summary from a file, including some basic statistics (average, min, max, median)
     * and some CTA instructions if there were any under-performing periods
     *
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return string
     * @throws MetricsDataNotFoundException
     * @throws \RuntimeException
     * @throws \App\Exceptions\DataRateUnitException
     */
    public function generateMetricsSummaryFromFile(MetricsSummaryFromFileRequest $metricsSummaryRequest): string
    {
        $fileContent = $this->getFileContent($metricsSummaryRequest->getPathToFile());
        $fileData = json_decode($fileContent, true);

        if (!$this->isFileDataValid($fileData)) {
            throw new MetricsDataNotFoundException();
        }

        $metricsSummaryRequest->setMetricData($fileData['data'][0]['metricData']);

        return $this->getSummaryStatsSection($metricsSummaryRequest) .
            $this->getSummaryCTASection($metricsSummaryRequest);
    }

    /**
     * @param string $pathToFile
     * @return string
     * @throws MetricsDataNotFoundException
     */
    private function getFileContent(string $pathToFile): string
    {
        $fileContent = file_get_contents($pathToFile);

        if ($fileContent === false) {
            throw new MetricsDataNotFoundException('Failed to get content from file');
        }

        return $fileContent;
    }

    /**
     * Checks that the file data is in the correct format
     *
     * @param array $fileData
     * @return bool true if the metricData array can be found
     */
    private function isFileDataValid(array $fileData): bool
    {
        return isset($fileData['data'][0]['metricData'])
            && !empty($fileData['data'][0]['metricData'])
            && \is_array($fileData['data'][0]['metricData']);
    }

    /**
     * Returns a short summary of the basic metrics statistics: average, min, max, median
     *
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return string
     * @throws \RuntimeException
     * @throws \App\Exceptions\DataRateUnitException
     */
    private function getSummaryStatsSection(MetricsSummaryFromFileRequest $metricsSummaryRequest): string
    {
        return $this->getSummaryOwner() . ' Metric Analyser ' . $this->getSummaryVersion() . PHP_EOL .
            '===============================' . PHP_EOL
            . PHP_EOL .
            'Period checked:' . PHP_EOL .
            PHP_EOL .
            '    From: ' . date('Y-m-d', $this->getStartDateFromMetricsSummaryRequest($metricsSummaryRequest)) . PHP_EOL .
            '    To:   ' . date('Y-m-d', $this->getEndDateFromMetricsSummaryRequest($metricsSummaryRequest)) . PHP_EOL .
            PHP_EOL .
            'Statistics:' . PHP_EOL .
            PHP_EOL .
            '    Unit: ' . $metricsSummaryRequest->getOutputUnit()->prettify() . PHP_EOL .
            PHP_EOL .
            '    Average: ' . $this->getAverageValueFromMetricsSummaryRequest($metricsSummaryRequest) . PHP_EOL .
            '    Min: ' . $this->getMinValueFromMetricsSummaryRequest($metricsSummaryRequest) . PHP_EOL .
            '    Max: ' . $this->getMaxValueFromMetricsSummaryRequest($metricsSummaryRequest) . PHP_EOL .
            '    Median: ' . $this->getMedianValueFromMetricsSummaryRequest($metricsSummaryRequest);
    }

    /**
     * @return string value of SUMMARY_OWNER env variable
     * @throws \RuntimeException
     */
    private function getSummaryOwner(): string
    {
        if (!isset($_ENV['SUMMARY_OWNER'])) {
            throw new \RuntimeException('SUMMARY_OWNER environment variable is not defined.');
        }

        return $_ENV['SUMMARY_OWNER'];
    }

    /**
     * @return string value of SUMMARY_VERSION env variable
     * @throws \RuntimeException
     */
    private function getSummaryVersion(): string
    {
        if (!isset($_ENV['SUMMARY_VERSION'])) {
            throw new \RuntimeException('SUMMARY_VERSION environment variable is not defined.');
        }

        return $_ENV['SUMMARY_VERSION'];
    }

    /**
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return int
     */
    private function getStartDateFromMetricsSummaryRequest(MetricsSummaryFromFileRequest $metricsSummaryRequest): int
    {
        return min(array_map('strtotime', array_column($metricsSummaryRequest->getMetricData(), 'dtime')));
    }

    /**
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return int
     */
    private function getEndDateFromMetricsSummaryRequest(MetricsSummaryFromFileRequest $metricsSummaryRequest): int
    {
        return max(array_map('strtotime', array_column($metricsSummaryRequest->getMetricData(), 'dtime')));
    }

    /**
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return float
     * @throws \App\Exceptions\DataRateUnitException
     */
    private function getAverageValueFromMetricsSummaryRequest(MetricsSummaryFromFileRequest $metricsSummaryRequest): float
    {
        $values = array_column($metricsSummaryRequest->getMetricData(), 'metricValue');
        $average = array_sum($values) / \count($values);

        return round($average * $metricsSummaryRequest->getUnitsConversion(), 2);
    }

    /**
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return float
     * @throws \App\Exceptions\DataRateUnitException
     */
    private function getMinValueFromMetricsSummaryRequest(MetricsSummaryFromFileRequest $metricsSummaryRequest): float
    {
        $values = array_column($metricsSummaryRequest->getMetricData(), 'metricValue');
        $min = min($values);

        return round($min * $metricsSummaryRequest->getUnitsConversion(), 2);
    }

    /**
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return float
     * @throws \App\Exceptions\DataRateUnitException
     */
    private function getMaxValueFromMetricsSummaryRequest(MetricsSummaryFromFileRequest $metricsSummaryRequest): float
    {
        $values = array_column($metricsSummaryRequest->getMetricData(), 'metricValue');
        $max = max($values);

        return round($max * $metricsSummaryRequest->getUnitsConversion(), 2);
    }

    /**
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return float
     * @throws \App\Exceptions\DataRateUnitException
     */
    private function getMedianValueFromMetricsSummaryRequest(MetricsSummaryFromFileRequest $metricsSummaryRequest): float
    {
        $values = array_column($metricsSummaryRequest->getMetricData(), 'metricValue');
        $totalValues = \count($values);

        sort($values);
        $midValue = (int)floor(($totalValues - 1) / 2);
        $median = ($totalValues % 2) ? $values[$midValue] : ($values[$midValue] + $values[$midValue + 1]) / 2;

        return round($median * $metricsSummaryRequest->getUnitsConversion(), 2);
    }

    /**
     * Returns a short summary with CTAs around the metrics. Includes:
     * - an investigate instruction for under-performing periods
     *
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return string
     * @throws \App\Exceptions\DataRateUnitException
     */
    private function getSummaryCTASection(MetricsSummaryFromFileRequest $metricsSummaryRequest): string
    {
        $info = '';
        $underperformingDates = $this->getUnderPerformingDates($metricsSummaryRequest);

        if (!empty($underperformingDates)) {
            $info .= PHP_EOL . PHP_EOL . 'Investigate:' . PHP_EOL . PHP_EOL;

            $i = 0;
            while ($i < \count($underperformingDates)) {
                if (!($i % 2)) {
                    $info .= '    * The period between ' . $underperformingDates[$i] . ' and ' .
                        $underperformingDates[$i + 1] . PHP_EOL . '      was under-performing.';
                    $i++;
                }
                $i++;
            }
            $info .= PHP_EOL;
        }

        return $info;
    }

    /**
     * Returns a list of dates representing under-performing periods in the metrics. The dates are calculated
     * taking into account the threshold of the rate unit the metrics are in.
     * The dates array should be used for getting pairs of dates representing "start" and "end" dates of the
     * under-performing period.
     *
     * Attention: this func assumes the metrics data contain sorted dates from oldest to newest
     *
     * @param MetricsSummaryFromFileRequest $metricsSummaryRequest
     * @return array dates of under-performing periods; the even indexes represent "start dates" and the odd
     * indexes represent "end dates"
     * @throws \App\Exceptions\DataRateUnitException
     */
    private function getUnderPerformingDates(MetricsSummaryFromFileRequest $metricsSummaryRequest): array
    {
        $dates = [];

        $metricsData = $metricsSummaryRequest->getMetricData();
        $metricsDataLength = \count($metricsData);
        $threshold = $metricsSummaryRequest->getInputUnit()->getThreshold();

        for ($i = 0; $i < $metricsDataLength; $i++) {
            $currentVal = $metricsData[$i]['metricValue'];
            $previousVal = ($i === 0) ? $currentVal : $metricsData[$i - 1]['metricValue'];
            $currentTime = $metricsData[$i]['dtime'];
            $previousTime = ($i === 0) ? $currentTime : $metricsData[$i - 1]['dtime'];

            if (abs($currentVal - $previousVal) > $threshold) {
                $dates[] = (\count($dates) % 2) ? $previousTime : $currentTime;
            }
        }

        return $dates;
    }
}