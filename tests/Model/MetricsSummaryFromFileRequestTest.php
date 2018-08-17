<?php declare(strict_types=1);

namespace App\Tests\Model;

use App\Exceptions\DataRateUnitException;
use App\Model\MetricsSummaryFromFileRequest;
use PHPUnit\Framework\TestCase;

class MetricsSummaryFromFileRequestTest extends TestCase
{
    /**
     * @var MetricsSummaryFromFileRequest
     */
    protected $file1Req;
    /**
     * @var MetricsSummaryFromFileRequest
     */
    protected $nonExistingFileReq;
    /**
     * @var MetricsSummaryFromFileRequest
     */
    protected $wrongUnitsReq;

    protected function setUp(): void
    {
        $this->file1Req = new MetricsSummaryFromFileRequest(
            'resources/fixtures/1.json',
            'B',
            'Mbit');

        $this->nonExistingFileReq = new MetricsSummaryFromFileRequest(
            'resources/fixtures/3.json',
            'B',
            'Mbit');

        $this->wrongUnitsReq = new MetricsSummaryFromFileRequest(
            'resources/fixtures/2.json',
            'kBit',
            'Gbit');
    }

    public function testGetPathReturnedForExistingFiles(): void
    {
        $this->assertEquals('resources/fixtures/1.json', $this->file1Req->getPathToFile());
    }

    public function testGetPathReturnedForNonExistingFiles(): void
    {
        $this->assertEquals('resources/fixtures/3.json', $this->nonExistingFileReq->getPathToFile());
    }

    public function testIsPathValidReturnsTrueForExistingFiles(): void
    {
        $this->assertTrue($this->file1Req->isPathValid());
    }

    public function testIsPathValidReturnsFalseForNonExistingFiles(): void
    {
        $this->assertFalse($this->nonExistingFileReq->isPathValid());
    }

    public function testIsInputUnitValidReturnsTrueForCorrectInputUnits(): void
    {
        $this->assertTrue($this->file1Req->isInputUnitValid());
        $this->assertTrue($this->nonExistingFileReq->isInputUnitValid());
    }

    public function testIsInputUnitValidReturnsFalseForIncorrectInputUnits(): void
    {
        $this->assertFalse($this->wrongUnitsReq->isInputUnitValid());
    }

    public function testIsInputUnitValidReturnsTrueForCorrectOutputUnits(): void
    {
        $this->assertTrue($this->file1Req->isOutputUnitValid());
        $this->assertTrue($this->nonExistingFileReq->isOutputUnitValid());
    }

    public function testIsInputUnitValidReturnsFalseForIncorrectOutputUnits(): void
    {
        $this->assertFalse($this->wrongUnitsReq->isOutputUnitValid());
    }

    public function testGetUnitsConversionReturnsValueForCorrectUnits(): void
    {
        $this->assertEquals(8.0E-6, $this->file1Req->getUnitsConversion());
        $this->assertEquals(8.0E-6, $this->nonExistingFileReq->getUnitsConversion());
    }

    public function testGetUnitsConversionThrowsErrorForIncorrectUnits(): void
    {
        $this->expectException(DataRateUnitException::class);
        $this->expectExceptionMessage('Unable to find unit with symbol kB');

        $this->wrongUnitsReq->getUnitsConversion();
    }
}