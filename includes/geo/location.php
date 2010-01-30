<?php

require_once('body.php');
require_once('place.php');

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
 */
class GeoLocation {
    private $data;
    public $body = null;
    public $place = null;
    
    function __construct ($global = null, $local = null) {
        if (!$global) {
            $this->data = array();
        } elseif (ereg("[BS][0-9]{5}[0-9]{3}", $global)) {
            $this->data[0] = $global;
        } elseif (ereg("[BS][0-9]{5}", $global)) {
            $this->data[0] = $global;
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
        
        $this->load_classes();
        
        //TODO: handle $local
    }
    
    function load_classes () {
        //No data, no class to load
        if (!count($this->data))
            return;
        
        //Loads global classes
        $global = $this->data[0];
        $body_code = substr($global, 1, 5);
        switch ($global[0]) {
            case 'B':
                switch (strlen($global)) {
                    case 9:
                        $this->place = GeoPlace::from_code($global);
                        
                    case 6:
                        $this->body = new GeoBody($body_code);
                        break;
                }
                break;
            case 'S':
                break;
        }
    }
    
    
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

            case 'body_kind':
                if ($this->data[0][0] == 'B' && $this->body != null) {
                    if ($kind = $this->body->kind()) {
                        return $kind;
                    }
                }
                return 'place';
            
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
                message_die(GENERAL_ERROR, "Handle S", 'TODO', __LINE__, __FILE__);
                break;
            
            default:
                message_die(GENERAL_ERROR, "Invalid global location expression size: " . $this->data[0], "GeoLocation exists method", __LINE__, __FILE__);
                return false;
        }

        
        if ($n > 1) {
            message_die(GENERAL_ERROR, "Can't check if a local place exists yet.", "GeoLocation exists method", __LINE__, __FILE__);
        }
        
        return true;
    }
    
    function equals ($expression) {
        if ($expression == $this->data[0]) return true;
        
        $n1 = strlen($expression);
        $n2 = strlen($this->data[0]);
       
        if ($n1 > $n2) {
            return substr($expression, 0, $n2) == $this->data[0];
        }
        
        return false;
    }
    
    function __toString () {
        if (!$this->data[0])
            return "";
        
        switch ($this->data[0][0]) {
            case 'S':
                message_die(GENERAL_ERROR, "Handle spaceship in GeoLocation __toString()", "TODO", __LINE__, __FILE__);
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
                if (ereg("[0-9]{1,3}", $value)) {
                    $value = sprintf("%03d", $value);
                    if (!$this->data[0]) {
                        $this->data[0] = "B" . $value;
                        return;
                    } elseif ($this->data[0][0] == 'B') {
                        $this->data[0] = "B" . $value . substr($this->data[0], 5);
                        return;
                    }
                    throw new Exception("Global location isn't a body.");
                }
                throw new Exception("$value isn't a valid body code");
            
            case 'place_code':
                if (!ereg("[0-9]{1,5}", $value)) {
                    throw new Exception("$value isn't a valid place code");
                }
                $value = sprintf("%05d", $value);
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