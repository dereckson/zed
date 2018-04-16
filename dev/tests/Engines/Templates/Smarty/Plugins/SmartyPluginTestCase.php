<?php
declare(strict_types=1);

namespace Zed\Tests\Engines\Templates\Smarty\Plugins;

use PHPUnit\Framework\TestCase;

abstract class SmartyPluginTestCase extends TestCase {

    protected function requirePlugin (string $type, string $name) {
        require_once($this->getPluginPath($type, $name));
    }

    private function getPluginPath (string $type, string $name) {
        return dirname(__DIR__, 6) // application root directory
               . '/Engines/Templates/Smarty/Plugins/'
               . "$type.$name.php";
    }

}
