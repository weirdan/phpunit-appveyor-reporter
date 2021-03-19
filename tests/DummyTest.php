<?php

namespace Weirdan\PhpUnitAppVeyorReporter\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class DummyTest extends TestCase
{
    public function testSuccessful(): void
    {
        $this->assertLessThan(2, rand(0, 1));
    }

    public function testFailure(): void
    {
        $this->fail('oops');
    }

    public function testError(): void
    {
        throw new RuntimeException('oops');
    }

    public function testSkipped(): void
    {
        $this->markTestSkipped('skipped');
    }

    public function testIncomplete(): void
    {
        $this->markTestIncomplete('incomplete');
    }

    public function testsRisky(): void
    {
        (function () {
            echo "\nzzz\n";
        })();
        $this->assertLessThan(2, rand(0, 1));
    }
}
