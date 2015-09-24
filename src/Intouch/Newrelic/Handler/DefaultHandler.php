<?php

namespace Intouch\Newrelic\Handler;

class DefaultHandler
{
    /**
     * @param string $functionName
     * @param array $arguments
     *
     * @return mixed
     */
    public function handle($functionName, array $arguments = array())
    {
        return call_user_func_array($functionName, $arguments);
    }
}
