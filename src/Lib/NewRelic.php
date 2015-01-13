<?php
namespace NewRelic\Lib;

/**
 * Class to help work with NewRelic in PHP
 *
 * @author Christian Winther
 * @see https://docs.newrelic.com/docs/php/the-php-api
 */
class NewRelic {

/**
 * Static instance of NewRelic
 *
 * @var NewRelic
 */
	protected static $_instance;

/**
 * Get the singleton instance of NewRelic
 *
 * @return NewRelic
 */
	public static function getInstance() {
		if (static::$_instance === null) {
			static::$_instance = new NewRelic();
		}

		return static::$_instance;
	}

/**
 * Change the application name
 *
 * @param  string $name
 * @return void
 */
	public function applicationName($name) {
		if (!$this->hasNewRelic()) {
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
	public function start($name = null) {
		if (!$this->hasNewRelic()) {
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
	public function stop($ignore = false) {
		if (!$this->hasNewRelic()) {
			return;
		}

		newrelic_end_transaction($ignore);
	}

/**
 * Ignore the current transaction
 *
 * @return
 */
	public function ignoreTransaction() {
		if (!$this->hasNewRelic()) {
			return;
		}

		newrelic_ignore_transaction();
	}

/**
 * Ignore the current apdex
 *
 * @return
 */
	public function ignoreApdex() {
		if (!$this->hasNewRelic()) {
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
	public function captureParams($boolean) {
		if (!$this->hasNewRelic()) {
			return;
		}

		newrelic_capture_params($boolean);
	}

/**
 * Add custom tracer method
 *
 * @param string $method
 */
	public function addTracer($method) {
		if (!$this->hasNewRelic()) {
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
	public function parameter($key, $value) {
		if (!$this->hasNewRelic()) {
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
	public function metric($key, $value) {
		if (!$this->hasNewRelic()) {
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
	public function tracer($method) {
		if (!$this->hasNewRelic()) {
			return;
		}

		newrelic_add_custom_tracer($method);
	}

/**
 * Send an exception to New Relic
 *
 * @param  Exception $e
 * @return void
 */
	public function sendException(Exception $e) {
		if (!$this->hasNewRelic()) {
			return;
		}

		newrelic_notice_error(null, $e);
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
	public function sendError($code, $description, $file, $line, $context = null) {
		if (!$this->hasNewRelic()) {
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
	public function user($user, $account, $product) {
		if (!$this->hasNewRelic()) {
			return;
		}

		newrelic_set_user_attributes($user, $account, $product);
	}

/**
 * Check if the NewRelic PHP extension is loaded
 *
 * @return boolean
 */
	public function hasNewRelic() {
		return extension_loaded('newrelic');
	}

}
