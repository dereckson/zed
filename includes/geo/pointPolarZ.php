<?php

/**
 * Geo point polar+z class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
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

require_once("point3D.php");

/**
 * Geo point polar+z class.
 *
 * This class represents a r, ρ, z point.
 *
 * They are useful to express coordinates in a cylinder shape, like a tower
 * where it make senses to use polar coordinates instead x, y but where the
 * height is not relative to a center, like it would be in a sphere.
 *
 * It implements IteratorAggregate to allow the foreach instruction
 * on a GeoPointPolarZ object:
 *
 * <code>
 * $point = new GeoPointPolarZ(17, '24°', -6);
 * foreach ($point as $axis => $coordinate) {
 *     echo "\n\t$axis = $coordinate";
 * }
 * //This will output:
 * //    r = 17
 * //    t = 24°
 * //    z = -6
 * </code>
 *
 * The point 3D representation is rtz: [ρ, θ, z] ; you can print it as a string
 * and get this format:
 *
 * <code>
 * $point = new GeoPointPolarZ(17, '24°', -6);
 * echo (string)$point;   //will output rρz: [17, 24°, -6]
 * </code>
 *
 */
class GeoPointPolarZ implements IteratorAggregate {
    //
    // ρ, θ, z public properties
    //

    /**
     * the ρ coordinate
     *
     * @var float
     */
    public $r;

    /**
     * the θ coordinate
     *
     * This coordinate could be expressed as:
     *  - a string with a float, appended by "°" or " °" (in degree)
     *  - as a float (in radian)
     *
     * @var mixed
     */
    public $t;

    /**
     * the z coordinate
     *
     * @var float
     */
    public $z;

    //
    // constructor / toString
    //

    /**
     * Initializes a new instance of GeoPointPolarZ class
     *
     * @param float $r the ρ coordinate
     * @param mixed $t the θ coordinate, in ° (string) or radian (float)
     * @param float $z the z coordinate
     */
    function __construct ($r, $t, $z) {
        $this->r = (float)$r;
        $this->t = trim($t);
        $this->z = (float)$z;
    }

    /**
     * Parses a string expression and gets a GeoPointPolarZ object
     *
     * Formats recognized are:
     *      - rtz: [ρ, θ, z]
     *      - (ρ, θ, z)
     *
     * @param string $expression the expression to parse
     * @return GeoPointPolarZ If the specified expression could be parsed, a GeoPointPolarZ instance ; otherwise, null.
     */
    static function fromString ($expression) {
        if (string_starts_with($expression, 'rtz:', false)) {
            $pos1 = strpos($expression, '[', 4) + 1;
            $pos2 = strpos($expression, ']', $pos1);
            if ($pos1 > -1 && $pos2 > -1) {
                $expression = substr($expression, $pos1, $pos2 - $pos1);
                $rtz = explode(',', $expression, 3);
                return new GeoPointPolarZ($rtz[0], $rtz[1], $rtz[2]);
            }
        } elseif ($expression[0] = '(') {
            $expression = substr($expression, 1, -1);
            $rtz = explode(',', $expression, 3);
            return new GeoPointPolarZ($rtz[0], $rtz[1], $rtz[2]);
        }
        return null;
    }

    /**
     * Returns a string representation of the point coordinates.
     *
     * @param $format the format to use
     * @return string a string representation of the coordinates
     *
     * To print a "rtz: [10, 20°, 40]" string:
     *  $point = new GeoPointPolarZ(10, '20°', 40);
     *  echo $point->sprintf("rtz: [%d, %s, %d]");
     *
     *  //Of course, you could have (implicitly) use the __toString method:
     *  echo $point;
     *
     * To print a (10, 20°, 40) string:
     *  $point = new GeoPointPolarZ(10, 20°, 40);
     *  echo $point->sprintf("(%d, %s, %d)");
     */
    function sprintf ($format) {
        return sprintf($format, $this->r, self::getDegrees($this->t), $this->z);
    }

    /**
     * Returns a rρz: [r, ρ, z] string representation of the point coordinates.
     *
     * @return string a rtz: [ρ, θ, z] string representation of the coordinates
     */
    function __toString () {
        return $this->sprintf("rtz: [%d, %s, %d]");
    }

    /**
     * Determines if this point is equal to the specified point.
     *
     * @param GeoPointPolarZ $point The point to compare
     * @return bool true if the two points are equal ; otherwise, false.
     */
    function equals ($point) {
        return ($this->r == $point->r) && self::areAngleEqual($this->t, $point->t) && ($this->z == $point->z);
    }

    /**
     * Determines if two angles are equal
     * @param mixed $angle1 the first angle value, ie a float (angle in radian) or a string formed by an integer appended by ° (degrees)
     * @param mixed $angle2 the second angle value, a float (angle in radian) or a string formed by an integer appended by ° (degrees)
     * @return bool true if the angles are equal ; otherwise, false.
     */
    static function areAngleEqual ($angle1, $angle2) {
        if ($angle1 === $angle2) {
            return true;
        }
        if (!is_numerical($angle1)) {
            $angle1 = deg2rad((float)$angle1);
        }
        if (!is_numerical($angle2)) {
            $angle2 = deg2rad((float)$angle2);
        }
        $angle1 = self::normalizeAngle($angle1);
        $angle2 = self::normalizeAngle($angle2);
        return ($angle1 == $angle2);
    }

    /**
     * Normalizes an angle (in radians) in the interval [0, π[ (or a custom interval)
     *
     * @param float $angle the angle (in radians)
     * @param float $min the radians value the angle must be greater or equal than  [optional, default value: 0]
     * @param float $max the radians value the angle must be strictly lesser than [optional, default value: M_PI]
     * @param float $interval the increment interval [optional, default value: 360]
     */
    static function normalizeAngle ($angle, $min = 0, $max = M_PI, $interval = M_PI) {
        while ($angle < $min) {
             $angle += $interval;
        }
        while ($angle >= $max) {
            $angle -= $interval;
        }
        return $angle;
    }

    /**
     * Normalizes an angle (in degrees) in the interval [0, 360[ (or a custom interval)
     *
     * @param float $angle the angle to normalize, in degrees
     * @param float $min the degrees value the angle must be greater or equal than [optional, default value: 0]
     * @param float $max the degrees value the angle must be strictly lesser than [optional, default value: 360]
     * @param float $interval the increment interval [optional, default value: 360]
     */
    static function normalizeAngleInDegrees ($angle, $min = 0, $max = 360, $interval = 360) {
        while ($angle < $min) {
             $angle += $interval;
        }
        while ($angle >= $max) {
            $angle -= $interval;
        }
        return $angle;
   }

    /**
     * Gets the specified angle in radians
     *
     * @param mixed $angle the angle, a float in radians or a string (a float + "°" or " °" in degrees
     * @return float the angle in radians
     */
    static function getRadians ($angle) {
        return is_numeric($angle) ? $angle : deg2rad((float)$angle);
    }

    /**
     * Gets the specified angle in degrees
     *
     * @param mixed $angle the angle, a float in radians or a string (a float + "°" or " °" in degrees
     * @return string the angle (float) in degrees followed by "°"
     */
    static function getDegrees ($angle) {
        return is_numeric($angle) ? rad2deg((float)$angle) . '°' : $angle;
    }

    /**
     * Converts a polar coordinate angle to a 0-360° CW angle
     */
    static function getNaturalDegrees ($angle) {
        return self::normalizeAngleinDegrees(90 - self::getDegrees($angle));
    }

    //
    // Math
    //

    /**
     * Gets the (x, y, z) cartesian coordinates from the current ρ, θ, z polar+z point
     *
     * @return array an array of 3 floats number, representing the (x, y, z) cartesian coordinates
     */
    function toCartesian () {
        $x = $this->r * cos(self::getRadians($this->t));
        $y = $this->r * sin(self::getRadians($this->t));
        return [$x, $y, $this->z];
    }

    /**
     * Converts the current GeoPointPolarZ instance to a GeoPoint3D instance
     *
     * @return GeoPoint3D an instance of the GeoPoint3D class representing the (x, y, z) cartesian coordinates
     */
    function toPoint3D () {
        $pt = $this->toCartesian();
        return new GeoPoint3D($pt[0], $pt[1], $pt[2]);
    }

    /**
     * Gets the (ρ, φ, θ) spherical coordinates from the current (ρ, θ, z) polar+z point
     *
     * The algo used is from http://fr.wikipedia.org/wiki/Coordonn%C3%A9es_sph%C3%A9riques#Relation_avec_les_autres_syst.C3.A8mes_de_coordonn.C3.A9es_usuels
     *
     * @return array an array of 3 floats number, representing the (ρ, φ, θ) spherical coordinates
     */
    function toSpherical () {
        $pt = $this->toCartesian();
        return GeoGalaxy::cartesianToSpherical($pt[0], $pt[1], $pt[2]);
    }

    /**
     * Gets the (ρ, φ, θ) spherical coordinates from the current (ρ, θ, z) polar+z point
     *
     * The algo used is from http://www.phy225.dept.shef.ac.uk/mediawiki/index.php/Cartesian_to_polar_conversion
     *
     * @return array an array of 3 floats number, representing the (ρ, φ, θ) spherical coordinates
     */
    function toSphericalAlternative () {
        $pt = $this->toCartesian();
        return GeoGalaxy::cartesianToSphericalAlternative($pt[0], $pt[1], $pt[2]);
    }

    /**
     * Translates the center and rescales.
     *
     * This method allow to help to represent coordinate in a new system
     *
     * This method is used to represent Zed objects in dojo with the following
     * parameters:
     * <code>
     * $pointKaos = GeoPointPolarZ(800, 42, 220);
     * $pointKaos->translate(500, 300, 200, 2);
     * echo $pointKaos;
     * //This will output rρz: [150, -129, 10]
     * </code>
     *
     * @param float $dr the difference between the old ρ and new ρ (ie the value of ρ = 0 in the new system)
     * @param float $dt the difference between the old θ and new θ (ie the value of θ = 0 in the new system)
     * @param float $dz the difference between the old y and new z (ie the value of z = 0 in the new system)
     * @param int $scale if specified, divides each coordinate by this value (optional)
     */
    function translate ($dr, $dt, $dz, $scale = 1) {
        if ($scale == 1) {
            $this->r += $dr;
            $this->t += $dt;
            $this->z += $dz;
        } elseif ($scale == 0) {
            $this->r = 0;
            $this->t = 0;
            $this->z = 0;
        } else {
            $this->r = $this->r * $scale + $dr;
            $this->t = $this->t * $scale + $dt;
            $this->z = $this->z * $scale + $dz;
        }
    }

    /**
     * Calculates the section number the specified angle belongs
     *
     * @param $angle float The natural angle in degree (North 0°, East 90°, etc. clockwise)
     * @param int $count the number of sections (default value: 6)
     * @return $int the section number
     */
    static function calculateSection ($angle, $count = 6) {
        if ($angle < 90) {
            $angle += 270;
        } else {
            $angle -= 90;
        }
        return 1 + (int)($angle / (360/$count));
    }

    /**
     * Gets the section number the θ angle belongs to.
     *
     * @param int $count the number of sections
     * @return $int the section number
     */
    function getSection ($count = 6) {
        return self::calculateSection(self::getNaturalDegrees($this->t), $count);
    }

    //
    // Implementing IteratorAggregate
    //

    /**
     * Retrieves class iterator. It traverses ρ, θ and z.
     *
     * @return Traversable the iterator
     */
    function getIterator () {
        return new ArrayIterator($this);
    }
}
