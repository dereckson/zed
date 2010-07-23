<?php

/**
 * Geo galaxy  class.
 * 
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * A 3D grid of objects
 *
 * 0.1    2010-02-08 14:02    DcK
 *
 * @package     Zed
 * @subpackage  Geo
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * Geo galaxy class
 *
 * This class provides methods to convert coordinate polars.
 *
 * @todo add a cartesian_to_polar method
 * @todo add a static method to get a grid of all the galaxy objects, with their x y z representation ; that will be useful to add in API, for a javascript galaxy viewer.
 *
 * @todo create a unit testing file dev/tests/GeoGalaxyTest.php
 * @todo add unit testing for the normalize_angle method in dev/tests/GeoGalaxyTest.php
 * @todo add unit testing for the polar_to_cartesian method
 */
class GeoGalaxy {
    
    /*
     * ----------------------------------------------------------------------- *
     *  Helper methods - math
     * ----------------------------------------------------------------------- *
     */
    
    /**
     * Normalizes an angle, so 0 =< angle < 2 PI
     * 
     * @param float $angle angle in radians (use deg2rad() if you've degrees)
     * @return an angle in the 0 =< angle < 2 PI interval
     */
    static function normalize_angle ($angle) {
        while ($angle < 0) {
            $angle += 2 * M_PI;
        }
        while ($angle >= 2 * M_PI) {
            $angle -= 2 * M_PI;
        }
        return $angle;
    }
    
    /*
     * Converts polar coordinates in cartesian x y coordinates
     * @param float $angle angle in radians (use deg2rad() if you've degrees)
     * @param float $height height
     * @return array an array of 2 float items: x, y
     */
    static function polar_to_cartesian ($angle, $height) {
        //A story of numbers
        if ($height < 0) {
            //Adds 180° and gets absolute value
            $height *= -1;
            $angle + M_PI;
        }
        $x = abs(sin($angle)) . $height;
        $y = abs(cos($angle)) . $height;
        
        //And now, the sign
        
        
        //Returns our coordinates
        return array($x, $y);
    }
    
}