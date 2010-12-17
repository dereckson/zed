<?php

/**
 * Zone class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
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
 * Content zone class
 *
 * A zone is a physical place, independent from the location.
 * This mechanism allows to more easily move zones.
 *
 * This class maps the content_zones table.
 */
class ContentZone {

    public $id;
    public $title;
    public $type;
    public $params;
    public $deleted = false;

    /**
     * Initializes a new instance of a zone object
     *
     * @param int $id The zone ID
     */
    function __construct ($id = '') {
        if ($id) {
            $this->id = $id;
            return $this->load_from_database();
        }
    }

    /**
     * Loads the object zone (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('title', $_POST)) $this->user_id = $_POST['title'];
        if (array_key_exists('type', $_POST)) $this->user_id = $_POST['type'];
        if (array_key_exists('params', $_POST)) $this->user_id = $_POST['params'];
        if (array_key_exists('deleted', $_POST)) $this->user_id = $_POST['deleted'];
    }

    /**
     * Loads the object zone (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM " . TABLE_CONTENT_ZONES . " WHERE zone_id = '" . $id . "'";
        if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, 'Unable to query content_zones', '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = 'Zone unkwown: ' . $this->id;
            return false;
        }
        $this->title = $row['zone_title'];
        $this->type = $row['zone_type'];
        $this->params = $row['zone_params'];
        $this->deleted = $row['zone_deleted'] ? true : false;

        return true;
    }

    /**
     * Saves the object to the database
     */
    function save_to_database () {
        global $db;
        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $title = $db->sql_escape($this->title);
        $type = $db->sql_escape($this->type);
        $params = $db->sql_escape($this->params);
        $deleted = $this->deleted ? 1 : 0;

        $sql = "REPLACE INTO " . TABLE_CONTENT_ZONES . " (`zone_id`, `zone_title`, `zone_type`, `zone_params`, `zone_deleted`) VALUES ($id, '$title', '$type', '$params', $deleted)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            $this->id = $db->sql_nextid();
        }
    }

    function assign_to ($location_global, $location_local, $delete_old_locations = true) {
        if (!$this->id) {
            $this->save_to_database();
        }
        global $db;
        if ($delete_old_locations) {
            $sql = "DELETE FROM " . TABLE_CONTENT_ZONES_LOCATIONS . " WHERE zone_id = " . $this->id;
            if (!$db->sql_query($sql)) {
                message_die(SQL_ERROR, "Unable to delete", '', __LINE__, __FILE__, $sql);
            }
        }
        $g = $db->sql_escape($location_global);
        $l = $db->sql_escape($location_local);
        $sql = "REPLACE INTO " . TABLE_CONTENT_ZONES_LOCATIONS . " (location_global, location_local, zone_id) VALUES ('$g', '$l', $this->id)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to set zone location", '', __LINE__, __FILE__, $sql);
        }

    }
}

?>

