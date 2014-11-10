<?php
namespace ecoreng\Module;

use \Slim\App;
use \Composer\Autoload\ClassLoader;

/**
 * Defines the contract for a Bootstrap to be loadable
 */
interface BootstrapInterface
{
    public function __construct(App $app, ClassLoader $autoloader);
    public function getName();
    public function setup(array $settings);
}
