<?php

namespace Weirdan\PhpUnitAppVeyorReporter;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class LoggerFactory
{
    public function __invoke(): LoggerInterface
    {
        if (getenv('PHPUNIT_APPVEYOR_REPORTER_DEBUG')) {
            return new Logger();
        }
        return new NullLogger();
    }
}
