<?php

namespace Weirdan\PhpUnitAppVeyorReporter\Reporter;

use Weirdan\PhpUnitAppVeyorReporter\Reporter;

final class Factory
{
    public function __invoke(): Reporter
    {
        if (getenv('APPVEYOR')) {
            return new AppVeyorReporter();
        }
        return new NullReporter();
    }
}
