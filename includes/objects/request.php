<?php

/**
 * Request class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2011, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2011-06-13 21:16    Autogenerated by Pluton Scaffolding
 *
 * @package     Zed
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2011 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * Request class
 *
 * This class maps the requests table.
 */
class Request {

    public $id;
    public $code;
    public $title;
    public $date;
    public $author;
    public $to;
    public $message;
    public $location_global;
    public $location_local;
    public $status;

    /**
     * Initializes a new instance
     * @param int $id the primary key
     */
    function __construct ($id = NULL) {
        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        } else {
           $this->date = time();
           $this->status = 'NEW';
        }
    }

    /**
     * Loads the object Request (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('code', $_POST)) {
            $this->code = $_POST['code'];
        }
        if (array_key_exists('title', $_POST)) {
            $this->title = $_POST['title'];
        }
        if (array_key_exists('date', $_POST)) {
            $this->date = $_POST['date'];
        }
        if (array_key_exists('author', $_POST)) {
            $this->author = $_POST['author'];
        }
        if (array_key_exists('to', $_POST)) {
            $this->to = $_POST['to'];
        }
        if (array_key_exists('message', $_POST)) {
            $this->message = $_POST['message'];
        }
        if (array_key_exists('location_global', $_POST)) {
            $this->location_global = $_POST['location_global'];
        }
        if (array_key_exists('location_local', $_POST)) {
            $this->location_local = $_POST['location_local'];
        }
        if (array_key_exists('status', $_POST)) {
            $this->status = $_POST['status'];
        }
    }

    /**
     * Loads the object Request (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM " . TABLE_REQUESTS . " WHERE request_id = '" . $id . "'";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to query requests", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Request unknown: " . $this->id;
            return false;
        }
        $this->code = $row['request_code'];
        $this->title = $row['request_title'];
        $this->date = $row['request_date'];
        $this->author = $row['request_author'];
        $this->message = $row['request_message'];
        $this->to = $row['request_to'];
        $this->location_global = $row['location_global'];
        $this->location_local = $row['location_local'];
        $this->status = $row['request_status'];
        return true;
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $code = $db->sql_escape($this->code);
        $title = $db->sql_escape($this->title);
        $date = $db->sql_escape($this->date);
        $author = $db->sql_escape($this->author);
        $message = $db->sql_escape($this->message);
        $to = $db->sql_escape($this->to);
        $location_global = $db->sql_escape($this->location_global);
        $location_local = $db->sql_escape($this->location_local);
        $status = $db->sql_escape($this->status);

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_REQUESTS . " (`request_id`, `request_code`, `request_title`, `request_date`, `request_author`, `request_message`, `request_to`, `location_global`, `location_local`, `request_status`) VALUES ('$id', '$code', '$title', '$date', '$author', '$message', '$to', '$location_global', '$location_local', '$status')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }
}
