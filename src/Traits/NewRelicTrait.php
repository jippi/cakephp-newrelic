<?php
namespace NewRelic\Traits;

use NewRelic\Lib\NewRelic;
use Cake\Console\Shell;
use Cake\Network\Request;
use Exception;

trait NewRelicTrait {

/**
 * The transaction name to use
 *
 * @var string
 */
	protected $_newrelicTransactionName;

/**
 * Set the transaction name
 *
 * If `$name` is a Shell instance, the name will
 * automatically be derived based on best practices
 *
 * @param string|Shell $name
 */
	public function setName($name) {
		if ($name instanceof Shell) {
            $name = $this->_deriveNameFromShell($name);
		}

		if ($name instanceof Request) {
			$name = $this->_deriveNameFromRequest($name);
		}

		$this->_newrelicTransactionName = $name;
	}

/**
 * Get the name
 *
 * @return string
 */
	public function getName() {
		return $this->_newrelicTransactionName;
	}

/**
 * Change the application name
 *
 * @param  string $name
 * @return void
 */
	public function applicationName($name) {
		NewRelic::applicationName($name);
	}

/**
 * Start a NewRelic transaction
 *
 * @param  null|string $name
 * @return void
 */
	public function start($name = null) {
		NewRelic::start($this->_getTransactionName($name));
	}

/**
 * Stop a transaction
 *
 * @return void
 */
	public function stop($ignore = false) {
		NewRelic::stop($ignore);
	}

/**
 * Ignore current transaction
 *
 * @return void
 */
	public function ignoreTransaction() {
		NewRelic::ignoreTransaction();
	}

/**
 * Ignore current apdex
 *
 * @return void
 */
	public function ignoreApdex() {
		NewRelic::ignoreApdex();
	}

/**
 * Add custom parameter to transaction
 *
 * @param  string $key
 * @param  scalar $value
 * @return void
 */
	public function parameter($key, $value) {
		NewRelic::parameter($key, $value);
	}

/**
 * Add custom metric
 *
 * @param  string $key
 * @param  float $value
 * @return void
 */
	public function metric($key, $value) {
		NewRelic::metric($key, $value);
	}

/**
 * capture params
 *
 * @param  boolean $capture
 * @return void
 */
	public function captureParams($capture) {
		NewRelic::captureParams($capture);
	}

/**
 * Add custom tracer method
 *
 * @param string $method
 */
	public function addTracer($method) {
		NewRelic::addTracer($method);
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
		NewRelic::user($user, $account, $product);
	}

/**
 * Send an exception to New Relic
 *
 * @param  Exception $e
 * @return void
 */
	public function sendException(Exception $e) {
		NewRelic::sendException($e);
	}

/**
 * Get transaction name
 *
 * @param  string $name
 * @return string
 */
	protected function _getTransactionName($name) {
		if (is_string($name)) {
			return $name;
		}

		return $this->_newrelicTransactionName;
	}

/**
 * Derive the transaction name
 *
 * @param  Shell $name
 * @return string
 */
	protected function _deriveNameFromShell(Shell $shell) {
		$name = [];

		if ($shell->plugin) {
			$name[] = $shell->plugin;
		}

		$name[] = $shell->name;
		$name[] = $shell->command;

		return join('/', $name);
	}

	/**
 * Compute name based on request information
 *
 * @param  CakeRequest $request
 * @return string
 */
	protected function _deriveNameFromRequest(Request $request) {
		$name = [];

		if ($request->getParam('prefix')) {
			$name[] = $request->getParam('prefix');
		}

		if ($request->getParam('plugin')) {
			$name[] = $request->getParam('plugin');
		}

		$name[] = $request->getParam('controller');
		$name[] = $request->getParam('action');

		$name = join('/', $name);

		if ($request->getParam('ext')) {
			$name .= '.' . $request->getParam('ext');
		}

		return $name;
	}

}
