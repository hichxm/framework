<?php

namespace Illuminate\Tests\View;

use Illuminate\View\Engines\PhpEngine;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ViewPhpEngineTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testViewsMayBeProperlyRendered()
    {
        $engine = new PhpEngine();
        $this->assertEquals('Hello World
', $engine->get(__DIR__.'/fixtures/basic.php'));
    }
}
