<?php

namespace ExampleCo\Example;

use \ecoreng\Module\BootstrapInterface;
use \Slim\App;
use \Composer\Autoload\ClassLoader;

class TestBootstrap implements BootstrapInterface
{

    protected $app;
    protected $autoloader;

    public function __construct(App $app, ClassLoader $autoloader)
    {
        $this->autoloader = $autoloader;
        $this->app = $app;
    }

    public function getName()
    {
        return 'testModule';
    }

    public function setup(array $settings)
    {
        $app = $this->app;
        
        // we have access to the full app including
        // dependencies and container
        $app['dependency'] = $app->factory(function () {
            return rand(1, 100);
        });
        
        // we can set routes and pass the $app instance
        $app->get('/', function () use ($app) {
            echo 'Hello from a route declared in a module!<br>'
                . 'Now a random number: '
                . $app['dependency'] . '<br> and another one: '
                . $app['dependency'] . '<br><br>';
        })->name('testUrl');
    }
}
