<?php

namespace Weirdan\PhpUnitAppVeyorReporter\Reporter;

use GuzzleHttp\Command\ServiceClientInterface;
use Weirdan\PhpUnitAppVeyorReporter\Reporter;

final class AppVeyorReporter implements Reporter
{
    private ServiceClientInterface $client;

    public function __construct(ServiceClientInterface $client)
    {
        $this->client = $client;
    }

    public function reportStarted(string $test, string $filename): void
    {
        $this->client->execute($this->client->getCommand(
            'addTest',
            [
                'testName' => $test,
                'testFramework' => 'PHPUnit',
                'fileName' => $filename,
                'outcome' => Reporter::OUTCOME_RUNNING,
            ]
        ));
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
            'durationMilliseconds' => round($time * 1000),
        ];
        if (null !== $error) {
            $params['ErrorMessage'] = $error;
        }
        $this->client->execute($this->client->getCommand('updateTest', $params));
    }
}
