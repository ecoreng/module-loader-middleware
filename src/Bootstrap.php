<?php

namespace ecoreng\Module;

use \ecoreng\Module\BootstrapInterface;
use \Slim\App;
use \Composer\Autoload\ClassLoader;

/**
 * Parent class that adds default and useful functionallity
 * to a boostrap to be easily configurable and loadable
 */
abstract class Bootstrap implements BootstrapInterface
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
        $fqn = get_class($this);
        $fqn = explode('\\', $fqn);
        array_pop($fqn);
        $fqn = implode('_', $fqn);
        return strtolower($fqn);
    }

    public function setup(array $settings)
    {
        $this->autoloader->addPsr4(
            $this->getNamespace(),
            $this->getClassFolder()
        );
    }

    public function getNamespace()
    {
        $obj = new \ReflectionClass($this);
        return $obj->getNamespaceName() . '\\';

    }

    public function getClassFolder()
    {
        $obj = new \ReflectionClass($this);
        $folder = dirname($obj->getFileName());
        return $folder . DIRECTORY_SEPARATOR;
    }
}
