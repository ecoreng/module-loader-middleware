<?php

namespace ecoreng\Test\Module;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $appName = class_exists('\\Slim\\App') ? '\\Slim\\App' : '\\Slim\\Slim';

        $this->autoloader = new \Composer\Autoload\ClassLoader;
        $this->bootstrap = new \ecoreng\Test\Module\TestBootstrap(
            new $appName,
            $this->autoloader
        );
    }

    public function testGetName()
    {
        $this->assertEquals('ecoreng_test_module', $this->bootstrap->getName());
    }

    public function testGetNamespace()
    {
        $this->assertEquals(
            'ecoreng\\Test\\Module\\',
            $this->bootstrap->getNamespace()
        );
    }

    public function testGetClassFolder()
    {
        $expected = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $this->assertEquals(
            $expected,
            $this->bootstrap->getClassFolder()
        );
    }

    public function testSetAutoloader()
    {
        $appName = class_exists('\\Slim\\App') ? '\\Slim\\App' : '\\Slim\\Slim';

        $mockAutoloader = $this->getMock('\\Composer\\Autoload\\ClassLoader', ['addPsr4']);
        $mockAutoloader->expects($this->once())
                ->method('addPsr4')
                ->with(
                    $this->bootstrap->getNamespace(),
                    $this->bootstrap->getClassFolder()
                );
        $bootstrap = new \ecoreng\Test\Module\TestBootstrap(
            new $appName,
            $mockAutoloader
        );
        $bootstrap->setup([]);
    }

    public function testWrongAppInstance()
    {
        try {
            $this->bootstrap = new \ecoreng\Test\Module\TestBootstrap(
                new \stdClass(),
                $this->autoloader
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Exception', $e);
        }
    }
}
