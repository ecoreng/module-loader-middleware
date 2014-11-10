<?php

namespace ecoreng\Test\Module;

class TestBootstrap extends \ecoreng\Module\Bootstrap
{

    public function setup(array $settings)
    {
        parent::setup($settings);
        $app = $this->app;
        // is Slim 3.* ??
        if (class_exists('\\Slim\\App')) {
            $app['service'] = function (\Pimple\Container $c) use ($settings) {
                if (count($settings) > 1) {
                    return $settings;
                } else {
                    return true;
                }
            };
        } else {
            $app->service = function () use ($settings) {
                if (count($settings) > 1) {
                    return $settings;
                } else {
                    return true;
                }
            };
        }
    }
}
