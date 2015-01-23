<?php
namespace NewRelic\Lib;

use Exception;

/**
 * Class to help work with NewRelic in PHP
 *
 * @author Christian Winther
 * @see https://docs.newrelic.com/docs/php/the-php-api
 */
class NewRelic {

	protected static $ignoredExceptions = [];

/**
 * Change the application name
 *
 * @param  string $name
 * @return void
 */
	public static function applicationName($name) {
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
	public static function start($name = null) {
		if (!static::hasNewRelic()) {
			return;
		}

		newrelic_start_transaction(NEW_RELIC_APP_NAME);
		newrelic_name_transaction($name);
	}

/**
 * End a New Relic transaction
 *
 * @param  boolean $ignore Should the statistics NewRelic gathered be discarded?
 * @return void
 */
	public static function stop($ignore = false) {
		if (!static::hasNewRelic()) {
			return;
		}

		newrelic_end_transaction($ignore);
	}

/**
 * Ignore the current transaction
 *
 * @return
 */
	public static function ignoreTransaction() {
		if (!static::hasNewRelic()) {
			return;
		}

		newrelic_ignore_transaction();
	}

/**
 * Ignore the current apdex
 *
 * @return
 */
	public static function ignoreApdex() {
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
	public static function captureParams($boolean) {
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
	public static function addTracer($method) {
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
	public static function parameter($key, $value) {
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
 * @return
 */
	public static function metric($key, $value) {
		if (!static::hasNewRelic()) {
			return;
		}

		if (!is_numeric($value)) {
			throw new CakeException('Value must be numeric');
		}

		newrelic_custom_metric($key, $value);
	}

/**
 * Add a custom method to have traced by NewRelic
 *
 * @param  string $method
 * @return void
 */
	public static function tracer($method) {
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
	public static function ignoreException($exception) {
		static::$ignoredExceptions = array_merge(static::$ignoredExceptions, (array)$exception);
	}

/**
 * Send an exception to New Relic
 *
 * @param  Exception $exception
 * @return void
 */
	public static function sendException(Exception $exception) {
		if (!static::hasNewRelic()) {
			return;
		}

		if (false !== array_search(get_class($exception), static::$ignoredExceptions)) {
			return;
		}

		newrelic_notice_error(null, $exception);
	}

/**
 * Send an error to New Relic
 *
 * @param  [type] $code        [description]
 * @param  [type] $description [description]
 * @param  [type] $file        [description]
 * @param  [type] $line        [description]
 * @param  [type] $context     [description]
 * @return [type]              [description]
 */
	public static function sendError($code, $description, $file, $line, $context = null) {
		if (!static::hasNewRelic()) {
			return;
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
	public static function user($user, $account, $product) {
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
	public static function hasNewRelic() {
		return extension_loaded('newrelic');
	}

}
