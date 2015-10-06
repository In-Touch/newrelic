<?php

namespace Intouch\Newrelic\Test\Handler;

use Intouch\Newrelic\Handler\NullHandler;

class NullHandlerTest extends \PHPUnit_Framework_TestCase
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
}
