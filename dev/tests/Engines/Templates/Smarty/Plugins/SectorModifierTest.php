<?php
declare(strict_types=1);

namespace Zed\Tests\Engines\Templates\Smarty\Plugins;

use GeoLocation;

require_once 'includes/geo/location.php';

class SectorModifierTest extends SmartyPluginTestCase {

    public function setUp () : void {
        $this->requirePlugin('modifier', 'sector');
    }

    public function testPluginWithValidData () {
        $location = new GeoLocation("B00001002", "(4,2,7)");
        $this->assertEquals("6", smarty_modifier_sector($location));
    }

}
