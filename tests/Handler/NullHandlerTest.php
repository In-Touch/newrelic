<?php

namespace Intouch\Newrelic\Test\Handler;

use Intouch\Newrelic\Handler\NullHandler;
use PHPUnit\Framework\TestCase;

class NullHandlerTest extends TestCase
{
    public function testImplementsInterface()
    {
        $handler = new NullHandler();

        $this->assertInstanceOf('Intouch\Newrelic\Handler\Handler', $handler);
    }

    public function testHandleReturnsFalse()
    {
        $functionName = 'strpos';
        $arguments = array(
            'foobarbaz',
            'bar',
            0
        );

        $handler = new NullHandler();

        $this->assertFalse($handler->handle($functionName, $arguments));
    }

    public function testIsDistributedTracingEnabledReturnsFalse()
    {
        $handler = new NullHandler();

        $this->assertFalse($handler->isDistributedTracingEnabled());
    }
}
