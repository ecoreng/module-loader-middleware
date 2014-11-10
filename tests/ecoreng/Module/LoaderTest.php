<?php

namespace ecoreng\Test\Module;

class LoaderTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $appName = class_exists('\\Slim\\App') ? '\\Slim\\App' : '\\Slim\\Slim';

        $this->autoloader = new \Composer\Autoload\ClassLoader;
        $this->app = new $appName;
        $this->nextMiddleware = new \ecoreng\Test\Module\TestMiddleware;
    }

    public function setupLoader($settings)
    {
        $loader = new \ecoreng\Module\Loader($settings, $this->autoloader);
        $loader->setApplication($this->app);
        $loader->setNextMiddleware($this->nextMiddleware);
        return $loader;
    }

    public function testShortSyntaxInit()
    {
        $loader = $this->setupLoader([
            'testDirectClass' => '\\ecoreng\\Test\\Module\\TestBootstrap'
        ]);
        $loader->call();
        $service = class_exists('\\Slim\\App') ? $this->app['service'] : $this->app->service;
        $this->assertEquals(true, $service);
    }

    public function testShortLongInit()
    {
        $loader = $this->setupLoader([
            'testDirectClass' => [
                'class' => '\\ecoreng\\Test\\Module\\TestBootstrap'
        ]]);
        $loader->call();
        $service = class_exists('\\Slim\\App') ? $this->app['service'] : $this->app->service;
        $this->assertEquals(true, $service);
    }

    public function testWithParams()
    {
        $loader = $this->setupLoader([
            'testDirectClass' => [
                'class' => '\\ecoreng\\Test\\Module\\TestBootstrap',
                'foo' => 'bar'
        ]]);
        $loader->call();
        $service = class_exists('\\Slim\\App') ? $this->app['service'] : $this->app->service;
        $settings = $service;
        $this->assertEquals('bar', $settings['foo']);
    }

    public function testSettingsNotIterable()
    {
        $loader = $this->setupLoader([]);
        $appOriginal = clone($this->app);
        $loader->call();
        $this->assertEquals($appOriginal, $this->app);
    }

    public function testMissingClass()
    {
        $loader = $this->setupLoader(['test' => ['foo' => 'bar']]);
        $appOriginal = clone($this->app);
        try {
            $loader->call();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\\Exception', $e);
        }
    }

    public function testNonExistentClass()
    {
        $loader = $this->setupLoader(['test' => '\\NonExistent']);
        $appOriginal = clone($this->app);
        try {
            $loader->call();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\\Exception', $e);
        }
    }

    public function testBootstrapNotInterface()
    {
        $loader = $this->setupLoader([
            'test' => '\\ecoreng\\Test\\Module\\TestBadBootstrap'
        ]);
        $appOriginal = clone($this->app);
        try {
            $loader->call();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\\Exception', $e);
        }
    }
}
