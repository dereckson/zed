<?php
declare(strict_types=1);

namespace Zed\Tests\Engines\Templates\Smarty\Plugins;

class RomanizeModifierTest extends SmartyPluginTestCase {

    public function setUp () : void {
        $this->requirePlugin('modifier', 'romanize');
    }

    public function testPluginWithCorrectValueAsInteger () {
        $this->assertEquals('iv', smarty_modifier_romanize(4));
    }

    public function testPluginWithCorrectValueAsString () {
        $this->assertEquals('iv', smarty_modifier_romanize('4'));
    }

    public function testPluginWithNonNumeric () {
        $this->assertEquals('quux', smarty_modifier_romanize('quux'));
    }

    public function testPluginWithNegativeNumber () {
        $this->assertSame('-4', smarty_modifier_romanize(-4));
    }

}
