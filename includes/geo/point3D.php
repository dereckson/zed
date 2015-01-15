<?php

/**
 * Geo point 3D class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-02-23 14:14    DcK
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

require_once("galaxy.php");

/**
 * Geo point 3D class.
 *
 * This class represents a x, y, z point.
 *
 * It implements IteratorAggregate to allow the foreach instruction
 * on a GeoPoint3D object:
 *
 * <code>
 * $point = new GeoPoint3D(17, 24, -6);
 * foreach ($point as $axis => $coordinate) {
 *     echo "\n\t$axis = $coordinate";
 * }
 * //This will output:
 * //    x = 17
 * //    y = 24
 * //    z = -6
 * </code>
 *
 * The point 3D representation is xyz: [x, y, z] ; you can print it as a string
 * and get this format:
 *
 * <code>
 * $point = new GeoPoint3D(17, 24, -6);
 * echo (string)$point;   //will output xyz: [17, 24, -6]
 * </code>
 *
 */
class GeoPoint3D implements IteratorAggregate {
    //
    // x, y, z public properties
    //

    /**
     * the x coordinate
     *
     * @var integer
     */
    public $x;

    /**
     * the y coordinate
     *
     * @var integer
     */
    public $y;

    /**
     * the z coordinate
     *
     * @var integer
     */
    public $z;

    //
    // constructor / toString
    //

    /**
     * Initializes a new instance of GeoPoint3D class
     *
     * @param int $x the x coordinate
     * @param int $y the y coordinate
     * @param int $z the z coordinate
     */
    function __construct ($x, $y, $z) {
        $this->x = (int)$x;
        $this->y = (int)$y;
        $this->z = (int)$z;
    }

    /**
     * Parses a string expression ang gets a GeoPoint3D object
     *
     * Formats recognized are:
     *      - xyz: [x, y, z]
     *      - (x, y, z)
     *
     * @param string $expression the expression to parse
     * @return GeoPoint3D If the specified expression could be parsed, a GeoPoint3D instance ; otherwise, null.
     */
    static function fromString ($expression) {
        if (string_starts_with($expression, 'xyz:', false)) {
            $pos1 = strpos($expression, '[', 4) + 1;
            $pos2 = strpos($expression, ']', $pos1);
            if ($pos1 > -1 && $pos2 > -1) {
                $expression = substr($expression, $pos1, $pos2 - $pos1);
                $xyz = explode(',', $expression, 3);
                return new GeoPoint3D($xyz[0], $xyz[1], $xyz[2]);
            }
        } elseif ($expression[0] = '(') {
            $expression = substr($expression, 1, -1);
            $xyz = explode(',', $expression, 3);
            return new GeoPoint3D($xyz[0], $xyz[1], $xyz[2]);
        }
        return null;
    }

    /**
     * Returns a string representation of the point coordinates.
     *
     * @param $format the format to use
     * @return string a string representation of the coordinates
     *
     * To print a "xyz: [10, 20, 40]" string:
     *  $point = new GeoPoint3D(10, 20, 40);
     *  echo $point->sprintf("xyz: [%d, %d, %d]");
     *
     *  //Of course, you could have (implicitely) use the __toString method:
     *  echo $point;
     *
     * To print a (10, 20, 40) string:
     *  $point = new GeoPoint3D(10, 20, 40);
     *  echo $point->sprintf("(%d, %d, %d)");
     */
    function sprintf ($format) {
        return sprintf($format, $this->x, $this->y, $this->z);
    }

    /**
     * Returns a xyz: [x, y, z] string representation of the point coordinates.
     *
     * @return string a xyz: [x, y, z] string representation of the coordinates
     */
    function __toString () {
        return $this->sprintf("xyz: [%d, %d, %d]");
    }

    /**
     * Determines if this point is equal to the specified point.
     *
     * @param GeoPoint3D $point The point to compare
     * @return bool true if the two points are equal ; otherwise, false.
     */
    function equals ($point) {
        return ($this->x == $point->x) && ($this->y == $point->y) && ($this->z == $point->z);
    }

    //
    // Math
    //

    /**
     * Gets the (ρ, φ, θ) spherical coordinates from the current x, y, z cartesian point
     *
     * The algo used is from http://fr.wikipedia.org/wiki/Coordonn%C3%A9es_sph%C3%A9riques#Relation_avec_les_autres_syst.C3.A8mes_de_coordonn.C3.A9es_usuels
     *
     * @return array an array of 3 floats number, representing the (ρ, φ, θ) spherical coordinates
     */
    function to_spherical () {
        return GeoGalaxy::cartesian_to_spherical($this->x, $this->y, $this->z);
    }

    /**
     * Gets the (ρ, φ, θ) spherical coordinates from the current x, y, z cartesian point
     *
     * The algo used is from http://www.phy225.dept.shef.ac.uk/mediawiki/index.php/Cartesian_to_polar_conversion
     *
     * @return array an array of 3 floats number, representing the (ρ, φ, θ) spherical coordinates
     */
    function to_spherical2 () {
        return GeoGalaxy::cartesian_to_spherical2($this->x, $this->y, $this->z);
    }

    /**
     * Translates the center and rescales.
     *
     * This method allow to help to represent coordinate in a new system
     *
     * This method is used to represent Zed objects in dojo with the following
     * parameters:
     * <code>
     * $pointKaos = GeoPoint3D(800, 42, 220);
     * $pointKaos->translate(500, 300, 200, 2);
     * echo $pointKaos;
     * //This will output xyz: [150, -129, 10]
     * </code>
     *
     * @param int $dx the difference between the old x and new x (ie the value of x = 0 in the new system)
     * @param int $dy the difference between the old y and new y (ie the value of y = 0 in the new system)
     * @param int $dz the difference between the old y and new z (ie the value of z = 0 in the new system)
     * @param float $scale if specified, divides each coordinate by this value (optional)
     */
    function translate ($dx, $dy, $dz, $scale = 1) {
        if ($scale == 1) {
            $this->x += $dx;
            $this->y += $dy;
            $this->z += $dz;
        } elseif ($scale == 0) {
            $this->x = 0;
            $this->y = 0;
            $this->z = 0;
        } else {
            $this->x = $this->x * $scale + $dx;
            $this->y = $this->y * $scale + $dy;
            $this->z = $this->z * $scale + $dz;
        }
    }

    //
    // Implementing IteratorAggregate
    //

    /**
     * Retrieves class iterator. It traverses x, y and z.
     *
     * @return Traversable the iterator
     */
    function getIterator () {
        return new ArrayIterator($this);
    }


}

?>
