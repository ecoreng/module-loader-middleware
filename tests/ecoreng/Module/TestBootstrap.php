<?php

namespace ecoreng\Test\Module;

class TestBootstrap extends \ecoreng\Module\Bootstrap
{

    public function setup(array $settings)
    {
        parent::setup($settings);
        $app = $this->app;
        $app['service'] = function (\Pimple\Container $c) use ($settings) {
            if (count($settings) > 1) {
                return $settings;
            } else {
                return true;
            }
        };
    }
}
