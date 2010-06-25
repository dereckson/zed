<?php

/*
 * Geo point 3D class
  *
 * 0.1    2010-02-23 14:14    DcK
 *
 * @package Zed
 * @subpackage Geo
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 */
class GeoPoint3D implements IteratorAggregate {
    //
    // x, y, z public properties
    //
    
    /*
     * @var integer the x coordinate
     */
    public $x;

    /*
     * @var integer the y coordinate
     */
    public $y;
    
    /*
     * @var integer the z coordinate
     */
    public $z;
    
    //
    // constructor / toString
    //
    
    /*
     * Initializes a new instance of GeoPoint3D class
     */
    function __construct ($x, $y, $z) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }
    
    /*
     * Returns a xyz: [x, y, z] string representation of the point coordinates
     */
    function __toString () {
        return sprintf("xyz: [%d, %d, %d]", $this->x, $this->y, $this->z);
    }
    
    //
    // Implementing IteratorAggregate
    //
    
    /*
     * Retrieves class iterator. It traverses x, y and z.
     * @return Traversable the iterator
     */
    function getIterator () {
        return new ArrayIterator($this);
    }
}

?>