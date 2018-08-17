<?php declare(strict_types=1);

namespace App\Model;

class MetricsSummaryFromFileRequest
{
    /**
     * @var string
     */
    private $pathToFile;
    /**
     * @var DataRateUnit
     */
    private $inputUnit;
    /**
     * @var DataRateUnit
     */
    private $outputUnit;
    /**
     * @var array
     */
    private $metricData;

    public function __construct(string $pathToFile, string $inputUnitSymbol, string $outputUnitSymbol)
    {
        $this->pathToFile = $pathToFile;
        $this->inputUnit = new DataRateUnit($inputUnitSymbol);
        $this->outputUnit = new DataRateUnit($outputUnitSymbol);
    }

    /**
     * @return string
     */
    public function getPathToFile(): string
    {
        return $this->pathToFile;
    }

    /**
     * @return DataRateUnit
     */
    public function getInputUnit(): DataRateUnit
    {
        return $this->inputUnit;
    }

    /**
     * @return bool
     */
    public function isPathValid(): bool
    {
        return is_readable($this->pathToFile);
    }

    /**
     * @return bool
     */
    public function isInputUnitValid(): bool
    {
        return $this->inputUnit->isValid();
    }

    /**
     * @return bool
     */
    public function isOutputUnitValid(): bool
    {
        return $this->outputUnit->isValid();
    }

    /**
     * @return array
     */
    public function getMetricData(): array
    {
        return $this->metricData;
    }

    /**
     * @param array $metricData
     */
    public function setMetricData(array $metricData): void
    {
        $this->metricData = $metricData;
    }

    /**
     * Gets the factor to multiply the input unit value with when converting it to the output unit
     *
     * @return float
     * @throws \App\Exceptions\DataRateUnitException
     */
    public function getUnitsConversion(): float
    {
        return $this->inputUnit->getConversionForTargetUnit($this->getOutputUnit());
    }

    /**
     * @return DataRateUnit
     */
    public function getOutputUnit(): DataRateUnit
    {
        return $this->outputUnit;
    }
}