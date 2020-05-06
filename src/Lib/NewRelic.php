<?php
declare(strict_types=1);

namespace NewRelic\Lib;

use Exception;
use Throwable;

/**
 * Class to help work with NewRelic in PHP
 *
 * @author Christian Winther
 * @see https://docs.newrelic.com/docs/php/the-php-api
 */
class NewRelic
{

    protected static $ignoredExceptions = [];

    protected static $ignoredErrors = [];

    protected static $serverVariables = [];

    protected static $cookieVariables = [];

    protected static $currentTransactionName;

    /**
     * Change the application name
     *
     * @param  string $name
     * @return void
     */
    public static function applicationName($name)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_set_appname($name);
    }

    /**
     * Start a New Relic transaction
     *
     * @param  string $name
     * @return void
     */
    public static function start($name = null)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_start_transaction(NEW_RELIC_APP_NAME);

        if ($name) {
            static::$currentTransactionName = $name;
        }

        newrelic_name_transaction(static::$currentTransactionName);
    }

    /**
     * End a New Relic transaction
     *
     * @param  boolean $ignore Should the statistics NewRelic gathered be discarded?
     * @return void
     */
    public static function stop($ignore = false)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_end_transaction($ignore);
    }

    /**
     * Ignore the current transaction
     *
     * @return void
     */
    public static function ignoreTransaction()
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_ignore_transaction();
    }

    /**
     * Ignore the current apdex
     *
     * @return void
     */
    public static function ignoreApdex()
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_ignore_apdex();
    }

    /**
     * Should NewRelic capture params ?
     *
     * @param  boolean $boolean
     * @return void
     */
    public static function captureParams($boolean)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_capture_params($boolean);
    }

    /**
     * Add custom tracer method
     *
     * @param string $method
     */
    public static function addTracer($method)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_add_custom_tracer($method);
    }

    /**
     * Add a custom parameter to the New Relic transaction
     *
     * @param string $key
     * @param mixed $value
     */
    public static function parameter($key, $value)
    {
        if (!static::hasNewRelic()) {
            return false;
        }

        if (!is_scalar($value)) {
            $value = json_encode($value);
        }

        newrelic_add_custom_parameter($key, $value);
    }

    /**
     * Track a custom metric
     *
     * @param  string $key
     * @param  integer|float $value
     * @return void|\Exception
     */
    public static function metric($key, $value)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        if (!is_numeric($value)) {
            throw new \Exception('Value must be numeric');
        }

        newrelic_custom_metric($key, $value);
    }

    /**
     * Add a custom method to have traced by NewRelic
     *
     * @param  string $method
     * @return void
     */
    public static function tracer($method)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_add_custom_tracer($method);
    }

    /**
     * Ignore an exception class
     *
     * @param  string $exception
     * @return void
     */
    public static function ignoreException($exception)
    {
        static::$ignoredExceptions = array_merge(static::$ignoredExceptions, (array) $exception);
    }

    /**
     * Ignore error strings
     *
     * @param  string $error
     * @return void
     */
    public static function ignoreError($error)
    {
        static::$ignoredErrors = array_merge(static::$ignoredErrors, (array) $error);
    }

    /**
     * Server variables to collect
     *
     * @param  array $variables
     * @return void
     */
    public static function collectServerVariables(array $variables)
    {
        static::$serverVariables = array_merge(static::$serverVariables, (array) $variables);
    }

    /**
     * Cookie variables to collect
     *
     * @param  array $variables
     * @return void
     */
    public static function collectCookieVariables(array $variables)
    {
        static::$cookieVariables = array_merge(static::$cookieVariables, (array) $variables);
    }

    /**
     * Send an exception to New Relic
     *
     * @param  Exception|Throwable $exception
     * @return void
     */
    public static function sendException($exception)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        $exceptionClass = get_class($exception);
        if (in_array($exceptionClass, static::$ignoredExceptions)) {
            return;
        }

        newrelic_notice_error(null, $exception);
    }

    /**
     * Send an error to New Relic
     *
     * @param mixed $code
     * @param mixed $description
     * @param mixed $file
     * @param mixed $line
     * @param mixed $context
     * @return void
     */
    public static function sendError($code, $description, $file, $line, $context = null)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        foreach (static::$ignoredErrors as $errorMessage) {
            if (false !== strpos($description, $errorMessage)) {
                return;
            }
        }

        newrelic_notice_error($code, $description, $file, $line, $context);
    }

    /**
     * Set user attributes
     *
     * @param  string $user
     * @param  string $account
     * @param  string $product
     * @return void
     */
    public static function user($user, $account, $product)
    {
        if (!static::hasNewRelic()) {
            return;
        }

        newrelic_set_user_attributes($user, $account, $product);
    }

    /**
     * Check if the NewRelic PHP extension is loaded
     *
     * @return boolean
     */
    public static function hasNewRelic()
    {
        return extension_loaded('newrelic');
    }

    /**
     * Collect environmental data for the transaction
     *
     * @return void
     */
    public static function collect()
    {
        static::parameter('_get', $_GET);
        static::parameter('_post', $_POST);
        static::parameter('_files', $_FILES);

        foreach ($_SERVER as $key => $value) {
            if (!in_array($key, static::$serverVariables)) {
                continue;
            }
            static::parameter('server_' . strtolower($key), $value);
        }

        foreach ($_COOKIE as $key => $value) {
            if (!in_array($key, static::$cookieVariables)) {
                continue;
            }
            static::parameter('cookie_' . strtolower($key), $value);
        }
    }
}
