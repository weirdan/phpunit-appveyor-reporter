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

    public function __construct()
    {
        $this->reporter = (new ReporterFactory())();
    }

    public function executeBeforeTest(string $test): void
    {
        $this->reporter->reportStarted($test, $this->testNameToFileName($test));
        $this->reported = false;
    }

    public function executeAfterSuccessfulTest(string $test, float $time): void
    {
        $this->reporter->reportFinished(
            $test,
            $this->testNameToFileName($test),
            Reporter::OUTCOME_PASSED,
            $time
        );
        $this->reported = true;
    }

    public function executeAfterTestFailure(string $test, string $message, float $time): void
    {
        $this->reporter->reportFinished(
            $test,
            $this->testNameToFileName($test),
            Reporter::OUTCOME_FAILED,
            $time,
            $message
        );
        $this->reported = true;
    }

    public function executeAfterTestError(string $test, string $message, float $time): void
    {
        $this->reporter->reportFinished(
            $test,
            $this->testNameToFileName($test),
            Reporter::OUTCOME_FAILED,
            $time,
            $message
        );
        $this->reported = true;
    }

    public function executeAfterTestWarning(string $test, string $message, float $time): void
    {
        $this->reporter->reportFinished(
            $test,
            $this->testNameToFileName($test),
            Reporter::OUTCOME_PASSED,
            $time,
            $message
        );
        $this->reported = true;
    }

    public function executeAfterSkippedTest(string $test, string $message, float $time): void
    {
        $this->reporter->reportFinished(
            $test,
            $this->testNameToFileName($test),
            Reporter::OUTCOME_SKIPPED,
            $time,
            $message
        );
        $this->reported = true;
    }

    public function executeAfterIncompleteTest(string $test, string $message, float $time): void
    {
        $this->reporter->reportFinished(
            $test,
            $this->testNameToFileName($test),
            Reporter::OUTCOME_INCONCLUSIVE,
            $time,
            $message
        );
        $this->reported = true;
    }

    public function executeAfterRiskyTest(string $test, string $message, float $time): void
    {
        $this->reporter->reportFinished(
            $test,
            $this->testNameToFileName($test),
            Reporter::OUTCOME_PASSED,
            $time,
            $message
        );
        $this->reported = true;
    }

    public function executeAfterTest(string $test, float $time): void
    {
        if ($this->reported) {
            return;
        }
        $this->reporter->reportFinished(
            $test,
            $this->testNameToFileName($test),
            Reporter::OUTCOME_INCONCLUSIVE,
            $time
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
}
