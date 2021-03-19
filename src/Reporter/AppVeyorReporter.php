<?php

namespace Weirdan\PhpUnitAppVeyorReporter\Reporter;

use GuzzleHttp\Command\ServiceClientInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Weirdan\PhpUnitAppVeyorReporter\Reporter;

final class AppVeyorReporter implements Reporter
{
    private ServiceClientInterface $client;
    private LoggerInterface $logger;

    public function __construct(ServiceClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function reportStarted(string $test, string $filename): void
    {
        try {
            $this->client->execute($this->client->getCommand(
                'addTest',
                [
                    'testName' => $test,
                    'testFramework' => 'PHPUnit',
                    'fileName' => $filename,
                    'outcome' => Reporter::OUTCOME_RUNNING,
                ]
            ));
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
        }
    }

    public function reportFinished(
        string $test,
        string $filename,
        string $outcome,
        float $time,
        ?string $error = null
    ): void {
        $params = [
            'testName' => $test,
            'testFramework' => 'PHPUnit',
            'fileName' => $filename,
            'outcome' => $outcome,
            'durationMilliseconds' => (int) round($time * 1000),
        ];
        if (null !== $error) {
            $params['ErrorMessage'] = $error;
        }

        try {
            $this->client->execute($this->client->getCommand('updateTest', $params));
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
        }
    }
}
