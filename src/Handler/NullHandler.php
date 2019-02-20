<?php

namespace Intouch\Newrelic\Handler;

class NullHandler implements Handler
{
    public function handle($functionName, array $arguments = array())
    {
        return false;
    }

    public function isDistributedTracingEnabled()
    {
        return false;
    }
}
