<?php

namespace Weirdan\PhpUnitAppVeyorReporter\Reporter;

use Psr\Log\LoggerInterface;
use Weirdan\PhpUnitAppVeyorReporter\ClientFactory;
use Weirdan\PhpUnitAppVeyorReporter\Reporter;

final class Factory
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(): Reporter
    {
        if (getenv('APPVEYOR')) {
            $client = (new ClientFactory($this->logger))();
            return new AppVeyorReporter($client, $this->logger);
        }
        return new NullReporter();
    }
}
