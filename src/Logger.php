<?php

namespace Weirdan\PhpUnitAppVeyorReporter;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

final class Logger implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = [])
    {
        $level = strtoupper((string) $level);
        fwrite(STDERR, "[$level] $message / " . json_encode($context, JSON_THROW_ON_ERROR) . "\n");
    }
}
