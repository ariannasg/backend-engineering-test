<?php declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class AppAnalyseMetricsCommandTest
 *
 * @package App\Tests\Command
 */
class AppAnalyseMetricsCommandTest extends KernelTestCase
{
    /**
     * @return \Generator
     */
    public function detectionProvider(): \Generator
    {
        /**
         * The first test case is all about working statistics
         * from a basic set of values. Check out the input and
         * output files of what we expect.
         */
        yield [
            // Filename
            'resources/fixtures/1.json',

            // Output
            'resources/fixtures/1.output',
        ];

        /**
         * The second test case is all pattern detection, working
         * out when something out of the ordinary is happening.
         */
        yield [
            // Filename
            'resources/fixtures/2.json',

            // Output
            'resources/fixtures/2.output',
        ];
    }

    /**
     * Tests to ensure we're correctly detecting all slowdowns.
     *
     * @dataProvider detectionProvider
     *
     * @param string $filename The filename of the input fixture.
     * @param string $expected The expected output of the command.
     */
    public function testDetection(string $filename, string $expected): void
    {
        $kernel = self::createKernel();
        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('app:analyse-metrics');

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command' => $command->getName(),
            '--path-to-file' => \realpath($filename),
            '--input-unit-symbol' => 'B',
            '--output-unit-symbol' => 'Mbit',
        ]);

        $this->assertStringEqualsFile(\realpath($expected), $commandTester->getDisplay());
    }
}
