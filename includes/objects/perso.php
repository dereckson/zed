<?php

/*
 * Perso class
 *
 * 0.1    2010-01-27 00:39    Autogenerated by Pluton Scaffolding
 * 0.2    2010-01-29 14:39    Adding flags support
 * 0.3    2010-02-06 17:50    Adding static perso hashtable
 *
 * @package Zed
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.3
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
    
    public static $hashtable_id = array();
    public static $hashtable_name = array();
    
    /*
     * Initializes a new instance
     * @param mixed $data perso ID or nickname
     */
    function __construct ($data = null) {
        if ($data) {
            if (is_numeric($data)) {
                $this->id = $data;
            } else {
                $this->nickname = $data;
            }

            $this->load_from_database();
        } else {
            $this->generate_id();
        }
    }
    
    /*
     * Initializes a new Perso instance if needed or get already available one.
     * @param mixed $data perso ID or nickname
     * @eturn Perso the perso instance
     */
    static function get ($data = null) {        
        if ($data) {
            //Checks in the hashtables if we already have loaded this instance
            if (is_numeric($data)) {
                if (array_key_exists($data, Perso::$hashtable_id)) {
                    return Perso::$hashtable_id[$data];
                }
            } else {
                if (array_key_exists($data, Perso::$hashtable_name)) {
                    return Perso::$hashtable_name[$data];
                }
            }
        }
        
        $perso = new Perso($data);
        return $perso;
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
        $sql = "SELECT * FROM " . TABLE_PERSOS;
        if ($this->id) {
            $id = $db->sql_escape($this->id);
            $sql .= " WHERE perso_id = '" . $id . "'";
        } else {
            $nickname = $db->sql_escape($this->nickname);
            $sql .= " WHERE perso_nickname = '" . $nickname . "'";
        }
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query persos", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Perso unkwown: " . $this->id;
            return false;
        }
        
        $this->id = $row['perso_id'];
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
        
        //Puts object in hashtables
        Perso::$hashtable_id[$this->id] = $this;
        Perso::$hashtable_name[$this->nickname] = $this;
        
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
            message_die(GENERAL_ERROR, "You're trying to update a perso record not yet saved in the database: $field");
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
        } elseif ($local != null) {
            $this->save_field('location_local');
        }
        
        //Updates location member
        $this->location = new GeoLocation(
            $this->location_global,
            $this->location_local
        );
    }

    /*
     * Gets the specified flag value
     * @param string $key flag key
     * @return mixed the flag value (string) or null if not existing
     */    
    public function get_flag ($key) {
        if (array_key_exists($key, $this->flags)) {
            return $this->flags[$key];
        }
        return null;
    }
    
    /*
     * Sets the specified flag
     * @param string $key flag key
     * @param string $value flag value (optional, default value: 1)
     */
    public function set_flag ($key, $value = 1) {
        //Checks if flag isn't already set at this value
        if (array_key_exists($key, $this->flags) && $this->flags[$key] === $value)
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
    
    /*
     * Deletes the specified flag
     * @param string $key flag key
     */ 
    public function delete_flag ($key) {
        global $db;
        if (!array_key_exists($key, $this->flags)) return;
        
        $id = $db->sql_escape($this->id);
        $key = $db->sql_escape($key);
        $sql = "DELETE FROM " . TABLE_PERSOS_FLAGS  .
               " WHERE flag_key = '$key' AND perso_id = '$id' LIMIT 1";
        if (!$db->sql_query($sql))
            message_die(SQL_ERROR, "Can't delete flag", '', __LINE__, __FILE__, $sql);
    }
    
    /*
     * Ensures the current perso have the flag or dies.
     * @param string $flag XXXX
     * @param string $$threshold YYYY
     */
    public function request_flag ($flag, $threshold = 0) {
        if (!array_key_exists($flag, $this->flags) || $this->flags[$flag] <= $threshold) {
            message_die(HACK_ERROR, "You don't have $flag permission.", "Permissions");
        }
    }
    
    /*
     * Determines if the specified ID is available
     * @param integer $id The perso ID to check
     * @return boolean true if the specified ID is available ; otherwise, false
     */
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
     * @return the user's perso count
     */
    public static function get_persos_count ($user_id) {
        global $db;
        $sql = "SELECT COUNT(*) FROM " . TABLE_PERSOS . " WHERE user_id = $user_id";
        return $db->sql_query_express($sql);
         
    }
    
    /*
     * Gets an array with all the perso of the specified user
     */
    public static function get_persos ($user_id) {
        global $db;
        $user_id = $db->sql_escape($user_id);
        $sql = "SELECT perso_id FROM " . TABLE_PERSOS . " WHERE user_id = $user_id";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get persos", '', __LINE__, __FILE__, $sql);
        }
        
        while ($row = $db->sql_fetchrow($result)) {
            $persos[] = Perso::get($user_id);
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
    
    public function on_select () {
        //Session
        set_info('perso_id', $this->id);
        $this->set_flag("site.lastlogin", $_SERVER['REQUEST_TIME']);
        define("PersoSelected", true);
    }
    
    public function on_logout () {
        //Clears perso information in $_SESSION and session table
        set_info('perso_id', null);
        clean_session();
    }
}
    
?>