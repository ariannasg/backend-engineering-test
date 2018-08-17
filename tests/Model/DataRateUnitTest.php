<?php declare(strict_types=1);

namespace App\Tests\Model;

use App\Exceptions\DataRateUnitException;
use App\Model\DataRateUnit;
use PHPUnit\Framework\TestCase;

class DataRateUnitTest extends TestCase
{
    /**
     * @var DataRateUnit
     */
    protected $bytesUnit;
    /**
     * @var DataRateUnit
     */
    protected $kilobytesUnit;

    protected function setUp(): void
    {
        $this->bytesUnit = new DataRateUnit('B');
        $this->kilobytesUnit = new DataRateUnit('kB');
    }

    public function testIsValidReturnsTrueForSetUnit(): void
    {
        $this->assertTrue($this->bytesUnit->isValid());
    }

    public function testIsValidReturnsFalseForUnsetUnit(): void
    {
        $this->assertFalse($this->kilobytesUnit->isValid());
    }

    public function testConversionForTargetUnitReturnedForSetUnits(): void
    {
        $targetUnit = new DataRateUnit('Mbit');
        $this->assertEquals(8.0E-6, $this->bytesUnit->getConversionForTargetUnit($targetUnit));
    }

    public function testConversionForTargetUnitThrowsErrorForUnsetUnits(): void
    {
        $this->expectException(DataRateUnitException::class);
        $this->expectExceptionMessage('Unable to find unit with symbol kB');

        $this->kilobytesUnit->getConversionForTargetUnit($this->kilobytesUnit);
    }

    public function testThresholdReturnsValueForSetUnits(): void
    {
        $this->assertEquals((float)5000000, $this->bytesUnit->getThreshold());
    }

    public function testThresholdThrowsErrorForUnsetUnits(): void
    {
        $this->expectException(DataRateUnitException::class);
        $this->expectExceptionMessage('Unable to find unit with symbol kB');

        $this->kilobytesUnit->getThreshold($this->kilobytesUnit);
    }
}