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

/**
 * Geo point 3D class.
 *
 * This class reprensents a x, y, z point.
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
     */
    function __construct ($x, $y, $z) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }
    
    /**
     * Returns a xyz: [x, y, z] string representation of the point coordinates
     *
     * @return string a xyz: [x, y, z] string representation of the coordinates
     */
    function __toString () {
        return sprintf("xyz: [%d, %d, %d]", $this->x, $this->y, $this->z);
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