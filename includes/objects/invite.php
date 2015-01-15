<?php

/**
 * User invite class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-06-29 02:13    Initial version [DcK]
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
 * User invite class
 *
 * This class maps the users_invites table.
 */
class Invite {

    public $code;
    public $date;
    public $from_user_id;
    public $from_perso_id;

    /**
     * The user_id who have been claimed the invite
     * Will be NULL as long as the invite haven't been claimed
     *
     * @var int
     */
    public $to_user_id = NULL;

    /**
     * Initializes a new instance
     *
     * @param int $code the primary key
     */
    function __construct ($code = NULL) {
        if ($code) {
            $this->code = $code;
            $this->load_from_database();
        } else {
            //New invite code
            $this->generate_code();
            $this->date = time();
        }
    }

    /**
     * Generates a unique invite code and sets it in the code property.
     */
    function generate_code () {
        global $db;

        do {
            $this->code = generate_random_string("AAA111");
            $sql = "SELECT COUNT(*) FROM " . TABLE_USERS_INVITES . " WHERE invite_code = '$this->code' LOCK IN SHARE MODE;";
            if (!$result = $db->sql_query($sql)) {
                message_die(SQL_ERROR, "Can't access invite users table", '', __LINE__, __FILE__, $sql);
            }
            $row = $db->sql_fetchrow($result);
        } while ($row[0]);
    }

    /**
     * Loads the object Invite (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $code = $db->sql_escape($this->code);
        $sql = "SELECT * FROM " . TABLE_USERS_INVITES . " WHERE invite_code = '" . $code . "'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query invite codes", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Invite code unkwown: " . $this->code;
            return false;
        }
        $this->code = $row['invite_code'];
        $this->date = $row['invite_date'];
        $this->from_user_id = $row['invite_from_user_id'];
        $this->from_perso_id = $row['invite_from_perso_id'];
        $this->to_user_id = $row['invite_to_user_id'];

        return true;
    }

    /**
     * Determines wheter the current invite code have been claimed by an user.
     *
     * @return true if the code have been claimed ; otherwise, false.
     */
    function is_claimed () {
        return (bool)$this->to_user_id;
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $code = $db->sql_escape($this->code);
        $date = $db->sql_escape($this->date);
        $from_user_id = $db->sql_escape($this->from_user_id);
        $from_perso_id = $db->sql_escape($this->from_perso_id);
        $to_user_id = $this->to_user_id ? "'" . $db->sql_escape($this->to_user_id) . "'" : 'NULL';

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_USERS_INVITES . " (`invite_code`, `invite_date`, `invite_from_user_id`, `invite_from_perso_id`, `invite_to_user_id`) VALUES ('$code', '$date', '$from_user_id', '$from_perso_id', $to_user_id)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save invite code", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Deletes the invite
     */
    function delete () {
        global $db;
        $code = $db->sql_escape($this->code);
        $sql = "DELETE FROM " . TABLE_USERS_INVITES . " WHERE invite_code = '$code'";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save delete code", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Creates an invite code
     *
     * @param int $user_id user id
     * @param int $perso_id perso id
     * @return string the invite code
     */
    static function create ($user_id, $perso_id) {
        $invite = new Invite();
        $invite->from_perso_id = $perso_id;
        $invite->from_user_id = $user_id;
        $invite->save_to_database();
        return $invite->code;
    }

    /**
     * Gets invites generated by the specified perso ID
     *
     * @param int $perso_id the perso whom to get the invites
     * @return Array an array of string, each line being an invite code
     */
    static function get_invites_from ($perso_id) {
        global $db;
        $sql = "SELECT invite_code FROM " . TABLE_USERS_INVITES
             . " WHERE invite_from_perso_id = $perso_id AND invite_to_user_id IS NULL ORDER BY invite_date ASC";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't access invite users table", '', __LINE__, __FILE__, $sql);
        }
        $codes = array();
        while ($row = $db->sql_fetchrow($result)) {
            $codes[] = $row['invite_code'];
        }
        return $codes;
    }

    /**
     * Gets the perso ID who invited the specified perso
     *
     * @param int $perso_id the perso whom to get the invites
     * @return int|null the perso whom to get the invites ; or null, if nobody have invited him
     */
    static function who_invited ($perso_id) {
        global $db;
        $perso = Perso::get($perso_id);

        if ($user_id = $perso->user_id) {
            $sql = "SELECT invite_from_perso_id FROM " . TABLE_USERS_INVITES . " WHERE invite_to_user_id = '$user_id'";
            if (!$result = $db->sql_query($sql)) {
                message_die(SQL_ERROR, "Can't access invite users table", '', __LINE__, __FILE__, $sql);
            }
            if ($row = $db->sql_fetchrow($result)) {
                return $row[0];
            }
        }

        return null;
    }

}

?>

