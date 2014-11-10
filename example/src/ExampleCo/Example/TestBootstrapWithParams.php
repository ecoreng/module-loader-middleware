<?php

namespace ExampleCo\Example;

class TestBootstrapWithParams extends \ecoreng\Module\Bootstrap
{

    public function setup(array $settings)
    {
        // configure autoloader automatically for this plugin
        parent::setup($settings);
        echo 'This module bootstrap was passed the following settings:<br>';
        echo json_encode($settings);
        echo '<br>But it still decided to do nothing with them.. shameful bootstrap.<br><br>';
    }
}
