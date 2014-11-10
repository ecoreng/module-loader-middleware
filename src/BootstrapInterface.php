<?php
namespace ecoreng\Module;

use \Composer\Autoload\ClassLoader;

/**
 * Defines the contract for a Bootstrap to be loadable
 */
interface BootstrapInterface
{
    public function __construct($app, ClassLoader $autoloader);
    public function getName();
    public function setup(array $settings);
}
