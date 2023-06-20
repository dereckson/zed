<?php

/**
 * MOTD class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-02-03 21:11    Import from Azhàr code
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

namespace Zed\Models\Objects;

use Keruald\Database\DatabaseEngine;
use Zed\Models\Base\Entity;

/**
 * MOTD class
 *
 * This class maps the motd table.
 */
class MOTD extends Entity {

    public $id;
    public $perso_id;
    public $text;
    public $date;

    public string $lastError = "";

    /**
     * Initializes a new instance of a MOTD object
     */
    function __construct (DatabaseEngine $db, int $id = null) {
        $this->setDatabase($db);

        if ($id !== null) {
            $this->id = $id;
            return $this->load_from_database();
        } else {
            $this->date = time();
            return true;
        }
    }

    /**
     * Loads the object MOTD (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('perso_id', $_POST)) {
            $this->user_id = $_POST['user_id'];
        }
        if (array_key_exists('text', $_POST)) {
            $this->text = $_POST['text'];
        }
        if (array_key_exists('date', $_POST)) {
            $this->date = $_POST['date'];
        }
    }

    /**
     * Loads the object MOTD (ie fill the properties) from the database
     */
    function load_from_database (): bool {
        $db = $this->getDatabase();

        $id = $db->escape($this->id);
        $sql = "SELECT * FROM " . TABLE_MOTD . " WHERE motd_id = '" . $id . "'";
        if (!($result = $db->query($sql))) {
            message_die(SQL_ERROR, "Unable to query azhar_motd", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            $this->lastError = "MOTD unknown: " . $this->id;
            return false;
        }
        $this->perso_id = $row['perso_id'];
        $this->text = $row['motd_text'];
        $this->date = $row['motd_date'];
        return true;
    }

    /**
     * Saves the object to the database
     */
    function save_to_database (): void {
        $db = $this->getDatabase();

        $id = $this->id ? "'" . $db->escape($this->id) . "'" : 'NULL';
        $perso_id = $db->escape($this->perso_id);
        $text = $db->escape($this->text);
        $date = $db->escape($this->date);

        $sql = "REPLACE INTO " . TABLE_MOTD . " (`motd_id`, `perso_id`, `motd_text`, `motd_date`) VALUES ($id, '$perso_id', '$text', '$date')";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
    }
}
