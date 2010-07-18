<?php

require_once('body.php');
require_once('place.php');
require_once('point3D.php');
require_once('includes/objects/ship.php');

/*
 * Geo location class
 *
 * 0.1    2010-01-28 18:52    DcK
 *
 * @package Zed
 * @subpackage Geo
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 * @todo initializes $point3D from $body or $ship own locations;
 *
 */
class GeoLocation {
    private $data;

    /*
     * @var GeoBody a body object
     */
    public $body = null;
    
    /*
     * @var GeoPlace a place object
     */
    public $place = null;

    /*
     * @var GeoPoint3D a point identified by x, y, z coordinates
     */    
    public $point3D = null;
    
    /*
     * @var Ship a ship object
     */
    public $ship = null;
    
    function __construct ($global = null, $local = null) {
        if (!$global) {
            $this->data = array();
        } elseif (preg_match("/[BS][0-9]{5}[0-9]{3}/", $global)) {
            $this->data[0] = $global;
        } elseif (preg_match("/[BS][0-9]{5}/", $global)) {
            $this->data[0] = $global;
        } elseif (preg_match("/^xyz\:/", $global)) {
            $coords = sscanf($global, "xyz: [%d, %d, %d]");
            if (count($coords) == 3) {
                $this->data[0] = $global;
            } else {
                throw new Exception("Invalid expression: $global");    
            }
        } else {
            global $db;
            $name = $db->sql_escape($global);
            $sql = "SELECT location_code FROM " . TABLE_LOCATIONS . " WHERE location_name LIKE '$name'";
            $code = $db->sql_query_express($sql);
            if ($code) {
                $this->data[0] = $code;
                return;
            }
            throw new Exception("Invalid expression: $global");
        }
        
        //TODO: handle $local in a better way: from the global location, gets
        //a local location handler. Or a some inheriance, like a class
        //HypershipGeoLocation extending GeoLocation.
        if ($local !== null) $this->data[1] = $local;
        
        $this->load_classes();
    }
    
    /*
     * Gets $place, $body and $ship instances if they're needed
     */
    function load_classes () {
        //No data, no class to load
        if (!count($this->data))
            return;
        
        //Loads global classes
        $global = $this->data[0];
        $code = substr($global, 1, 5);
        switch ($global[0]) {
            case 'B':
                switch (strlen($global)) {
                    case 9:
                        $this->place = GeoPlace::from_code($global);
                        
                    case 6:
                        $this->body = new GeoBody($code);
                        break;
                }
                break;
            
            case 'S':
                $this->ship = new Ship($code);
                break;
            
            case 'x':
                $coords = sscanf($global, "xyz: [%d, %d, %d]");
                if (count($coords) == 3) {
                    $this->point3D = new GeoPoint3D($coords[0], $coords[1], $coords[2]);
                }
                break;
        }
    }
    
    /*
     * Magic method called when a unknown property is get.
     * Handles $global, $local, $type, $body_code, $ship_code, $place_code,
     *         $body_kind, $containsGlobalLocation, $containsLocalLocation.
     */
    function __get ($variable) {
        switch ($variable) {
            /* main variables */
            
            case 'global':
                return $this->data[0];
                break;
            
            case 'local':
                return $this->data[1];
                break;
            
            /* global location */
            
            case 'type':
                return $this->data[0][0];
            
            case 'body_code':
                if ($this->data[0][0] == 'B') {
                    return substr($this->data[0], 1, 5);
                }
                return null;
            
            case 'place_code':
                if ($this->data[0][0] == 'B') {
                    return substr($this->data[0], 6, 3);
                }
                return null;
            
            case 'ship_code':
                if ($this->data[0][0] == 'S') {
                    return substr($this->data[0], 1, 5);
                }
                return null;

            case 'body_kind':
                if ($this->data[0][0] == 'B' && $this->body != null) {
                    if ($kind = $this->body->kind()) {
                        return $kind;
                    }
                } elseif ($this->data[0][0] == 'S') {
                    return 'ship';
                }
                return 'place';

            case 'containsGlobalLocation':
                return count($this->data) > 0;
            
            case 'containsLocalLocation':
                return count($this->data) > 1;
            
            default:
                throw new Exception("Unknown variable: $variable");
                break;                
        }
    }
    
    /*
     * Checks if the place exists
     *
     * @return boolean true if the place exists ; false otherwise
     */
    function exists () {
        $n = count($this->data);
        
        //If not defined, it doesn't exist
        if ($n == 0) return false;
        
        //Checks global location
        switch ($this->data[0][0]) {
            case 'B':
                switch (strlen($this->data[0])) {
                    case 9:
                        if (!$place = GeoPlace::from_code($this->data[0]))
                            return false;
                        break;
                        
                    case 6:
                        $body = new GeoBody(substr($this->data[0], 1));
                        if ($body->lastError) return false;
                        break;
                        
                    default:
                        message_die(GENERAL_ERROR, "Invalid global location expression size: " . $this->data[0], "GeoLocation exists method", __LINE__, __FILE__);
                        
                }
                break;
            
            case 'S':
                $ship = new Ship(substr($this->data[0], 1));
                if ($body->lastError) return false;
                break;
            
            default:
                message_die(GENERAL_ERROR, "Invalid global location expression size: " . $this->data[0], "GeoLocation exists method", __LINE__, __FILE__);
                return false;
        }

        
        if ($n > 1) {
            if (!isset($place)) {
                message_die(GENERAL_ERROR, "Can't check if a local place exists for the following location: " . $this->data[0], "GeoLocation exists method", __LINE__, __FILE__);
            }
            if  (!$place->is_valid_local_location($this->data[1])) {
                return false;
            }
        }
        
        return true;
    }
    
    /*
     * Checks if the place is equals at the specified expression or place
     *
     * @return boolean true if the places are equals ; otherwise, false
     */
    function equals ($expression) {
        //Are global location equals?
        
        //TODO: creates a better set of rules to define when 2 locations are equa l.
        if (is_a($expression, 'GeoLocation')) {
            if (!$this->equals($expression->data[0])) {
                return false;
            }
            if (count($expression->data) + count($this->data) > 2) {
                return $expression->data[1] == $this->data[1];
            }
        }

        if ($expression == $this->data[0]) return true;
        
        $n1 = strlen($expression);
        $n2 = strlen($this->data[0]);
       
        if ($n1 > $n2) {
            return substr($expression, 0, $n2) == $this->data[0];
        }
        
        return false;
    }
    
    /*
     * Represents the current location instance as a string
     *
     * @return string a string representing the current location
     */
    function __toString () {
        if (!$this->data[0])
            return "";
        
        switch ($this->data[0][0]) {
            case 'S':
                $ship = new Ship($this->ship_code);
                $location[] = $ship->name;
                break;
                
            case 'B':
                $body = new GeoBody($this->body_code);
                $location[] = $body->name ? $body->name : lang_get('UnknownBody');
                
                if (strlen($this->data[0]) == 9) {
                    $place = GeoPlace::from_code($this->data[0]);
                    $location[] = $place->name ? $place->name : lang_get('UnknownPlace');
                }
                break;
                
            default:
                message_die(GENERAL_ERROR, "Unknown location identifier: $type.<br />Expected: B or S.");
        }
        
        return implode(", ", array_reverse($location));        
    }
    
    
    /*
     * Magic method called when a unknown property is set.
     * Handles $global, $local, $type, $body_code, $ship_code, $place_code
     */
    function __set ($variable, $value) {
        switch ($variable) {
            /* main variables */
            
            case 'global':
                $this->data[0] = $value;
                break;
            
            case 'local':
                $this->data[1] = $value;
                break;
            
            /* global location */
            
            case 'type':
                if ($value == 'B' || $value == 'S') {
                    if (!$this->data[0]) {
                        $this->data[0] = $value;
                    } else {
                        $this->data[0][0] = $value;
                    }
                }
                break;
            
            case 'body_code':
                if (preg_match("/[0-9]{1,5}/", $value)) {
                    $value = sprintf("%05d", $value);
                    if (!$this->data[0]) {
                        $this->data[0] = "B" . $value;
                        return;
                    } elseif ($this->data[0][0] == 'B') {
                        $this->data[0] = "B" . $value . substr($this->data[0], 6);
                        return;
                    }
                    throw new Exception("Global location isn't a body.");
                }
                throw new Exception("$value isn't a valid body code");
                
            case 'ship_code':
                if (preg_match("/[0-9]{1,5}/", $value)) {
                    $value = sprintf("%05d", $value);
                    if (!$this->data[0]) {
                        $this->data[0] = "S" . $value;
                        return;
                    } elseif ($this->data[0][0] == 'S') {
                        $this->data[0] = "S" . $value . substr($this->data[0], 6);
                        return;
                    }
                    throw new Exception("Global location isn't a ship.");
                }
                throw new Exception("$value isn't a valid ship code");
            
            case 'place_code':
                if (!preg_match("/[0-9]{1,3}/", $value)) {
                    throw new Exception("$value isn't a valid place code");
                }
                $value = sprintf("%03d", $value);
                if ($this->data[0][0] == 'B') {
                    $this->data[0] = substr($this->data[0], 0, 6) . $value;
                }
                throw new Exception("Global location isn't a body.");
            
            default:
                throw new Exception("Unknown variable: $variable");
                break;                
        }
    }
}

?>
