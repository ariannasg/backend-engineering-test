<?php declare(strict_types=1);

namespace App\Model;

use App\Exceptions\DataRateUnitException;

class DataRateUnit
{
    /**
     * @var array
     */
    private static $allUnits = [
        [
            'symbol' => 'B',
            'name' => 'byte',
            'conversion' => [
                'B' => 1,
                'Mbit' => 8.0E-6
            ],
            'threshold' => 5000000
        ],
        [
            'symbol' => 'Mbit',
            'name' => 'megabit',
            'conversion' => [
                'B' => 125000,
                'Mbit' => 1
            ],
            'threshold' => 50
        ],
    ];

    /**
     * @var string
     */
    private $unitSymbol;

    public function __construct(string $unitSymbol)
    {
        $this->unitSymbol = $unitSymbol;
    }

    /**
     * @return bool true if symbol is defined in self::$allUnits, false otherwise
     */
    public function isValid(): bool
    {
        return \in_array($this->unitSymbol, array_column(self::$allUnits, 'symbol'), false);
    }

    /**
     * Gets the factor to multiply the unit value with when converting from one unit
     * to another
     *
     * @param DataRateUnit $targetUnit
     * @return float conversion value for that unit
     * @throws DataRateUnitException
     */
    public function getConversionForTargetUnit(DataRateUnit $targetUnit): float
    {
        $targetUnitSymbol = $targetUnit->getUnitSymbol();
        $unit = $this->getUnit();

        if (isset($unit['conversion'][$targetUnitSymbol])) {
            return (float)$unit['conversion'][$targetUnitSymbol];
        }

        throw new DataRateUnitException('Conversion rate from ' . $this->unitSymbol . 'to ' . $targetUnitSymbol . 'not defined');
    }

    /**
     * @return string unit symbol
     */
    public function getUnitSymbol(): string
    {
        return $this->unitSymbol;
    }

    /**
     * Gets all unit properties
     *
     * @return array
     * @throws DataRateUnitException
     */
    private function getUnit(): array
    {
        foreach (self::$allUnits as $unit) {
            if ($unit['symbol'] === $this->unitSymbol) {
                return $unit;
            }
        }

        throw new DataRateUnitException('Unable to find unit with symbol ' . $this->unitSymbol);
    }

    /**
     * @return string pretty representation of unit name
     * @throws DataRateUnitException
     */
    public function prettify(): string
    {
        return ucfirst($this->getUnit()['name']) . 's per second';
    }

    /**
     * Gets the value from which the sub of two amounts in that unit
     * is considered a slowdown
     *
     * @return float unit threshold
     * @throws DataRateUnitException
     */
    public function getThreshold(): float
    {
        return (float)$this->getUnit()['threshold'];
    }
}