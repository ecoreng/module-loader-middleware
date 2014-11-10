<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

// set autoloader in variable
$autoloader = require('../vendor/autoload.php');

// Set a basic autoloader using a folder called src where this file is
$autoloader->addPsr4(null, dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR, true);

// init app
$app = new \Slim\App(['debug' => true]);

// config array
$setup = [
    'test' => '\\ExampleCo\\Example\\TestBootstrap',
    'test2' => [
        'class' => '\\ExampleCo\\Example\\TestBootstrapWithParams',
        'foo' => 'bar',
        'test' => 'param'
        ]
    ];

// add middleware to app
$app->add(new \ecoreng\Module\Loader($setup, $autoloader));

// magic
$app->run();
