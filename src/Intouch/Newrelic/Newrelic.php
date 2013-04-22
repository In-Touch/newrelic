<?php
/**
 * Copyright 2013 In-Touch Insight Systems
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Intouch\Newrelic;

/**
 * Wrapper class for the NewRelic PHP Agent API methods.
 *
 * This class is designed to work with PHP Agent version 3.1+ and requires PHP 5.3+
 *
 * @package Intouch\Newrelic
 */
class Newrelic
{
    /**
     * @var bool
     */
    protected $installed;

    /**
     * Allows pass-through if NewRelic is not installed (default) or optionally throws a runtime exception is the
     * NewRelic PHP agent methods are not found.
     *
     * @param bool $throw
     *
     * @throws \RuntimeException
     */
    public function __construct( $throw = false )
    {
        $this->installed = extension_loaded( 'newrelic' ) && function_exists( 'newrelic_set_appname' );
        if ( $throw && !$this->installed )
        {
            throw new \RuntimeException('NewRelic PHP Agent does not appear to be installed');
        }
    }

    /**
     * Sets the name of the application to name. The string uses the same format as newrelic.appname and can set
     * multiple application names by separating each with a semi-colon. However please be aware of the restriction on
     * the application name ordering as described for that setting.
     *
     * The first application name is the primary name, and up to two extra application names can be specified (however
     * the same application name can only ever be used once as a primary name). This function should be called as early
     * as possible, and will have no effect if called after the RUM footer has been sent. You may want to consider
     * setting the application name in a file loaded by PHP's auto_prepend_file INI setting. This function returns true
     * if it succeeded or false otherwise.
     *
     * If you use multiple licenses you can also specify a license key along with the application name. An application
     * can appear in more than one account and the license key controls which account you are changing the name in.
     * If you do not wish to change the license and wish to use the third variant, simply set the license key to the
     * empty string ("").
     *
     * The xmit flag is new in version 3.1 of the agent. Usually, when you change an application name, the agent simply
     * discards the current transaction and does not send any of the accumulated metrics to the daemon. However, if you
     * want to record the metric and transaction data up to the point at which you called this function, you can specify
     * a value of true for this argument to make the agent send the transaction to the daemon. This has a very slight
     * performance impact as it takes a few milliseconds for the agent to dump its data. By default this parameter is
     * false.
     *
     * @param string $name
     * @param string $license
     * @param bool   $xmit
     *
     * @return bool
     */
    public function setAppName( $name, $license = "", $xmit = false )
    {
        return $this->call( 'newrelic_set_appname', array( $name, $license, $xmit ) );
    }

    /**
     * Report an error at this line of code, with a complete stack trace. The third form of the call was added in agent
     * version 2.6 and should be used for reporting exceptions. Only the exception for the last call is retained during
     * the course of a transaction.
     *
     * The exception parameter must be a valid PHP Exception class, and the stack frame recorded in that class will be
     * the one reported, rather than the stack at the time this function was called. When using this form, if the error
     * message is empty, a standard message in the same format as created by Exception::__toString() will be
     * automatically generated.
     *
     * NOTE: You should always pass an exception here if possible.
     *
     * @param string          $message
     * @param \Exception|null $exception
     *
     * @return mixed
     */
    public function noticeError( $message, $exception = null )
    {
        if ( $exception instanceof \Exception )
        {
            return $this->call( 'newrelic_notice_error', array( $message, $exception ) );
        }
        else
        {
            return $this->call( 'newrelic_notice_error', array( $message ) );
        }
    }

    /**
     * Sets the name of the transaction to the specified string. This can be useful if you have implemented your own
     * dispatching scheme and wish to name transactions according to their purpose rather than their URL.
     *
     * Avoid creating too many unique transaction names. For example, if you have /product/123 and /product/234, if you
     * generate a separate transaction name for each, then New Relic will store separate information for these two
     * transaction names. This will make your graphs less useful, and may run into limits we set on the number of unique
     * transaction names per account. It also can slow down the performance of your application. Instead, store the
     * transaction as /product/*, or use something significant about the code itself to name the transaction, such as
     * /Product/view. The limit for the total number of transactions should be less than 1000 unique transaction
     * names -- exceeding that is not recommended.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function nameTransaction( $name )
    {
        return $this->call( 'newrelic_name_transaction', array( $name ) );
    }

    /**
     * Stop recording the web transaction immediately. Usually used when a page is done with all computation and is
     * about to stream data (file download, audio or video streaming etc) and you don't want the time taken to stream to
     * be counted as part of the transaction. This is especially relevant when the time taken to complete the operation
     * is completely outside the bounds of your application. For example, a user on a very slow connection may take a
     * very long time to download even small files, and you wouldn't want that download time to skew the real
     * transaction time.
     *
     * @return mixed
     */
    public function endOfTransaction()
    {
        return $this->call( 'newrelic_end_of_transaction' );
    }

    /**
     * Despite being similar in name to newrelic_end_of_transaction above, this call serves a very different purpose.
     * newrelic_end_of_transaction simply marks the end time of the transaction but takes no other action. The
     * transaction is still only sent to the daemon when the PHP engine determines that the script is done executing and
     * is shutting down. This function on the other hand, causes the current transaction to end immediately, and will
     * ship all of the metrics gathered thus far to the daemon unless the ignore parameter is set to true. In effect
     * this call simulates what would happen when PHP terminates the current transaction. This is most commonly used in
     * command line scripts that do some form of job queue processing. You would use this call at the end of processing
     * a single job task, and begin a new transaction (see below) when a new task is pulled off the queue.
     *
     * Normally, when you end a transaction you want the metrics that have been gathered thus far to be recorded.
     * However, there are times when you may want to end a transaction without doing so. In this case use the second
     * form of the function and set ignore to true.
     *
     * @param bool $ignore
     *
     * @return mixed
     */
    public function endTransaction( $ignore = false )
    {
        return $this->call( 'newrelic_end_transaction', array( $ignore ) );
    }

    /**
     * If you have ended a transaction before your script terminates (perhaps due to it just having finished a task in a
     * job queue manager) and you want to start a new transaction, use this call. This will perform the same operations
     * that occur when the script was first started. Of the two arguments, only the application name is mandatory.
     * However, if you are processing tasks for multiple accounts, you may also provide a license for the associated
     * account. The license set for this API call will supersede all per-directory and global default licenses
     * configured in INI files.
     *
     * @param string $name
     * @param string $license
     *
     * @return mixed
     */
    public function startTransaction( $name, $license = "" )
    {
        return $this->call( 'newrelic_start_transaction', array( $name, $license ) );
    }

    /**
     * Do not generate metrics for this transaction. This is useful when you have transactions that are particularly
     * slow for known reasons and you do not want them always being reported as the transaction trace or skewing your
     * site averages.
     *
     * @return mixed
     */
    public function ignoreTransaction()
    {
        return $this->call( 'newrelic_ignore_transaction' );
    }

    /**
     * Do not generate Apdex metrics for this transaction. This is useful when you have either very short or very long
     * transactions (such as file downloads) that can skew your apdex score.
     *
     * @return mixed
     */
    public function ignoreApdex()
    {
        return $this->call( 'newrelic_ignore_apdex' );
    }

    /**
     * If no argument or true as an argument is given, mark the current transaction as a background job. If false is
     * passed as an argument, mark the transaction as a web transaction.
     *
     * @param bool $flag
     *
     * @return mixed
     */
    public function backgroundJob( $flag = true )
    {
        return $this->call( 'newrelic_background_job', array( $flag ) );
    }

    /**
     * If enable is omitted or set to on, enables the capturing of URL parameters for displaying in transaction traces.
     * In essence this overrides the newrelic.capture_params setting. In agents prior to 2.1.3 this was called
     * newrelic_enable_params() but that name is now deprecated.
     *
     * @param bool $enable
     *
     * @return mixed
     */
    public function captureParams( $enable = true )
    {
        return $this->call( 'newrelic_capture_params', array( $enable ) );
    }

    /**
     * Adds a custom metric with the specified name and value, which is of type double. Values saved are assumed to be
     * milliseconds, so "4" will be stored as ".004" in our system. Your custom metrics can then be used in custom
     * dashboards and custom views in the New Relic user interface. It's a best practice to name your custom metrics
     * with a Custom/ prefix. This will make them easily usable in custom dashboards.
     *
     * Note: Avoid creating too many unique custom metric names. New Relic limits the total number of custom metrics you
     * can use (not the total you can report for each of these custom metrics). Exceeding more than 2000 unique custom
     * metric names can cause automatic clamps that will affect other data.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function customMetric( $name, $value )
    {
        return $this->call( 'newrelic_custom_metric', array( $name, $value ) );
    }

    /**
     * Add a custom parameter to the current web transaction with the specified value. For example, you can add a
     * customer's full name from your customer database. This parameter is shown in any transaction trace that results
     * from this transaction.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function addCustomParameter( $key, $value )
    {
        return $this->call( 'newrelic_add_custom_parameter', array( $key, $value ) );
    }

    /**
     * API equivalent of the newrelic.transaction_tracer.custom setting. It allows you to add user defined functions or
     * methods to the list to be instrumented. Internal PHP functions cannot have custom tracing.
     *
     * NOTE: $function_name should be in the format 'foo' or 'Foo::Bar' as a string, not the PHP callable array format.
     *
     * @param string $function_name
     *
     * @return mixed
     */
    public function addCustomTracer( $function_name )
    {
        return $this->call( 'newrelic_add_custom_tracer', array( $function_name ) );
    }

    /**
     * Returns the JavaScript string to inject as part of the header for browser timing (real user monitoring). If flag
     * is specified it must be a boolean, and if omitted, defaults to true. This indicates whether or not surrounding
     * script tags should be returned as part of the string.
     *
     * @param bool $flag
     *
     * @return string
     */
    public function getBrowserTimingHeader( $flag = true )
    {
        return $this->call( 'newrelic_get_browser_timing_header', array( $flag ) );
    }

    /**
     * Returns the JavaScript string to inject at the very end of the HTML output for browser timing (real user
     * monitoring). If flag is specified it must be a boolean, and if omitted, defaults to true. This indicates whether
     * or not surrounding script tags should be returned as part of the string.
     *
     * @param bool $flag
     *
     * @return string
     */
    public function getBrowserTimingFooter( $flag = true )
    {
        return $this->call( 'newrelic_get_browser_timing_footer', array( $flag ) );
    }

    /**
     * Prevents the output filter from attempting to insert RUM JavaScript for this current transaction. Useful for
     * AJAX calls, for example.
     *
     * @return mixed
     */
    public function disableAutoRUM()
    {
        return $this->call( 'newrelic_disable_autorum' );
    }

    /**
     * Adds the three parameter strings to collected browser traces. All three parameters are required, but may be empty
     * strings. For more information please see the section on
     * {@link https://newrelic.com/docs/features/browser-traces browser traces}.
     *
     * @param string $user
     * @param string $account
     * @param string $product
     *
     * @return mixed
     */
    public function setUserAttributes( $user = "", $account = "", $product = "" )
    {
        return $this->call( 'newrelic_set_user_attributes', array( $user, $account, $product ) );
    }

    /**
     * Call the named method with the given params.  Return false if the NewRelic PHP agent is not available.
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    protected function call( $method, array $params = array() )
    {
        if ( !$this->installed )
        {
            return false;
        }

        return call_user_func_array( $method, $params );
    }
}