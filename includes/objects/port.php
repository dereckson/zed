<?php

/**
 * Port class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-02-09 19:17    Autogenerated by Pluton Scaffolding
 * 
 * @package     Zed
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

require_once("includes/geo/location.php");

/**
 * Port class
 *
 * This class maps the ports table.
 *
 * The class also provides helper methods to handle ports at specified location.
 */
class Port {

    public $id;  
    public $location_global;
    public $location_local;
    public $name;
    
    public $hidden;
    public $requiresPTA;
    public $default;
    
    /**
     * Initializes a new instance
     * @param int $id the primary key
     */
    function __construct ($id = NULL) {
        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        }
    }
    
    /**
     * Loads the object Port (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('location_global', $_POST)) $this->location_global = $_POST['location_global'];
        if (array_key_exists('location_local', $_POST)) $this->location_local = $_POST['location_local'];
        if (array_key_exists('name', $_POST)) $this->name = $_POST['name'];
        
        if (array_key_exists('hidden', $_POST)) $this->hidden = $_POST['hidden'] ? true : false;
        if (array_key_exists('requiresPTA', $_POST)) $this->requiresPTA = $_POST['requiresPTA'] ? true : false;
        if (array_key_exists('default', $_POST)) $this->hidden = $_POST['default'] ? true : false;
    }
    
    /**
     * Loads the object Port (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM " . TABLE_PORTS . " WHERE port_id = '" . $id . "'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query ports", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Port unkwown: " . $this->id;
            return false;
        }
        $this->location_global = $row['location_global'];
        $this->location_local = $row['location_local'];
        $this->name = $row['port_name'];
        
        //Explodes place_status SET field in boolean variables
        if ($row['place_status']) {
            $flags = explode(',', $row['port_status']);
            foreach ($flags as $flag) {
                $this->$flag = true;
            }
        }

        return true;
    }
    
    /**
     * Gets status field value
     *
     * @return string the status field value (e.g. "requiresPTA,default")
     */
    function get_status () {
        $flags = array('hidden', 'requiresPTA', 'default');
        foreach ($flags as $flag) {
            if ($this->$flag) {
                $status[] = $flag;
            }
        }
        return implode(',', $status);
    }
    
    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;
        
        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $location_global = $db->sql_escape($this->location_global);
        $location_local = $db->sql_escape($this->location_local);
        $name = $db->sql_escape($this->name);
        $status = $this->get_status();

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_PORTS . " (`port_id`, `location_global`, `location_local`, `port_name`, `port_status`) VALUES ($id, '$location_global', '$location_local', '$name', '$status')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
        
        if (!$id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }
    
    /**
     * Determines if the specified location have a port
     * 
     * @param string $location_global the global location
     * @return boolean true if there is a spatioport exactly at the specified location ; otherwise, false.
     */
    static function have_port ($location_global) {
        return (get_port_id($location_global) !== NULL); 
    }

    /**
     * Gets the port situated exactly at the specified global location
     * 
     * @param string $location_global the global location
     * @return int the port ID
     */    
    static function get_port_id ($location_global) {
        global $db;
        $location_global = $db->sql_escape($location_global);
        $sql = "SELECT port_id FROM " . TABLE_PORTS . " WHERE location_global = '$location_global'";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to get ports", '', __LINE__, __FILE__, $sql);
        }
        if ($row = $db->sql_fetchrow($result)) {
           return $row['port_id']; 
        }
        return null;
    }
    
    /**
     * Gets default port, from specified global location
     * 
     * @param string $location_global the global location
     * @return Port the port near this location ; null if there isn't port there.
     */
    static function from_location ($location_global) {
        $havePlace = strlen($location_global) == 9;
        $port_id = null;
        
        if ($havePlace) {
            //Checks if there's a port at specified location
            $port_id = self::get_port_id($location_global);
        }
        
        if ($port_id == null) {
            //Nearest default port.
            //If place have been specified (B0001001), we've to found elsewhere
            //==> B00001%
            global $db;
            $loc = $db->sql_escape(substr($location_global, 0, 6));
            $sql = "SELECT port_id FROM " . TABLE_PORTS . " WHERE location_global LIKE '$loc%'";
            if (!$result = $db->sql_query($sql)) {
                message_die(SQL_ERROR, "Can't get port", '', __LINE__, __FILE__, $sql);
            }
            if ($row = $db->sql_fetchrow($result)) {
                $port_id = $row['port_id'];
            } else {
                return null;
            }
        }
        
        return new Port($port_id);
     }
}
    
?>