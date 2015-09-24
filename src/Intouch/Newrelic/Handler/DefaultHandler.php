<?php

namespace Intouch\Newrelic\Handler;

class DefaultHandler implements Handler
{
    public function handle($functionName, array $arguments = array())
    {
        return call_user_func_array($functionName, $arguments);
    }
}
