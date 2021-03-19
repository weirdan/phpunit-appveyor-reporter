<?php

namespace Weirdan\PhpUnitAppVeyorReporter\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class DummyTest extends TestCase
{
    /**
     * @testWith ["test", 1]
     */
    public function testSuccessful(string $_input, int $_length): void
    {
        $this->assertLessThan(2, rand(0, 1));
    }

    public function testFailure(): void
    {
        sleep(2);
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
