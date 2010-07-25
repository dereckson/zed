<?php

/**
 * Geo octocube class.
 * 
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-02-25  3:33    DcK
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
 * Geo octocube class
 *
 * An octocube is a cube divided in 8 parts (sliced in two in x, y and z)
 *
 * Its coordinate (0, 0, 0) is the octocube centrum.
 */
class GeoOctocube {
    /**
     * Gets the sector from the (x, y, z) specified coordinates
     *
     * Sector will be:
     * <code>
     * //             _____ _____ 
     * //           /  5  /  6  /|
     * //          /- - -/- - -/ |
     * //         /_____/____ /| |    
     * //        |     |     | |/|
     * //        |  7  |  8  | / | 2  
     * //        |_____|_____|/| |
     * //        |     |     | |/
     * //        |  3  |  4  | /
     * //        |_____|_____|/ 
     * </code>
     *
     * @param int $x the x coordinate
     * @param int $y the y coordinate
     * @param int $z the z coordinate
     * @return int the number of the sector (0 if x = y = z 0 ; otherwise, 1 to 8)
     */
    static function get_sector ($x, $y, $z) {
        //Cube centrum
        if ($x == 0 && $y == 0 && $z == 0) return 0;
        
        //One of the 8 cubes
        $sector = 1;
        if ($x >= 0) $sector++;      //we're at right
        if ($y < 0)  $sector += 2;  //we're at bottom
        if ($z >= 0) $sector += 4; //we're on the top layer
        
        return $sector;
    }
    
    /**
     * Gets the sector from the (x, y, z) specified coordinates
     * @see get_sector
     * 
     * @param GeoPoint3D $pt the x, y, z coordinates
     * @return int the number of the sector (0 if x = y = z 0 ; otherwise, 1 to 8)
     */
    static function get_sector_from_point3D ($pt) {
        return get_sector($pt->x, $pt->y, $pt->z);
    }
}

?>


