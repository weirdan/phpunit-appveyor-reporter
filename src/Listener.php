<?php

namespace Weirdan\PhpUnitAppVeyorReporter;

use PHPUnit\Runner\AfterIncompleteTestHook;
use PHPUnit\Runner\AfterRiskyTestHook;
use PHPUnit\Runner\AfterSkippedTestHook;
use PHPUnit\Runner\AfterSuccessfulTestHook;
use PHPUnit\Runner\AfterTestErrorHook;
use PHPUnit\Runner\AfterTestFailureHook;
use PHPUnit\Runner\AfterTestHook;
use PHPUnit\Runner\AfterTestWarningHook;
use PHPUnit\Runner\BeforeTestHook;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use RuntimeException;
use Weirdan\PhpUnitAppVeyorReporter\Reporter\Factory as ReporterFactory;

final class Listener implements
    BeforeTestHook,
    AfterSuccessfulTestHook,
    AfterTestFailureHook,
    AfterSkippedTestHook,
    AfterIncompleteTestHook,
    AfterTestWarningHook,
    AfterTestErrorHook,
    AfterRiskyTestHook,
    AfterTestHook
{
    private bool $reported = false;
    private Reporter $reporter;
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = (new LoggerFactory())();
        $this->reporter = (new ReporterFactory($this->logger))();
    }

    public function executeBeforeTest(string $test): void
    {
        $this->reporter->reportStarted($this->stripDataset($test), $this->testNameToFileName($test));
        $this->reported = false;
    }

    public function executeAfterSuccessfulTest(string $test, float $time): void
    {
        $this->reportCompletion($test, $time, Reporter::OUTCOME_PASSED);
    }

    public function executeAfterTestFailure(string $test, string $message, float $time): void
    {
        $this->reportCompletion($test, $time, Reporter::OUTCOME_FAILED, $message);
    }

    public function executeAfterTestError(string $test, string $message, float $time): void
    {
        $this->reportCompletion($test, $time, Reporter::OUTCOME_FAILED, $message);
    }

    public function executeAfterTestWarning(string $test, string $message, float $time): void
    {
        $this->reportCompletion($test, $time, Reporter::OUTCOME_PASSED, $message);
    }

    public function executeAfterSkippedTest(string $test, string $message, float $time): void
    {
        $this->reportCompletion($test, $time, Reporter::OUTCOME_SKIPPED, $message);
    }

    public function executeAfterIncompleteTest(string $test, string $message, float $time): void
    {
        $this->reportCompletion($test, $time, Reporter::OUTCOME_INCONCLUSIVE, $message);
    }

    public function executeAfterRiskyTest(string $test, string $message, float $time): void
    {
        $this->reportCompletion($test, $time, Reporter::OUTCOME_PASSED, $message);
    }

    public function executeAfterTest(string $test, float $time): void
    {
        if ($this->reported) {
            return;
        }
        $this->reportCompletion($test, $time, Reporter::OUTCOME_INCONCLUSIVE);
    }


    /** @param Reporter::OUTCOME_* $outcome */
    private function reportCompletion(string $test, float $time, string $outcome, ?string $message = null): void
    {
        $this->reporter->reportFinished(
            $this->stripDataset($test),
            $this->testNameToFileName($test),
            $outcome,
            $time,
            $message
        );
        $this->reported = true;
    }

    private function testNameToFileName(string $test): string
    {
        $className = strtok($test, '::');

        if (false === $className || !class_exists($className)) {
            throw new RuntimeException("Failed to resolve $test to a file name");
        }

        $class = new ReflectionClass($className);
        return $class->getFileName();
    }

    private function stripDataset(string $test): string
    {
        if (preg_match('/^.* with data set ".*"/U', $test, $matches)) {
            return $matches[0];
        }
        if (preg_match('/^.* with data set #\d+/U', $test, $matches)) {
            return $matches[0];
        }
        return $test;
    }
}
