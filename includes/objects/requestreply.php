<?php

/**
 * RequestReply class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2011, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2011-06-13 21:20    Autogenerated by Pluton Scaffolding
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
 * RequestReply class
 *
 * This class maps the requests_replies table.
 */
class RequestReply {

    public $id;
    public $request_id;
    public $author;
    public $date;
    public $text;

    public string $lastError = "";

    /**
     * Initializes a new instance
     * @param int $id the primary key
     */
    function __construct ($id = null) {
        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        }
    }

    /**
     * Loads the object RequestReply (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('request_id', $_POST)) {
            $this->request_id = $_POST['request_id'];
        }
        if (array_key_exists('author', $_POST)) {
            $this->author = $_POST['author'];
        }
        if (array_key_exists('date', $_POST)) {
            $this->date = $_POST['date'];
        }
        if (array_key_exists('text', $_POST)) {
            $this->text = $_POST['text'];
        }
    }

    /**
     * Loads the object RequestReply (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->escape($this->id);
        $sql = "SELECT * FROM " . TABLE_REQUESTS_REPLIES . " WHERE request_reply_id = '" . $id . "'";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Unable to query requests_replies", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            $this->lastError = "RequestReply unknown: " . $this->id;
            return false;
        }
        $this->request_id = $row['request_id'];
        $this->author = $row['request_reply_author'];
        $this->date = $row['request_reply_date'];
        $this->text = $row['request_reply_text'];
        return true;
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->escape($this->id) . "'" : 'NULL';
        $request_id = $db->escape($this->request_id);
        $author = $db->escape($this->author);
        $date = $db->escape($this->date);
        $text = $db->escape($this->text);

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_REQUESTS_REPLIES . "(`request_reply_id`, `request_id`, `request_reply_author`, `request_reply_date`, `request_reply_text`) VALUES ('$id', '$request_id', '$author', '$date', '$text')";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            //Gets new record id value
            $this->id = $db->nextId();
        }
    }
}
