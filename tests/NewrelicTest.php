<?php

namespace Intouch\Newrelic\Test;

use Intouch\Newrelic\Newrelic;

class NewrelicTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructCreatesDefaultHandler()
    {
        $agent = new Newrelic();

        $this->assertAttributeInstanceOf('Intouch\Newrelic\Handler\DefaultHandler', 'handler', $agent);
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

        $this->setExpectedException('RuntimeException', 'NewRelic PHP Agent does not appear to be installed');

        new Newrelic(true);
    }

    public function testMethodsExist()
    {
        if ($this->isExtensionLoaded() === true) {
            $this->markTestSkipped('Can not run test when newrelic extension is loaded');
        }

        $agent = new Newrelic();

        $this->assertFalse($agent->addCustomParameter('foo', 'bar'));
        $this->assertFalse($agent->addCustomTracer('foo'));
        $this->assertFalse($agent->backgroundJob(false));
        $this->assertFalse($agent->captureParams(false));
        $this->assertFalse($agent->customMetric('foo', 9000));
        $this->assertFalse($agent->disableAutoRUM());
        $this->assertFalse($agent->endOfTransaction());
        $this->assertFalse($agent->endTransaction());
        $this->assertFalse($agent->getBrowserTimingFooter(false));
        $this->assertFalse($agent->getBrowserTimingHeader(false));
        $this->assertFalse($agent->ignoreApdex());
        $this->assertFalse($agent->ignoreTransaction());
        $this->assertFalse($agent->nameTransaction('foo'));
        $this->assertFalse($agent->noticeError('foo', 'bar'));
        $this->assertFalse($agent->setAppName('foo', 'bar', false));
        $this->assertFalse($agent->setUserAttributes('foo', 'bar', 'baz'));
        $this->assertFalse($agent->startTransaction('foo', 'bar'));
    }

    /**
     * @return bool
     */
    private function isExtensionLoaded()
    {
        return extension_loaded('newrelic') && function_exists('newrelic_set_appname');
    }
}
