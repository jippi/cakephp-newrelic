# CakePHP <3 NewRelic

You can modify your files like this

## Things included

- NewRelic.NewRelic task
- NewRelic.NewRelic component
- NewRelicTrait trait
- NewRelic.NewRelic

### Console

Include this snippet in `app/Console/AppShell.php`

```php
	public function startup() {
		$this->NewRelic = $this->Tasks->load('NewRelic.NewRelic');
		$this->NewRelic->setName($this);
		$this->NewRelic->start();
		$this->NewRelic->parameter('params', json_encode($this->params));
		$this->NewRelic->parameter('args', json_encode($this->args));

		parent::startup();
	}
```

### Controller

Simply add `NewRelic.NewRelic` do your `$components` list

## app/webroot/index.php

Add this in top of your file before `define('DS', 'DIRECTORY_SEPARATOR')`

```php
<?php
require_once dirname(dirname(__DIR__)) . '/vendors/autoload.php';

if (extension_loaded('newrelic')) {
	$appType = 'app';
	$appName = 'web';

	if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
		$appName = 'admin';
	}

	define('NEW_RELIC_APP_NAME', sprintf('%1$s - %2$s - %3$s', 'production', $appType, $appName));

	newrelic_set_appname(NEW_RELIC_APP_NAME);
	newrelic_background_job(false);
	newrelic_capture_params(true);
}

// Rest of your index.php here
```

## app/Console/cake.php

```php
<?php
require_once dirname(dirname(__DIR__)) . '/vendors/autoload.php';

if (extension_loaded('newrelic')) {
	define('NEW_RELIC_APP_NAME', sprintf('%s - app - cli', 'production'));
	newrelic_set_appname(NEW_RELIC_APP_NAME);
	newrelic_background_job(true);
	newrelic_capture_params(true);
}

// Rest of your cake.php file here
```
