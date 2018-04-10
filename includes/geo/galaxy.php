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
 * 0.1    2010-02-08 14:02    Initial version [DcK]
 * 0.2    2010-07-25  9:20    Spherical conversion, get objects
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
 * @todo create a unit testing file dev/tests/GeoGalaxyTest.php
 * @todo add unit testing for the normalizeAngle method in dev/tests/GeoGalaxyTest.php
 */
class GeoGalaxy {
    /*
     * ----------------------------------------------------------------------- *
     *  Objects fetchers
     * ----------------------------------------------------------------------- *
     */

    /**
     * Gets all the coordinates of the objects in the galaxy.
     *
     * @return array An array of array. Each item is  [string object_name, string object_type, GeoPoint3D coordinates]
     */
    static function getCoordinates () {
        global $db;
        $sql = "SELECT * FROM geo_coordinates";
        if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Can't query geo_coordinates view.", '', __LINE__, __FILE__, $sql);

        $objects = [];
        while ($row = $db->sql_fetchrow($result)) {
            //Demios  ship        xyz: [-50, 30, 40]
            //Kaos	  asteroid    xyz: [150, -129, 10]
            $objects[] = [$row[0], $row[1], GeoPoint3D::fromString($row[2])];
        }
        return $objects;
    }

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
    static function normalizeAngle ($angle) {
        while ($angle < 0) {
            $angle += 2 * M_PI;
        }
        while ($angle >= 2 * M_PI) {
            $angle -= 2 * M_PI;
        }
        return $angle;
    }

    /**
     * Converts (x, y, z) cartesian to (ρ, φ, θ) spherical coordinates
     *
     * The algo used is from http://fr.wikipedia.org/wiki/Coordonn%C3%A9es_sph%C3%A9riques#Relation_avec_les_autres_syst.C3.A8mes_de_coordonn.C3.A9es_usuels
     *
     * @param int $x the x coordinate
     * @param int $y the y coordinate
     * @param int $z the z coordinate
     * @return array an array of 3 floats number, representing the (ρ, φ, θ) spherical coordinates
     */
    static function cartesianToSpherical ($x, $y, $z) {
        $rho = sqrt($x * $x + $y * $y + $z * $z);    //ρ = sqrt(x² + y² + z²)
        $theta= acos($z / $rho);                    //φ = acos z/φ
        $phi = acos($x / sqrt($x * $x + $y * $y)); //θ = acos x / sqrt(x² + y²)
        if (y < 0) $phi = 2 * M_PI - $phi;        //∀ y < 0     θ = 2π - θ

        return [round($rho, 2), round(rad2deg($theta), 2), round(rad2deg($phi), 2)];
    }

    /**
     * Converts (x, y, z) cartesian to (ρ, φ, θ) spherical coordinates
     *
     * The algo used is from http://www.phy225.dept.shef.ac.uk/mediawiki/index.php/Cartesian_to_polar_conversion
     *
     * @param int $x the x coordinate
     * @param int $y the y coordinate
     * @param int $z the z coordinate
     * @return array an array of 3 floats number, representing the (ρ, φ, θ) spherical coordinates
     */
    static function cartesianToSphericalAlternative ($x, $y, $z) {
        $rho = sqrt($x * $x + $y * $y + $z * $z); //ρ = sqrt(x² + y² + z²)
        $theta= atan2($y, $x);                    //φ = atan2 $y $x
        $phi = acos($z / $rho);                   //θ = acos z/φ

        return [round($rho, 2), round(rad2deg($theta), 2), round(rad2deg($phi), 2)];
    }
}
