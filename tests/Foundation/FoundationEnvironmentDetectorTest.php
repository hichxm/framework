<?php

namespace Illuminate\Tests\Foundation;

use Illuminate\Foundation\EnvironmentDetector;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class FoundationEnvironmentDetectorTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testClosureCanBeUsedForCustomEnvironmentDetection()
    {
        $env = new EnvironmentDetector();

        $result = $env->detect(function () {
            return 'foobar';
        });
        $this->assertEquals('foobar', $result);
    }

    public function testConsoleEnvironmentDetection()
    {
        $env = new EnvironmentDetector();

        $result = $env->detect(function () {
            return 'foobar';
        }, ['--env=local']);
        $this->assertEquals('local', $result);
    }
}
