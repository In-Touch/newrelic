<?php

namespace Intouch\Newrelic\Test\Handler;

use Intouch\Newrelic\Handler\DefaultHandler;
use PHPUnit\Framework\TestCase;

class DefaultHandlerTest extends TestCase
{
    public function testImplementsInterface()
    {
        $handler = new DefaultHandler(false);

        $this->assertInstanceOf('Intouch\Newrelic\Handler\Handler', $handler);
    }

    public function testHandleCallsFunctionWithArguments()
    {
        $functionName = 'strpos';
        $arguments = array(
            'foobarbaz',
            'bar',
            0
        );

        $handler = new DefaultHandler(false);

        $expected = call_user_func_array($functionName, $arguments);

        $this->assertSame($expected, $handler->handle($functionName, $arguments));
    }

    public function testIsDistributedTracingEnabled()
    {
        $handler = new DefaultHandler(true);

        $this->assertTrue($handler->isDistributedTracingEnabled());
    }

    public function testIsDistributedTracingEnabledFalse()
    {
        $handler = new DefaultHandler(false);

        $this->assertFalse($handler->isDistributedTracingEnabled());
    }
}
