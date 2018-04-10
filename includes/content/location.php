<?php

/**
 * Content location class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-12-03  2:58    Forked from Content class
  *
 * @package     Zed
 * @subpackage  Content
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * Content location class
 *
 * This class maps the content_locations table.
 *
 * A content location is defined by 3 parameters:
 *  - location_global
 *  - location_local
 *  - location_k, an index for the content at the specified location
 *
 * This class allows to get or set the content_id at this
 * (global, local, k) location.
 *
 * This class also provides a static helper method to
 * get local content from a specific location.
 */
class ContentLocation {

/*  -------------------------------------------------------------
    Properties
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    public $location_global = null;
    public $location_local  = null;
    public $location_k      = null;

    public $content_id;

/*  -------------------------------------------------------------
    Constructor, __toString
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Initializes a new ContentLocation instance
     *
     * @param string $location_global the global location
     * @param string $location_local the local location
     * @param int $location_k the item indice for the specified location
     */
    function __construct ($location_global = null, $location_local = null, $location_k = null) {
        $this->location_global = $location_global;
        $this->location_local  = $location_local;

        if ($location_k) {
            $this->location_k =  $location_k;
            $this->load_from_database();
        } else {
            $this->location_k = self::get_free_location_k($location_global, $location_local);
        }
    }

    /**
     * Returns a string representation of current Content instance
     *
     * @return string the content title or path if title is blank.
     */
    function __toString () {
        $location_global = $this->location_global ? $this->location_global : '?';
        $location_local = $this->location_local ? $this->location_local : '?';
        $location_k = $this->location_k ? $this->location_k : '?';
        return "($location_global, $location_local, $location_k)";
    }

/*  -------------------------------------------------------------
    Load/save class
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Loads the object ContentLocation (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $location_global = "'" . $db->sql_escape($this->location_global) . "'";
        $location_local = "'" . $db->sql_escape($this->location_local) . "'";
        $location_k = "'" . $db->sql_escape($this->location_k) . "'";
        $sql = "SELECT * FROM content_locations WHERE location_global = '$location_global' AND location_local = '$location_local' AND location_k = '$location_k'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query content", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Content location unknown: " . $this->content_id;
            return false;
        }
        $this->load_from_row($row);
        return true;
    }

    /**
     * Loads the object from row
     */
    function load_from_row ($row) {
        $this->content_id = $row['content_id'];
        $this->location_global = $row['location_global'];
        $this->location_local = $row['location_local'];
        $this->location_k = $row['location_k'];
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $location_global = "'" . $db->sql_escape($this->location_global) . "'";
        $location_local = "'" . $db->sql_escape($this->location_local) . "'";
        $location_k = "'" . $db->sql_escape($this->location_k) . "'";
        $content_id = $this->content_id ? "'" . $db->sql_escape($this->content_id) . "'" : 'NULL';

        $sql = "REPLACE INTO content_locations (location_global, location_local, location_k, content_id) VALUES ($location_global, $location_local, $location_k, $content_id)";
        if (!$db->sql_query($sql))
            message_die(SQL_ERROR, "Can't save content location", '', __LINE__, __FILE__, $sql);
    }

/*  -------------------------------------------------------------
    Helper methods
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Gets the next k free value for the specified location
     *
     * @param string $location_global the global location
     * @param string $location_local the local location
     *
     * @param int $location_k the next free local content indice
     */
    function get_free_location_k ($location_global, $location_local) {
        $location_global = "'" . $db->sql_escape($location_global) . "'";
        $location_local = "'" . $db->sql_escape($location_local) . "'";
        $sql = "SELECT MAX(location_k) + 1 FROM content_locations WHERE location_global = '$location_global' AND location_local = '$location_local'";
        if (!$result = $db->sql_query($sql))
            message_die(SQL_ERROR, "Can't get content location k", '', __LINE__, __FILE__, $sql);
        $row = $db->sql_fetchrow($result);
        return $row[0];
    }

    /**
     * Deletes this content location from the database
     */
    function delete() {
        $location_global = "'" . $db->sql_escape($this->location_global) . "'";
        $location_local = "'" . $db->sql_escape($this->location_local) . "'";
        $location_k = "'" . $db->sql_escape($this->location_k) . "'";
        $sql = "DELETE FROM content_locations WHERE location_global = '$location_global' AND location_local = '$location_local' AND location_k = '$location_k' LIMIT 1";
        if (!$db->sql_query($sql))
            message_die(SQL_ERROR, "Can't delete current content location", '', __LINE__, __FILE__, $sql);
    }

    /**
     * Moves the content into new location
     *
     * @param string $location_global the target global location
     * @param string $location_local the target local location
     * @param int $location_k the target local content indice [facultative]
     */
    function move ($location_global, $location_local, $location_k = null) {
        if ($this->content_id) {
            $this->delete();
        }

        if ($location_k) {
            $this->location_k =  $location_k;
        } else {
            $this->location_k = self::get_free_location_k($location_global, $location_local);
        }

        if ($this->content_id) {
            $this->save_to_database();
        }
    }

/*  -------------------------------------------------------------
    Gets content
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Gets content at specified location
     *
     * @param string $location_global global content location
     * @param string $location_local   local content location
     * @return Array array of ContentFile instances
     *
     * The returned array indices are the local_k.
     */
    static function get_local_content ($location_global, $location_local) {
        global $db;

        //Get contents at this location
        $location_global = $db->sql_escape($location_global);
        $location_local  = $db->sql_escape($location_local);

        $sql = "SELECT c.* FROM content c WHERE c.location_global = '$location_global' AND c.location_local = '$location_local' ORDER BY location_k ASC";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get content", '', __LINE__, __FILE__, $sql);
        }

        //Fills content array
        $contents = [];
        while ($row = $db->sql_fetchrow($result)) {
            $k = $row['location_k'];
            $contents[$k] = new ContentFile();
            $contents[$k]->load_from_row($row);
        }

        return $contents;
    }

}
