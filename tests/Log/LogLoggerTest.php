<?php

namespace Illuminate\Tests\Log;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Log\Logger;
use Mockery as m;
use Monolog\Logger as Monolog;
use PHPUnit\Framework\TestCase;

class LogLoggerTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testMethodsPassErrorAdditionsToMonolog()
    {
        $writer = new Logger($monolog = m::mock(Monolog::class));
        $monolog->shouldReceive('error')->once()->with('foo', []);

        $writer->error('foo');
    }

    public function testLoggerFiresEventsDispatcher()
    {
        $writer = new Logger($monolog = m::mock(Monolog::class), $events = new Dispatcher());
        $monolog->shouldReceive('error')->once()->with('foo', []);

        $events->listen(MessageLogged::class, function ($event) {
            $_SERVER['__log.level'] = $event->level;
            $_SERVER['__log.message'] = $event->message;
            $_SERVER['__log.context'] = $event->context;
        });

        $writer->error('foo');
        $this->assertTrue(isset($_SERVER['__log.level']));
        $this->assertEquals('error', $_SERVER['__log.level']);
        unset($_SERVER['__log.level']);
        $this->assertTrue(isset($_SERVER['__log.message']));
        $this->assertEquals('foo', $_SERVER['__log.message']);
        unset($_SERVER['__log.message']);
        $this->assertTrue(isset($_SERVER['__log.context']));
        $this->assertEquals([], $_SERVER['__log.context']);
        unset($_SERVER['__log.context']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Events dispatcher has not been set.
     */
    public function testListenShortcutFailsWithNoDispatcher()
    {
        $writer = new Logger($monolog = m::mock(Monolog::class));
        $writer->listen(function () {
            //
        });
    }

    public function testListenShortcut()
    {
        $writer = new Logger($monolog = m::mock(Monolog::class), $events = m::mock(DispatcherContract::class));

        $callback = function () {
            return 'success';
        };
        $events->shouldReceive('listen')->with(MessageLogged::class, $callback)->once();

        $writer->listen($callback);
    }
}
