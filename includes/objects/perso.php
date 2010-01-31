<?php

/*
 * Perso class
 *
 * 0.1    2010-01-27 00:39    Autogenerated by Pluton Scaffolding
 * 0.2    2010-01-29 14:39    Adding flags support
 *
 * @package Zed
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 */

require_once("includes/geo/location.php");

class Perso {

    public $id;  
    public $user_id;
    public $name;
    public $nickname;
    public $race;
    public $sex;
    public $avatar;
    public $location_global;
    public $location_local;
    
    public $flags;
    
    /*
     * Initializes a new instance
     * @param int $id the primary key
     */
    function __construct ($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        } else {
            $this->generate_id();
        }
    }
    
    /*
     * Loads the object Perso (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('user_id', $_POST)) $this->user_id = $_POST['user_id'];
        if (array_key_exists('name', $_POST)) $this->name = $_POST['name'];
        if (array_key_exists('nickname', $_POST)) $this->nickname = $_POST['nickname'];
        if (array_key_exists('race', $_POST)) $this->race = $_POST['race'];
        if (array_key_exists('sex', $_POST)) $this->sex = $_POST['sex'];
        if (array_key_exists('avatar', $_POST)) $this->avatar = $_POST['avatar'];
        if (array_key_exists('location_global', $_POST)) $this->location_global = $_POST['location_global'];
        if (array_key_exists('location_local', $_POST)) $this->location_local = $_POST['location_local'];
    }
    
    /*
     * Loads the object Perso (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        
        //Gets perso
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM " . TABLE_PERSOS . " WHERE perso_id = '" . $id . "'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query persos", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Perso unkwown: " . $this->id;
            return false;
        }
        $this->user_id = $row['user_id'];
        $this->name = $row['perso_name'];
        $this->nickname = $row['perso_nickname'];
        $this->race = $row['perso_race'];
        $this->sex = $row['perso_sex'];
        $this->avatar = $row['perso_avatar'];
        $this->location_global = $row['location_global'];
        $this->location_local = $row['location_local'];
        
        //Gets flags
        $sql = "SELECT flag_key, flag_value FROM " . TABLE_PERSOS_FLAGS .
               " WHERE perso_id = $this->id";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get flags", '', __LINE__, __FILE__, $sql);
        }
        while ($row = $db->sql_fetchrow($result)) {
            $this->flags[$row["flag_key"]] = $row["flag_value"];
        }
        
        //Gets location
        $this->location = new GeoLocation(
            $this->location_global,
            $this->location_local
        );
        
        return true;
    }
    
    /*
     * Saves to database
     */
    function save_to_database () {
        global $db;
        
        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $user_id = $db->sql_escape($this->user_id);
        $name = $db->sql_escape($this->name);
        $nickname = $db->sql_escape($this->nickname);
        $race = $db->sql_escape($this->race);
        $sex = $db->sql_escape($this->sex);
        $avatar = $db->sql_escape($this->avatar);
        $location_global =  $this->location_global ? "'" . $db->sql_escape($this->location_global) . "'" : 'NULL';
        $location_local = $this->location_local ? "'" .  $db->sql_escape($this->location_local) . "'" : 'NULL';

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_PERSOS . " (`perso_id`, `user_id`, `perso_name`, `perso_nickname`, `perso_race`, `perso_sex`, `perso_avatar`, `location_global`, `location_local`) VALUES ($id, '$user_id', '$name', '$nickname', '$race', '$sex', '$avatar', $location_global, $location_local)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
        
        if (!$id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }
    
    /*
     * Updates the specified field in the database record
     */
    function save_field ($field) {
        global $db;
        if (!$this->id) {
            message_die(GENERAL_ERROR, "You're trying to update a record not yet saved in the database");
        }
        $id = $db->sql_escape($this->id);
        $value = $db->sql_escape($this->$field);
        $sql = "UPDATE " . TABLE_PERSOS . " SET `$field` = '$value' WHERE perso_id = '$id'";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save $field field", '', __LINE__, __FILE__, $sql);
        }
    }
    
    /*
     * Gets perso location
     * @return string The location names
     */
    public function where () {
        return $this->location->__toString();
    }
    
    /*
     * Moves the perso to a new location
     */
    public function move_to ($global = null, $local = null) {
        //Sets global location
        if ($global != null) {
            $this->location_global = $global;
        }
        
        //Sets local location
        if ($local != null) {
            $this->location_local = $local;
        }
        
        //Updates database record
        if ($global != null && $local != null) {
            global $db;
            $perso_id = $db->sql_escape($this->id);
            $g = $db->sql_escape($this->location_global);
            $l = $db->sql_escape($this->location_local);
            $sql = "UPDATE " . TABLE_PERSOS .
                   " SET location_global = '$g', location_local = '$l'" .
                   " WHERE perso_id = '$perso_id'";
            if (!$db->sql_query($sql))
                message_die(SQL_ERROR, "Can't save new $global $local location.", '', __LINE__, __FILE__, $sql);
        } elseif ($global != null) {
            $this->save_field('location_global');
        } elseif ($global != null) {
            $this->save_field('location_local');
        }
        
        //Updates location member
        $this->location = new GeoLocation(
            $this->location_global,
            $this->location_local
        );
    }
    
    public function setflag ($key, $value) {
        //Checks if flag isn't already set at this value
        if ($this->flags[$key] === $value)
            return;
        
        //Saves flag to database
        global $db;
        $id = $db->sql_escape($this->id);
        $key = $db->sql_escape($key);
        $value = $db->sql_escape($value);
        $sql = "REPLACE " . TABLE_PERSOS_FLAGS . " SET perso_id = '$id', flag_key = '$key', flag_value = '$value'";
        if (!$db->sql_query($sql))
            message_die(SQL_ERROR, "Can't save flag", '', __LINE__, __FILE__, $sql);
        
        //Sets flag in this perso instance
        $this->flags[$key] = $value;
    }
    
    public static function is_available_id ($id) {
        global $db;
                   
        $sql = "SELECT COUNT(*) FROM " . TABLE_PERSOS . " WHERE perso_id = $id LOCK IN SHARE MODE";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't access users table", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->sql_fetchrow($result);
        return ($row[0] == 0);
    }
    
    /*
     * Generates a unique ID for the current object
     */
    private function generate_id () {
        do {
            $this->id = rand(2001, 5999);
        } while (!Perso::is_available_id($this->id));
    }
    
    /*
     * Checks if the nickname is available
     * @param string $nickname the nickname to check
     */
    public static function is_available_nickname ($nickname) {
        global $db;
        $nickname = $db->sql_escape($nickname);
        $sql = "SELECT COUNT(*) FROM " . TABLE_PERSOS . " WHERE perso_nickname LIKE '$nickname' LOCK IN SHARE MODE;";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Utilisateurs non parsable", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->sql_fetchrow($result);
        return ($row[0] == 0);
    }
    
    /*
     * Counts the perso a user have
     *
     * @param int user_id the user ID
     */
    public static function get_persos_count ($user_id) {
        global $db;
        $sql = "SELECT COUNT(*) FROM " . TABLE_PERSOS . " WHERE user_id = $user_id";
        return $db->sql_query_express($sql);
    }
    
    public static function get_persos ($user_id) {
        global $db;
        $user_id = $db->sql_escape($user_id);
        $sql = "SELECT perso_id FROM " . TABLE_PERSOS . " WHERE user_id = $user_id";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get persos", '', __LINE__, __FILE__, $sql);
        }
        while ($row = $db->sql_fetchrow($result)) {
            $persos[] = new Perso($row['perso_id']);
        }
        return $persos;
    }
    
    /*
     * Gets the first perso a user have
     * (typically to be used when get_persos_count returns 1 to autoselect)
     *
     * @param int user_id the user ID
     */
    public static function get_first_perso ($user_id) {
        global $db;
        $sql = "SELECT perso_id FROM " . TABLE_PERSOS . " WHERE user_id = $user_id LIMIT 1";
        if ($perso_id = $db->sql_query_express($sql)) {
            return new Perso($perso_id);
        }
    }
}
    
?>