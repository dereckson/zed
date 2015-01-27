<?php

/**
 * Unit testing : class GeoOctocube
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  Tests
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

require_once('../../includes/geo/octocube.php');

/**
 * Test cases for the class GeoPlace
 */
class GeoOctocubeTest extends PHPUnit_Framework_TestCase {
    /**
     * Tests the GeoPlace::is_valid_local_location($local_location) method.
     */
    public function testGetSector () {
        //Testing HyperShip Tower T2C3 format
        $this->assertTrue(GeoOctocube::get_sector(0, 0, 0) == 0);
        $this->assertTrue(GeoOctocube::get_sector(-10, 6, -4) == 1);
        $this->assertTrue(GeoOctocube::get_sector(10, 6, -4) == 2);
        $this->assertTrue(GeoOctocube::get_sector(-10, -6, -4) == 3);
        $this->assertTrue(GeoOctocube::get_sector(10, -6, -4) == 4);
        $this->assertTrue(GeoOctocube::get_sector(-10, 6, 4) == 5);
        $this->assertTrue(GeoOctocube::get_sector(10, 6, 4) == 6);
        $this->assertTrue(GeoOctocube::get_sector(-10, -6, 4) == 7);
        $this->assertTrue(GeoOctocube::get_sector(10, -6, 4) == 8);
    }
}
