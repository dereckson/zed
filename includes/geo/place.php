<?php

/**
 * Geo place class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-01-28 01:48    Autogenerated by Pluton Scaffolding
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
 * Default local location format
 *
 * The local_location format is a PCRE regular expression
 *
 * By default, local_location format is an (x, y, z) expression
 */
define('LOCATION_LOCAL_DEFAULT_FORMAT', '/^\([0-9]+( )*,( )*[0-9]+( )*,( )*[0-9]+\)$/');

/**
 * Geo place
 *
 * A place is a city or a hypership district.
 *
 * It's identified by a 9 chars geocode like B0001001.
 * The 5 first chars indicates the body (class GeoBody) where the place is and
 * the 3 last digits is the place number.
 *
 * This class maps the geo_places table.
 */
class GeoPlace {

    public $id;  
    public $body_code;
    public $code;
    public $name;
    public $description;
    public $location_local_format;
    
    public $start;
    public $hidden;
    
    /**
     * Initializes a new instance
     * 
     * @param int $id the primary key
     */
    function __construct ($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        }
    }
    
    /**
     * Loads the object place (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('body_code', $_POST)) $this->body_code = $_POST['body_code'];
        if (array_key_exists('code', $_POST)) $this->code = $_POST['code'];
        if (array_key_exists('name', $_POST)) $this->name = $_POST['name'];
        if (array_key_exists('description', $_POST)) $this->description = $_POST['description'];
        if (array_key_exists('status', $_POST)) $this->status = $_POST['status'];
        if (array_key_exists('location_local_format', $_POST)) $this->location_local_format = $_POST['location_local_format'];
    }
    
    /**
     * Loads the object place (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $sql = "SELECT * FROM " . TABLE_PLACES . " WHERE place_id = '" . $this->id . "'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query geo_places", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "place unkwown: " . $this->id;
            return false;
        }
        $this->body_code = $row['body_code'];
        $this->code = $row['place_code'];
        $this->name = $row['place_name'];
        $this->description = $row['place_description'];
        $this->location_local_format = $row['location_local_format'];

        //Explodes place_status SET field in boolean variables
        if ($row['place_status']) {
            $flags = explode(',', $row['place_status']);
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
        $flags = array('start', 'hidden');
        foreach ($flags as $flag) {
            if ($this->$flag == true) {
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
        $body_code = $db->sql_escape($this->body_code);
        $code = $db->sql_escape($this->code);
        $name = $db->sql_escape($this->name);
        $description = $db->sql_escape($this->description);
        $status = $this->get_status();
        $location_local_format = $db->sql_escape($this->location_local_format);

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_PLACES . " (`place_id`, `body_code`, `place_code`, `place_name`, `place_description`, `place_status`, `location_local_format`) VALUES ($id, '$body_code', '$code', '$name', '$description', '$status', '$location_local_format')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
        
        if (!$id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }
    
    /**
     * Determines if the specified local location looks valid
     *
     * @param string $local_location the local location
     * @return boolean true if the specified local location looks valid ; otherwise, false.
     */
    function is_valid_local_location ($local_location) {
        $format = $this->location_local_format ? $this->location_local_format : LOCATION_LOCAL_DEFAULT_FORMAT;
        return preg_match($format, $local_location) > 0;
    }

    /**
     * Gets a string representation of the current place
     * 
     * @return string A Bxxxxxyyy string like B00001001, which represents the current place.
     */    
    function __tostring () {
        return 'B' . $this->body_code . $this->code;
    }
    
    /**
     * Creates a Place instance, from the specified body/place code
     * 
     * @param $code the place's code
     * @return GeoPlace the place instance
     */
    static function from_code ($code) {
        global $db;        
        $sql = "SELECT * FROM " . TABLE_PLACES . " WHERE CONCAT('B', body_code, place_code) LIKE '$code'";
        if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Unable to query geo_places", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            return null;
        }
        
        $place = new GeoPlace();
        $place->id = $row['place_id'];
        $place->body_code = $row['body_code'];
        $place->code = $row['place_code'];
        $place->name = $row['place_name'];
        $place->description = $row['place_description'];
        $place->location_local_format = $row['location_local_format'];

        //Explodes place_status SET field in boolean variables
        if ($row['place_status']) {
            $flags = explode(',', $row['place_status']);
            foreach ($flags as $flag) {
                $place->$flag = true;
            }
        }

        return $place;
    }
    
    /**
     * Gets a start location
     *
     * @return string The global location code of a start location
     * 
     * @TODO sql optimisation (query contains ORDER BY RAND())
     */
    static function get_start_location () {
        global $db;
        $sql = "SELECT CONCAT('B', body_code, place_code) FROM " . TABLE_PLACES . " WHERE FIND_IN_SET('start', place_status) > 0 ORDER BY rand() LIMIT 1";
        return $db->sql_query_express($sql);
    }
}

?>
