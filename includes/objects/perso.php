<?php

/**
 * Perso class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-01-27 00:39    Autogenerated by Pluton Scaffolding
 * 0.2    2010-01-29 14:39    Adding flags support
 * 0.3    2010-02-06 17:50    Adding static perso hashtable
 * 0.4    2012-07-04 11:37    Refactoring: moving code from index.php
 *
 * @package     Zed
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010, 2012 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

require_once("includes/geo/location.php");

/**
 * Perso class
 *
 * This class maps the persos table.
 *
 * The class also provides methods
 *     to move or locate a perso,
 *     to gets and sets perso's flags and notes (tables persos_flags and persos_notes),
 *     to gets user's perso or check if a perso is online,
 *     to handle on select and logout events.
 *
 */
class Perso {

    public $id;
    public $user_id;
    public $name;
    public $nickname;
    public $race;
    public $sex;
    public string $avatar = "";

    public $location;
    public $location_global;
    public $location_local;

    public $flags;

    public string $lastError = "";

    public static $hashtable_id = [];
    public static $hashtable_name = [];

    /**
     * Initializes a new instance
     *
     * @param mixed $data perso ID or nickname
     */
    function __construct ($data = null) {
        if ($data) {
            if (is_numeric($data)) {
                $this->id = $data;
            } else {
                $this->nickname = $data;
            }

            if (!$this->load_from_database()) {
                message_die(GENERAL_ERROR, $this->lastError, "Can't authenticate perso");
            }
        } else {
            $this->generate_id();
        }
    }

    /**
     * Initializes a new Perso instance if needed or get already available one.
     *
     * @param mixed $data perso ID or nickname
     * @return Perso the perso instance
     */
    static function get ($data = null) : Perso {
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

        return new Perso($data);
    }

    /**
     * Loads the object Perso (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('user_id', $_POST)) {
            $this->user_id = $_POST['user_id'];
        }
        if (array_key_exists('name', $_POST)) {
            $this->name = $_POST['name'];
        }
        if (array_key_exists('nickname', $_POST)) {
            $this->nickname = $_POST['nickname'];
        }
        if (array_key_exists('race', $_POST)) {
            $this->race = $_POST['race'];
        }
        if (array_key_exists('sex', $_POST)) {
            $this->sex = $_POST['sex'];
        }
        if (array_key_exists('avatar', $_POST)) {
            $this->avatar = $_POST['avatar'];
        }
        if (array_key_exists('location_global', $_POST)) {
            $this->location_global = $_POST['location_global'];
        }
        if (array_key_exists('location_local', $_POST)) {
            $this->location_local = $_POST['location_local'];
        }
    }

    /**
     * Loads the object Perso (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;

        //Gets perso
        $sql = "SELECT * FROM " . TABLE_PERSOS;
        if ($this->id) {
            $id = $db->escape($this->id);
            $sql .= " WHERE perso_id = '" . $id . "'";
        } else {
            $nickname = $db->escape($this->nickname);
            $sql .= " WHERE perso_nickname = '" . $nickname . "'";
        }
        if ( !($result = $db->query($sql)) ) {
            message_die(SQL_ERROR, "Unable to query persos", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            $this->lastError = "Perso unknown: " . $this->id;
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
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Can't get flags", '', __LINE__, __FILE__, $sql);
        }
        while ($row = $db->fetchRow($result)) {
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

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->escape($this->id) . "'" : 'NULL';
        $user_id = $db->escape($this->user_id);
        $name = $db->escape($this->name);
        $nickname = $db->escape($this->nickname);
        $race = $db->escape($this->race);
        $sex = $db->escape($this->sex);
        $avatar = $db->escape($this->avatar);
        $location_global =  $this->location_global ? "'" . $db->escape($this->location_global) . "'" : 'NULL';
        $location_local = $this->location_local ? "'" .  $db->escape($this->location_local) . "'" : 'NULL';

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_PERSOS . " (`perso_id`, `user_id`, `perso_name`, `perso_nickname`, `perso_race`, `perso_sex`, `perso_avatar`, `location_global`, `location_local`) VALUES ($id, '$user_id', '$name', '$nickname', '$race', '$sex', '$avatar', $location_global, $location_local)";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$id) {
            //Gets new record id value
            $this->id = $db->nextId();
        }
    }

    /**
     * Updates the specified field in the database record
     *
     * @param string $field The field to save
     */
    function save_field ($field) {
        global $db;
        if (!$this->id) {
            message_die(GENERAL_ERROR, "You're trying to update a perso record not yet saved in the database: $field");
        }
        $id = $db->escape($this->id);
        $value = $db->escape($this->$field);
        $sql = "UPDATE " . TABLE_PERSOS . " SET `$field` = '$value' WHERE perso_id = '$id'";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Unable to save $field field", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Gets perso location
     *
     * @return string The location names
     */
    public function where () {
        return $this->location->__toString();
    }

    /**
     * Moves the perso to a new location
     *
     * @param string $global the global target location
     * @param string $global the local target location
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
            $perso_id = $db->escape($this->id);
            $g = $db->escape($this->location_global);
            $l = $db->escape($this->location_local);
            $sql = "UPDATE " . TABLE_PERSOS .
                   " SET location_global = '$g', location_local = '$l'" .
                   " WHERE perso_id = '$perso_id'";
            if (!$db->query($sql)) {
                message_die(SQL_ERROR, "Can't save new $global $local location.", '', __LINE__, __FILE__, $sql);
            }
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

    /**
     * Gets the specified flag value
     *
     * @param string $key flag key
     * @param mixed $defaultValue default value if the flag doesn't exist
     * @return mixed the flag value (string) or null if not existing
     */
    public function get_flag ($key, $defaultValue = null) {
        return $this->flag_exists($key) ? $this->flags[$key] : $defaultValue;
    }

    /**
     * Determines if the specified flag exists
     *
     * @param string $key the flag key to check
     * @return boolean true if the specified flag exists ; otherwise, false.
     */
    public function flag_exists ($key) {
        return array_key_exists($key, $this->flags);
    }

    /**
     * Sets the specified flag
     *
     * @param string $key flag key
     * @param string $value flag value (optional, default value: 1)
     */
    public function set_flag ($key, $value = 1) {
        //Checks if flag isn't already set at this value
        if ($this->flags != null && array_key_exists($key, $this->flags) && $this->flags[$key] === $value) {
            return;
        }

        //Saves flag to database
        global $db;
        $id = $db->escape($this->id);
        $key = $db->escape($key);
        $value = $db->escape($value);
        $sql = "REPLACE " . TABLE_PERSOS_FLAGS . " SET perso_id = '$id', flag_key = '$key', flag_value = '$value'";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Can't save flag", '', __LINE__, __FILE__, $sql);
        }

        //Sets flag in this perso instance
        $this->flags[$key] = $value;
    }

    /**
     * Deletes the specified flag
     *
     * @param string $key flag key
     */
    public function delete_flag ($key) {
        global $db;
        if (!array_key_exists($key, $this->flags)) {
            return;
        }

        $id = $db->escape($this->id);
        $key = $db->escape($key);
        $sql = "DELETE FROM " . TABLE_PERSOS_FLAGS  .
               " WHERE flag_key = '$key' AND perso_id = '$id' LIMIT 1";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Can't delete flag", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Ensures the current perso have the specified flag or exits.
     *
     *
     * @param string $flag the flag to assert
     * @param int $threshold value the flags must strictly be greater than (optional, the default value is 0)
     *
     * Example:
     * <code>
     * $perso->set_flag('quux.foo', 1);
     * //The perso wants to read quux, which we allow with the flag quux.foo
     * $perso->request_flag('quux.foo'); //will be okay
     *
     * //The perso wants also to write quux, which we all allow if quux.foo = 2
     * //The threshold will so be 1, as 2 > 1
     * $perso->request_flag('quux.foo', 1); //Will exits, with a "You don't have quux.foo permission" message
     * </code>
     */
    public function request_flag ($flag, $threshold = 0) {
        if (!array_key_exists($flag, $this->flags) || $this->flags[$flag] <= $threshold) {
            message_die(HACK_ERROR, "You don't have $flag permission.", "Permissions");
        }
    }

    /**
     * Gets the specified note
     *
     * @param string $code the note code
     * @return string the note content
     */
    public function get_note ($code) {
        global $db;
        $id = $db->escape($this->id);
        $code = $db->escape($code);
        $sql = "SELECT note_text FROM " . TABLE_PERSOS_NOTES . " WHERE perso_id = '$id' AND note_code LIKE '$code'";
        return $db->queryScalar($sql);
    }

    /**
     * Sets the specified note
     *
     * @param string $code the note code
     * @param string $text the note content
     */
    public function set_note ($code, $text) {
        global $db;
        $id = $db->escape($this->id);
        $code = $db->escape($code);
        $text = $db->escape($text);
        $sql = "REPLACE INTO " . TABLE_PERSOS_NOTES  . " (perso_id, note_code, note_text) VALUES ('$id', '$code', '$text')";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Can't save note", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Counts the amount of notes the perso have saved
     *
     * @return int the amount of notes assigned to the this perso
     */
    public function count_notes () {
        global $db;
        $id = $db->escape($this->id);
        $sql = "SELECT COUNT(*) FROM " . TABLE_PERSOS_NOTES . " WHERE perso_id = '$id'";
        return $db->queryScalar($sql);
    }

    /*
     * Determines if the specified ID is available
     *
     * @param integer $id The perso ID to check
     * @return boolean true if the specified ID is available ; otherwise, false
     */
    public static function is_available_id ($id) {
        global $db;

        $sql = "SELECT COUNT(*) FROM " . TABLE_PERSOS . " WHERE perso_id = $id LOCK IN SHARE MODE";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Can't access users table", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->fetchRow($result);
        return ($row[0] == 0);
    }

    /**
     * Generates a unique ID for the current object
     */
    private function generate_id () {
        do {
            $this->id = rand(2001, 5999);
        } while (!Perso::is_available_id($this->id));
    }

    /**
     * Checks if the nickname is available
     *
     * @param string $nickname the nickname to check
     */
    public static function is_available_nickname ($nickname) {
        global $db;
        $nickname = $db->escape($nickname);
        $sql = "SELECT COUNT(*) FROM " . TABLE_PERSOS . " WHERE perso_nickname LIKE '$nickname' LOCK IN SHARE MODE;";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Utilisateurs non parsable", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->fetchRow($result);
        return ($row[0] == 0);
    }

    /**
     * Counts the perso a user have
     *
     * @param int user_id the user ID
     * @return int the user's perso count
     */
    public static function get_persos_count ($user_id) : int {
        global $db;
        $sql = "SELECT COUNT(*) FROM " . TABLE_PERSOS . " WHERE user_id = $user_id";
        return (int)$db->queryScalar($sql);

    }

    /**
     * Gets an array with all the perso of the specified user
     *
     * @param int $user_id the user ID
     */
    public static function get_persos (int $user_id) : array {
        global $db;
        $user_id = $db->escape($user_id);
        $sql = "SELECT perso_id FROM " . TABLE_PERSOS . " WHERE user_id = $user_id";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Can't get persos", '', __LINE__, __FILE__, $sql);
        }

        $persos = [];
        while ($row = $db->fetchRow($result)) {
            $persos[] = Perso::get($row['perso_id']);
        }
        return $persos;
    }

    /**
     * Gets the first perso a user have
     * (typically to be used when get_persos_count returns 1 to autoselect)
     *
     * @param int user_id the user ID
     */
    public static function get_first_perso ($user_id) {
        global $db;
        $sql = "SELECT perso_id FROM " . TABLE_PERSOS ."  WHERE user_id = $user_id LIMIT 1";
        if ($perso_id = $db->queryScalar($sql)) {
            return new Perso($perso_id);
        }
    }

    /**
     * Determines whether the perso is online
     *
     * @return bool true if the perso is online ; otherwise, false.
     */
    public function is_online () {
        global $db;
        $id = $db->escape($this->id);
        $sql = "SELECT MAX(online) FROM " . TABLE_SESSIONS ." WHERE perso_id = $id";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Unable to query the table", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->fetchRow($result);
        return ($row[0] == 1);
    }

    /**
     * This event method is called when the user selects a new perso
     */
    public function on_select () {
        //Session
        set_info('perso_id', $this->id);
        $this->set_flag("site.lastlogin", $_SERVER['REQUEST_TIME']);
        define("PersoSelected", true);
    }

    /**
     * This event method is called when the user logs off its account or perso
     */
    public function on_logout () {
        //Clears perso information in $_SESSION and session table
        set_info('perso_id', null);
        clean_session();
    }

    /**
     * This event method is called when the perso is created
     */
    public function on_create () {
        //Notifies host
        $this->notify_inviter();
    }

   /**
    * Creates a new perso, from a parameter form
    *
    * @param User $user The user to attach the perso to
    * @param Perso $perso A reference to the created perso (don't initialize it, give it a null value)
    * @param array $errors A reference to the arrays containing errors  (should be an empty array, or the method will always return false)
    * @return boolean true if the perso has ben created ; otherwise, false
    */
    public static function create_perso_from_form (User $user, &$perso, &$errors) {
        $perso = new Perso();
        $perso->load_from_form();
        $perso->user_id = $user->id;

        //Validates forms
        if (!$perso->name) {
            $errors[] = lang_get("NoFullnameSpecified");
        }
        if (!$perso->race) {
            $errors[] = lang_get("NoRaceSpecified");
            $perso->race = "being";
        }
        if (!$perso->sex) {
            $errors[] = lang_get("NoSexSpecified");
        }
        if (!$perso->nickname) {
            $errors[] = lang_get("NoNicknameSpecified");
        } elseif (!Perso::is_available_nickname($perso->nickname)) {
            $errors[] = lang_get("UnavailableNickname");
        }

       if (count($errors)) {
            return false;
       }

       //Creates perso
       $perso->save_to_database();
       $perso->on_create();
       return true;
    }

    /**
     * Notifies the person having invited this perso
     */
    public function notify_inviter() {
        require_once('includes/objects/message.php');
        require_once('includes/objects/invite.php');
        $message = new Message();
        $message->from = 0;
        $message->to = invite::who_invited($this->id);
        $message->text =  sprintf(
            lang_get('InvitePersoCreated'),
            $this->name,
            get_server_url() . get_url('who', $this->nickname)
        );
        $message->send();
    }
}
