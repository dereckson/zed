<?php

/**
 * Profile comments class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-01-03 01:02    Autogenerated by Pluton Scaffolding
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

namespace Zed\Models\Profile;

use Keruald\Database\DatabaseEngine;
use Zed\Models\Base\Entity;
use Zed\Models\Base\WithDatabase;

/**
 * Profile comments class
 *
 * This class maps the profiles_comments table.
 */
class ProfileComment extends Entity {

    use WithDatabase;

    ///
    /// Properties
    ///

    public $id;
    public $perso_id;
    public $author;
    public $authorname; //should be read-only
    public $date;
    public $text;

    public string $lastError = "";

    /**
     * Initializes a new instance of the ProfileComment class
     *
     * @param int $id the comment ID
     */
    function __construct (DatabaseEngine $db, $id = '') {
        $this->setDatabase($db);

        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        } else {
            $this->date = time();
        }
    }

    /**
     * Loads the object comment (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('perso_id', $_POST)) {
            $this->perso_id = $_POST['perso_id'];
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
     * Loads the object comment (ie fill the properties) from the database
     */
    function load_from_database (): bool {
        $db = $this->getDatabase();

        $id = $db->escape($this->id);
        $sql = "SELECT c.*, p.perso_name as author FROM " . TABLE_PROFILES_COMMENTS . " c, " . TABLE_PERSOS . " p WHERE c.comment_id = '$id' AND p.perso_id = c.comment_author";
        if (!($result = $db->query($sql))) {
            message_die(SQL_ERROR, "Unable to query azhar_profiles_comments", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            $this->lastError = "comment unknown: " . $this->id;
            return false;
        }
        $this->perso_id = $row['perso_id'];
        $this->author = $row['comment_author'];
        $this->authorname = $row['author'];
        $this->date = $row['comment_date'];
        $this->text = $row['comment_text'];
        return true;
    }

    /**
     * Saves the object to the database
     */
    function save_to_database (): void {
        $db = $this->getDatabase();

        $id = $this->id ? "'" . $db->escape($this->id) . "'" : 'NULL';
        $perso_id = $db->escape($this->perso_id);
        $author = $db->escape($this->author);
        $date = $db->escape($this->date);
        $text = $db->escape($this->text);

        $sql = "REPLACE INTO " . TABLE_PROFILES_COMMENTS . " (`comment_id`, `perso_id`, `comment_author`, `comment_date`, `comment_text`) VALUES ($id, '$perso_id', '$author', '$date', '$text')";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
        if (!$id) {
            //Gets new record id value
            $this->id = $db->nextId();
        }
    }

    /**
     * Publishes the comment
     * @todo Add events on publish
     */
    function publish () {
        $this->save_to_database();
    }

    /**
     * Gets comments
     *
     * @param int $perso_id The Perso ID
     */
    static function get_comments (DatabaseEngine $db, $perso_id) {
        $sql = "SELECT comment_id FROM " . TABLE_PROFILES_COMMENTS . " WHERE perso_id = " . $db->escape($perso_id);
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Unable to get comments", '', __LINE__, __FILE__, $sql);
        }

        $comments = [];
        while ($row = $db->fetchRow($result)) {
            $comments[] = new ProfileComment($db, $row[0]);
        }
        return $comments;
    }
}