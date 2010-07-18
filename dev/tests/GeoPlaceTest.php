<?php
require_once('PHPUnit/Framework.php');
require_once('../../includes/geo/place.php');

class GeoPlaceTest extends PHPUnit_Framework_TestCase {
    public function testIsValidLocation () {
        //Testing HyperShip Tower T2C3 format
        $p0 = new GeoPlace();
        $p0->location_local_format = '/^T[1-9][0-9]*C[1-6]$/';
        $this->assertTrue($p0->is_valid_local_location("T1C1"));         // 1
        $this->assertTrue($p0->is_valid_local_location("T14C1"));        // 2
        $this->assertTrue($p0->is_valid_local_location("T14C6"));        // 3
        $this->assertTrue($p0->is_valid_local_location("T140C6"));       // 4
        $this->assertTrue($p0->is_valid_local_location("T14000C6"));     // 5
        
        $this->assertFalse($p0->is_valid_local_location("C1T6"));        // 6
        $this->assertFalse($p0->is_valid_local_location("T14000 C6"));   // 7
        $this->assertFalse($p0->is_valid_local_location("T4C7"));        // 8
        $this->assertFalse($p0->is_valid_local_location("T4C0"));        // 9
        $this->assertFalse($p0->is_valid_local_location("T0C0"));        //10
        
        //Unit testing is useful: this test led to fix the regexp
        //from T[0-9]+C[1-6] to T[1-9][0-9]*C[1-6]
        $this->assertFalse($p0->is_valid_local_location("T0C1"));        //11
        
        //Testing default format
        $p1 = new GeoPlace();
              
        $this->assertTrue($p1->is_valid_local_location("(4,62,35)"));    //12
        $this->assertTrue($p1->is_valid_local_location("(4, 62, 35)"));  //13
        $this->assertTrue($p1->is_valid_local_location("(4, 62,35)"));   //14
        
        $this->assertFalse($p1->is_valid_local_location("(4,62,-35)"));  //15
        $this->assertFalse($p1->is_valid_local_location("(4, 62)"));     //16

        
        //Testing (x, y, -z) format
        $p2 = new GeoPlace();
        $p2->location_local_format = '/^\(\-?[0-9]+( )*,( )*\-?[0-9]+( )*,( )*\-?[0-9]+\)$/';
              
        $this->assertTrue($p2->is_valid_local_location("(4,62,35)"));    //17
        $this->assertTrue($p2->is_valid_local_location("(4, 62, 35)"));  //18
        $this->assertTrue($p2->is_valid_local_location("(4, 62,35)"));   //19
        $this->assertTrue($p2->is_valid_local_location("(4,62,-35)"));   //20

        $this->assertFalse($p2->is_valid_local_location("(4,62,- 35)")); //21
        $this->assertFalse($p2->is_valid_local_location("(4,62, - 35)")); //22
        $this->assertFalse($p2->is_valid_local_location("(4, 62)"));     //23
    }
}
?>
