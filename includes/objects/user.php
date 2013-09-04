<?php

/**
 * User class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * [DESIGN BY CONTRACT NOTE] No more than one OpenID per user
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

/**
 * User class
 *
 * This class maps the users and users_openid tables.
 *
 * It also provides helper methods to check if a login is available,
 * or to retrieve a username from e-mail address.
 */
class User {

    public $id;
    public $name;
    public $password;
    public $active = 0;
    public $actkey;
    public $email;
    public $regdate;

    public static $hashtable_id = array();
    public static $hashtable_name = array();

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
     * Initializes a new User instance if needed or get already available one.
     *
     * @param mixed $data user ID or name
     * @return User the user instance
     */
    static function get ($data = null) {
        if ($data) {
            //Checks in the hashtables if we already have loaded this instance
            if (is_numeric($data)) {
                if (array_key_exists($data, User::$hashtable_id)) {
                    return User::$hashtable_id[$data];
                }
            } else {
                if (array_key_exists($data, User::$hashtable_name)) {
                    return User::$hashtable_name[$data];
                }
            }
        }

        $user = new User($data);
        return $user;
    }

    /**
     * Loads the object User (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('name', $_POST)) $this->name = $_POST['name'];
        if (array_key_exists('password', $_POST)) $this->password = $_POST['password'];
        if (array_key_exists('active', $_POST)) $this->active = $_POST['active'];
        if (array_key_exists('actkey', $_POST)) $this->actkey = $_POST['actkey'];
        if (array_key_exists('email', $_POST)) $this->email = $_POST['email'];
        if (array_key_exists('regdate', $_POST)) $this->regdate = $_POST['regdate'];
    }

    /**
     * Loads the object User (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE user_id = '" . $this->id . "'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query users", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "User unkwown: " . $this->id;
            return false;
        }
        $this->name = $row['username'];
        $this->password = $row['user_password'];
        $this->active = $row['user_active'];
        $this->actkey = $row['user_actkey'];
        $this->email = $row['user_email'];
        $this->regdate = $row['user_regdate'];

        //Puts object in hashtables
        Perso::$hashtable_id[$this->id] = $this;
        Perso::$hashtable_name[$this->name] = $this;

        return true;
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $name = $db->sql_escape($this->name);
        $password = $db->sql_escape($this->password);
        $active = $db->sql_escape($this->active);
        $actkey = $db->sql_escape($this->actkey);
        $email = $db->sql_escape($this->email);
        $regdate = $this->regdate ? "'" . $db->sql_escape($this->regdate) . "'" : 'NULL';

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_USERS . " (`user_id`, `username`, `user_password`, `user_active`, `user_actkey`, `user_email`, `user_regdate`) VALUES ($id, '$name', '$password', '$active', '$actkey', '$email', $regdate)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }

    /**
     * Updates the specified field in the database record
     */
    function save_field ($field) {
        global $db;
        if (!$this->id) {
            message_die(GENERAL_ERROR, "You're trying to update a record not yet saved in the database");
        }
        $id = $db->sql_escape($this->id);
        $value = $db->sql_escape($this->$field);
        $sql = "UPDATE " . TABLE_USERS . " SET `$field` = '$value' WHERE user_id = '$id'";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save $field field", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Generates a unique user id
     */
    function generate_id () {
        global $db;

        do {
            $this->id = rand(2001, 5999);
            $sql = "SELECT COUNT(*) FROM " . TABLE_USERS . " WHERE user_id = $this->id LOCK IN SHARE MODE;";
            if (!$result = $db->sql_query($sql)) {
                message_die(SQL_ERROR, "Can't access users table", '', __LINE__, __FILE__, $sql);
            }
            $row = $db->sql_fetchrow($result);
        } while ($row[0]);
    }

    /**
     * Fills password field with encrypted version of the specified clear password
     *
     * @param string $newpassword The user's new password
     */
    public function set_password ($newpassword) {
        $this->password = md5($newpassword);
    }

    /**
     * Deletes OpenID for this user
     */
    public function delete_OpenID () {
        $this->set_OpenID('');
    }

    /**
     * Sets OpenID for this user
     *
     * @param string $url OpenID endpoint URL
     */
    public function set_OpenID ($url) {
        global $db;
        if (!$this->id) $this->save_to_database();
        $url = $db->sql_escape($url);
        $sql = "DELETE FROM " . TABLE_USERS_AUTH . " WHERE auth_type = 'OpenID' AND user_id = $this->id";
        if (!$db->sql_query($sql))
            message_die(SQL_ERROR, "Can't delete old OpenID", '', __LINE__, __FILE__, $sql);
        if ($url != '') {
            $sql = "INSERT INTO " . TABLE_USERS_AUTH . " (auth_type, auth_identity, user_id) VALUES ('OpenID', '$url', $this->id)";
            if (!$db->sql_query($sql))
                message_die(SQL_ERROR, "Can't add new OpenID", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Checks if a login is available
     *
     * @param string $login the login to check
     * @return bool true if the specified login is available ; otherwise, false.
     */
    public static function is_available_login ($login) {
        global $db;
        $sql = "SELECT COUNT(*) FROM " . TABLE_USERS . " WHERE username LIKE '$login' LOCK IN SHARE MODE;";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Utilisateurs non parsable", '', __LINE__, __FILE__, $sql);
        }
        $row = $db->sql_fetchrow($result);
        return ($row[0] ? false : true);
    }

    /**
     * Gets username from specified e-mail
     *
     * @param string $mail the mail to search
     * @return string|bool the username matching the mail if found ; otherwise, false.
     */
    public static function get_username_from_email ($mail) {
        global $db;
        $sql = "SELECT username FROM " . TABLE_USERS . " WHERE user_email LIKE '$mail' LOCK IN SHARE MODE;";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Utilisateurs non parsable", '', __LINE__, __FILE__, $sql);
        }
        if ($row = $db->sql_fetchrow($result)) {
            return $row['username'];
        }
        return false;
    }
}

?>
