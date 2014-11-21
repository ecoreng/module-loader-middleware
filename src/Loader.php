<?php

namespace ecoreng\Module;

use \ecoreng\Module\BootstrapInterface;
use \Composer\Autoload\ClassLoader;
use \Slim\Middleware;

class Loader extends Middleware
{

    /**
     * Modules Configuration array
     * 
     * @var array
     */
    protected $modulesSetup;

    /**
     * Autoloader instance
     * 
     * @var \Composer\Autoload\ClassLoader
     */
    protected $autoloader;

    /**
     * Container key to set with loaded modules
     * 
     * @var string
     */
    protected $containerKey = 'modules';

    /**
     * Init the Module loader with settings
     * 
     * @param array $modulesSetup
     * @param ClassLoader $autoloader
     */
    public function __construct(array $modulesSetup, ClassLoader $autoloader, $containerKey = 'modules')
    {
        $this->modulesSetup = $modulesSetup;
        $this->autoloader = $autoloader;
        $this->containerKey = $containerKey;
    }

    /**
     * Entry point for the middleware
     * 
     * @throws \Exception
     */
    public function call()
    {
        $this->load();
        $this->next->call();
    }

    /**
     * Takes an array populated with and loads the bootstrap 
     * class defined as a string or as a subkey named "class", it passes
     * all the other keys as "settings" to the bootstrap
     * 
     * ex (both are valid):
     * [
     *      example_module => ["class" => "\ExampleCompany\Example\ExampleModule", "foo" => "bar"],
     *      example_module2 => "ExampleCompany\Example\ExampleModule"
     * ]
     * 
     * @param array $modulesSetup
     */
    public function load()
    {

        $app = $this->getApplication();
        if (self::iterable($this->modulesSetup)) {
            $modules = [];
            foreach ($this->modulesSetup as $moduleName => $moduleSettings) {
                if (self::loadable($moduleSettings, $moduleName)) {
                    $moduleSettings = self::normalizeSettings($moduleSettings);
                    $bootstrap = self::getBootstrapClass($moduleSettings);
                    if (class_exists($bootstrap)) {
                        $bootstrapInstance = new $bootstrap($app, $this->autoloader);
                        if (!($bootstrapInstance instanceof BootstrapInterface)) {
                            throw new \Exception(
                            'Boostrap class ' . get_class($bootstrapInstance)
                            . ' does not implement \ecoreng\Module\BootstrapInterface'
                            );
                        }
                        $modules[$bootstrapInstance->getName()] = [
                            'bootstrap' => $bootstrap,
                            'instance' => $bootstrapInstance
                        ];
                        $bootstrapInstance->setup($moduleSettings);
                    } else {
                        throw new \Exception('Bootstrap class: ' . $bootstrap . ' is not loadable.');
                    }
                }
            }
            $this->setModules($modules);
        }
    }

    public function setModules(array $modules)
    {
        // is Slim 3.* ??
        if (class_exists('\\Slim\\App')) {
            $this->getApplication()[$this->containerKey] = $modules;
        } else {
            $this->getApplication()->{$this->containerKey} = $modules;
        }
    }

    /**
     * Returns the name of the class to instantiate or null (from config array)
     * 
     * @param array $moduleSettings
     * @return mixed
     */
    protected static function getBootstrapClass($moduleSettings)
    {
        if (is_array($moduleSettings)) {
            if (array_key_exists('class', $moduleSettings)) {
                return $moduleSettings['class'];
            } else {
                return null;
            }
        } else {
            if (is_string($moduleSettings)) {
                return $moduleSettings;
            } else {
                return null;
            }
        }
    }

    /**
     * Returns whether or not $modulesSetup needs to be iterated to load
     * modules and settings from it
     * 
     * @param mixed $modulesSetup
     * @return boolean
     */
    protected static function iterable($modulesSetup)
    {
        if (count($modulesSetup) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true if a setup class is extractable from the moduleSettings
     * or throws BadMethodCallException otherwise
     * 
     * @param array $moduleSettings
     * @param string $moduleName
     * @return boolean
     * @throws \BadMethodCallException
     */
    protected function loadable($moduleSettings, $moduleName)
    {
        if (self::getBootstrapClass($moduleSettings) === null) {
            throw new \Exception('Missing bootstrap class for module key: ' . $moduleName);
        } else {
            return true;
        }
    }

    /**
     * Normalizes moduleSettings to at least be an array containing one key
     * named 'class'
     * 
     * @param array $moduleSettings
     * @return array
     */
    protected static function normalizeSettings($moduleSettings)
    {
        if (is_array($moduleSettings)) {
            return $moduleSettings;
        } else {
            return ['class' => $moduleSettings];
        }
    }

}
