<?php

namespace Intouch\Newrelic\Test;

use Intouch\Newrelic\Newrelic;
use PHPUnit\Framework\TestCase;

class NewrelicTest extends TestCase
{
    public function testConstructCreatesHandler()
    {
        $agent = new Newrelic();

        $class = 'Intouch\Newrelic\Handler\NullHandler';
        if ($this->isExtensionLoaded()) {
            $class = 'Intouch\Newrelic\Handler\DefaultHandler';
        }

        $this->assertAttributeInstanceOf($class, 'handler', $agent);
    }

    public function testConstructSetsHandler()
    {
        $handler = $this->getHandlerMock();

        $agent = new Newrelic(false, $handler);

        $this->assertAttributeSame($handler, 'handler', $agent);
    }

    public function testIsExtensionLoaded()
    {
        $agent = new Newrelic();

        $this->assertAttributeSame($this->isExtensionLoaded(), 'installed', $agent);
    }

    public function testConstructorThrowsRuntimeExceptionIfThrowIsTrueAndExtensionNotLoaded()
    {
        if ($this->isExtensionLoaded() === true) {
            $this->markTestSkipped('Can not run test when newrelic extension is loaded');
        }

        $this->expectException('RuntimeException', 'NewRelic PHP Agent does not appear to be installed');

        new Newrelic(true);
    }

    public function testAddCustomParameter()
    {
        $key = 'foo';
        $value = 'bar';

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_add_custom_parameter',
            array(
                $key,
                $value
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->addCustomParameter($key, $value));
    }

    public function testAddCustomTracer()
    {
        $functionName = 'bar';

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_add_custom_tracer',
            array(
                $functionName
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->addCustomTracer($functionName));
    }

    public function testBackgroundJob()
    {
        $flag = true;

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_background_job',
            array(
                $flag
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->backgroundJob($flag));
    }

    public function testCaptureParams()
    {
        $enable = true;

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_capture_params',
            array(
                $enable
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->captureParams($enable));
    }

    public function testCustomMetric()
    {
        $name = 'foo';
        $value = 9000;

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_custom_metric',
            array(
                $name,
                $value
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->customMetric($name, $value));
    }

    public function testDisableAutoRUM()
    {
        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_disable_autorum',
            array(),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->disableAutoRUM());
    }

    public function testEndOfTransaction()
    {
        $handler = $this->getHandlerSpy(
            'newrelic_end_of_transaction',
            array()
        );

        $agent = new Newrelic(false, $handler);

        $this->assertNull($agent->endOfTransaction());
    }

    public function testEndTransaction()
    {
        $ignore = false;

        $handler = $this->getHandlerSpy(
            'newrelic_end_transaction',
            array(
                $ignore,
            )
        );

        $agent = new Newrelic(false, $handler);

        $this->assertNull($agent->endTransaction($ignore));
    }

    public function testGetBrowserTimingFooter()
    {
        $includeTags = false;

        $result = '<p>Foo</p>';

        $handler = $this->getHandlerSpy(
            'newrelic_get_browser_timing_footer',
            array(
                $includeTags,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->getBrowserTimingFooter($includeTags));
    }

    public function testGetBrowserTimingHeader()
    {
        $includeTags = false;

        $result = '<p>Foo</p>';

        $handler = $this->getHandlerSpy(
            'newrelic_get_browser_timing_header',
            array(
                $includeTags,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->getBrowserTimingHeader($includeTags));
    }

    public function testIgnoreApdex()
    {
        $handler = $this->getHandlerSpy(
            'newrelic_ignore_apdex',
            array()
        );

        $agent = new Newrelic(false, $handler);

        $this->assertNull($agent->ignoreApdex());
    }

    public function testIgnoreTransaction()
    {
        $handler = $this->getHandlerSpy(
            'newrelic_ignore_transaction',
            array()
        );

        $agent = new Newrelic(false, $handler);

        $this->assertNull($agent->ignoreTransaction());
    }

    public function testNameTransaction()
    {
        $name = 'foo';

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_name_transaction',
            array(
                $name,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->nameTransaction($name));
    }

    public function testNoticeError()
    {
        $message = 'foo';
        $exception = new \InvalidArgumentException('bar');

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_notice_error',
            array(
                $message,
                $exception,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->noticeError($message, $exception));
    }

    public function testNoticeErrorWithoutException()
    {
        $message = 'foo';

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_notice_error',
            array(
                $message,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->noticeError($message));
    }

    public function testSetAppName()
    {
        $name = 'foo';
        $licence = 'bar9000';
        $xmit = false;

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_set_appname',
            array(
                $name,
                $licence,
                $xmit,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->setAppName($name, $licence, $xmit));
    }

    public function testRecordCustomEvent()
    {
        $name = 'foo';
        $attributes = array(
            'bar' => 'baz',
            'qux' => false,
        );

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_record_custom_event',
            array(
                $name,
                $attributes,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->recordCustomEvent($name, $attributes));
    }

    public function testSetUserAttributes()
    {
        $user = 'foo';
        $account = 'bar';
        $product = 'baz';

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_set_user_attributes',
            array(
                $user,
                $account,
                $product,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->setUserAttributes($user, $account, $product));
    }

    public function testStartTransaction()
    {
        $name = 'foo';
        $licence = 'bar9000';

        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_start_transaction',
            array(
                $name,
                $licence,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->startTransaction($name, $licence));
    }

    public function testCreateDistributedTracePayload()
    {
        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_create_distributed_trace_payload',
            array(),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->createDistributedTracePayload());
    }

    public function testAcceptDistributedTracePayload()
    {
        $payload = 'payload';
        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_accept_distributed_trace_payload',
            array(
                $payload,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->acceptDistributedTracePayload($payload));
    }

    public function testAcceptDistributedTracePayloadHttpSafe()
    {
        $payload = 'payload';
        $result = true;

        $handler = $this->getHandlerSpy(
            'newrelic_accept_distributed_trace_payload_httpsafe',
            array(
                $payload,
            ),
            $result
        );

        $agent = new Newrelic(false, $handler);

        $this->assertSame($result, $agent->acceptDistributedTracePayloadHttpSafe($payload));
    }

    /**
     * @return bool
     */
    private function isExtensionLoaded()
    {
        return extension_loaded('newrelic') && function_exists('newrelic_set_appname');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Handler
     */
    private function getHandlerMock()
    {
        return $this->getMockBuilder('Intouch\Newrelic\Handler\Handler')->getMock();
    }

    /**
     * @param string $functionName
     * @param array $arguments
     * @param mixed $result
     * @return Handler|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getHandlerSpy($functionName, array $arguments = array(), $result = null)
    {
        $handler = $this->getHandlerMock();

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->identicalTo($functionName),
                $this->identicalTo($arguments)
            )
            ->willReturn($result)
        ;

        return $handler;
    }
}
