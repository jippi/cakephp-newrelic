# CakePHP <3 NewRelic

You can modify your files like this

## Things included

- NewRelic.NewRelic task
- NewRelic.NewRelic component
- NewRelicTrait trait
- NewRelic.NewRelic

## Installation

```
composer require jippi/cakephp-newrelic
```


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

Simply add `NewRelic.NewRelic` to your `$components` list

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

### Remark if using > CakePHP 3.3.0 and using middleware
If you utilise CakePHP middlewares from https://book.cakephp.org/3.0/en/controllers/middleware.html 

You can use the supplied `NewRelicErrorHandlerMiddleware` placed in `NewRelic\Middleware\NewRelicErrorHandlerMiddleware` which extends the built in `Cake\Error\Middleware\ErrorHandlerMiddleware`. By using this you'll get the NewRelic working *and* have default CakePHP behavior.

Example:

```php
<?php

namespace App;

use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * Setup the middleware your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware.
     */
    public function middleware($middleware)
    {
        $middleware
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(\NewRelic\Middleware\NewRelicErrorHandlerMiddleware::class)
            // Handle plugin/theme assets like CakePHP normally does.
            ->add(AssetMiddleware::class)
            // Apply routing
            ->add(RoutingMiddleware::class);
	    
        return $middleware;
    }
}

?>
```
