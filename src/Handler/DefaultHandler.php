<?php

namespace Intouch\Newrelic\Handler;

class DefaultHandler implements Handler
{
    private $hasDistributedTracing;

    public function __construct($hasDistributedTracing)
    {
        $this->hasDistributedTracing = $hasDistributedTracing;
    }

    public function handle($functionName, array $arguments = array())
    {
        return call_user_func_array($functionName, $arguments);
    }

    public function isDistributedTracingEnabled()
    {
        return $this->hasDistributedTracing;
    }
}
