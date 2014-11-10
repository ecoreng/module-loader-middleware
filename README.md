Slim Module Loader Middleware
=======================

Loads modules (bootstrap classes) from a configuration array passed at the middleware's instantiation. It requires ``composer`` as it passes the autoloader instance to the bootstrap file so the module creator is able to configure the ``ClassLoader`` directly. The bootstrap class also gets an instance of the``Slim\App`` beign used so it's able to configure services, routes and everything else. All the loaded module bootstrap instances get stored in a service in the app's container (``$app['modules']``).

## Why is this useful? ##

You can encapsulate functionality in your application

## Usage ##
```
$autoloader = require('../vendor/autoload.php');

// basic autoloader setup for example folder configuration (read more below)
$autoloader->addPsr4(null, dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR, true);

// Slim 3.*
$app = new \Slim\App;

// Config array with bootstrap classes
$setup = [
    'exampleModule' => '\\ExampleCo\\Example\\TestBootstrap',
    'myModule' => [
        'class' => '\\ExampleCo\\Example2\\TestBootstrapWithParams',
				'param1' => 'foo',
				'anotherParam' => 'bar'
    ]
];

// Add middleware
$app->add(new \ecoreng\Module\Loader($setup, $autoloader));

// You can use Slim normally here before all the modules are loaded
$app->get('/test', function () {echo 'just a test';});

// Run
$app->run();

```
## Things to Note ##
Before you can load the bootstraps you will have to decide on a folder structure and add a basic autoloader configuration, for instance for the following folder structure:
```
-\
 |-index.php (front controller)
 |-src\
 | |-ExampleCo
 | | |-module1\
 | | | |-M1Bootstrap.php
 | | | |-Controllers\
 | | | |-Models\
 ...

```

you would configure your autoloader like this:
```
$autoloader->addPsr4(null, dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR, true);
```
This would add an autoloader layer (uh?) in the folder ``src`` where your front controller is. Don't worry about this beign too simple for your module, as the bootstrap class gets the autoloader instance so you are able to configure it the way you want inside your module.

## Examples ##
Check out the example @ ``example\index.php``

## Contribute ##
Pull Requests welcome, add some tests if necessary and adhere to PSR-2 coding style.
