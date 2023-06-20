<?php
declare(strict_types=1);

namespace Zed\Tests\Engines\Templates\Smarty\Plugins;

use Keruald\Database\Engines\MockDatabaseEngine;

use Zed\Models\Geo\Location;

class SectorModifierTest extends SmartyPluginTestCase {

    const QUERY_RESULTS = [
        // Queries for SectorModifierTest

        "SELECT * FROM geo_places WHERE CONCAT('B', body_code, place_code) LIKE 'B00001002'" => [[
            "place_id" => 2,
            "body_code" => "00001",
            "place_code" => "002",
            "place_name" => "Core",
            "place_description" => "",
            "place_status" =>  null,
            "location_local_format" =>  "/^\\(\\-?[0-9]+( )*,( )*\\-?[0-9]+( )*,( )*\\-?[0-9]+\\)$/",
        ]],
        "SELECT * FROM geo_bodies WHERE body_code = '00001'" => [[
            "body_code" => 00001,
            "body_name" => "Hypership",
            "body_status" => "hypership",
            "body_location" => "xyz: [-5,-35,10]",
            "body_description" => null,
        ]],
    ];

    public function setUp () : void {
        define('TABLE_BODIES', 'geo_bodies');
        define('TABLE_PLACES', 'geo_places');

        $this->requirePlugin('modifier', 'sector');
    }

    public function testPluginWithValidData () {
        $db = (new MockDatabaseEngine())
            ->withQueries(self::QUERY_RESULTS);

        $location = new Location($db, "B00001002", "(4,2,7)");
        $this->assertEquals("6", smarty_modifier_sector($location));
    }

}
