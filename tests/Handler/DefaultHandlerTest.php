<?php

namespace Intouch\Newrelic\Test\Handler;

use Intouch\Newrelic\Handler\DefaultHandler;

class DefaultHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsInterface()
    {
        $handler = new DefaultHandler();

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

        $handler = new DefaultHandler();

        $expected = call_user_func_array($functionName, $arguments);

        $this->assertSame($expected, $handler->handle($functionName, $arguments));
    }
}
