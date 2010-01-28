<?php

/*
 * place class
 *
 * 0.1    2010-01-28 01:48    Autogenerated by Pluton Scaffolding
 *
 * @package Zed
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 */
class GeoPlace {

    public $id;  
    public $body_code;
    public $code;
    public $name;
    public $description;
    public $status;
    
    /*
     * Initializes a new instance
     * @param int $id the primary key
     */
    function __construct ($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        }
    }
    
    /*
     * Loads the object place (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('body_code', $_POST)) $this->body_code = $_POST['body_code'];
        if (array_key_exists('code', $_POST)) $this->code = $_POST['code'];
        if (array_key_exists('name', $_POST)) $this->name = $_POST['name'];
        if (array_key_exists('description', $_POST)) $this->description = $_POST['description'];
        if (array_key_exists('status', $_POST)) $this->status = $_POST['status'];
    }
    
    /*
     * Loads the object place (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $sql = "SELECT * FROM geo_places WHERE place_id = '" . $this->id . "'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query geo_places", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "place unkwown: " . $this->id;
            return false;
        }
        $this->body_code = $row['body_code'];
        $this->code = $row['place_code'];
        $this->name = $row['place_name'];
        $this->description = $row['place_description'];
        $this->status = $row['place_status'];
        return true;
    }
    
    /*
     * Saves to database
     */
    function save_to_database () {
        global $db;
        
        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $body_code = $db->sql_escape($this->body_code);
        $code = $db->sql_escape($this->code);
        $name = $db->sql_escape($this->name);
        $description = $db->sql_escape($this->description);
        $status = $db->sql_escape($this->status);

        //Updates or inserts
        $sql = "REPLACE INTO geo_places (`place_id`, `body_code`, `place_code`, `place_name`, `place_description`, `place_status`) VALUES ($id, '$body_code', '$code', '$name', '$description', '$status')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
        
        if (!$id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }
    
    /*
     * Gets a start location
     */
    static function get_start_location () {
        global $db;
        $sql = "SELECT CONCAT('B', body_code, place_code) FROM geo_places WHERE FIND_IN_SET('start', place_status) > 0 ORDER BY rand() LIMIT 1";
        return $db->sql_query_express($sql);
    }
}
?>

