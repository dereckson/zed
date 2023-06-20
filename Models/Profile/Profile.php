<?php

/**
 * Profile  class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-01-02 16:49    Autogenerated by Pluton Scaffolding
 *                            Import from Azhàr
 * 0.2    2010-07-05 03:56    Tags
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

use Cache;
use Keruald\Database\DatabaseEngine;
use Zed\Models\Base\Entity;
use Zed\Models\Base\WithDatabase;
use Zed\Models\Objects\Perso;

/**
 * Profile class
 *
 * This class maps the profiles table.
 *
 * The class also provides methods to handle and cache tags.
 */
class Profile extends Entity {

    use WithDatabase;

    public Perso $perso;
    public string $text = "";
    public $updated;
    public $fixedwidth;

    public string $lastError = "";

    /**
     * Initializes a new instance of the Profile class
     */
    function __construct (DatabaseEngine $db, Perso $perso) {
        $this->setDatabase($db);

        $this->perso = $perso;
        $this->load_from_database();
    }

    /**
     * Loads the object Profile (ie fill the properties) from the $_POST array
     */
    function load_from_form ($read_boolean = true) {
        $db = $this->getDatabase();

        if (array_key_exists('perso_id', $_POST)) {
            $this->perso = Perso::get($db, $_POST['perso_id']);
        }
        if (array_key_exists('text', $_POST)) {
            $this->text = $_POST['text'];
        }
        if (array_key_exists('updated', $_POST)) {
            $this->updated = $_POST['updated'];
        }
        if ($read_boolean) {
            if (array_key_exists('fixedwidth', $_POST)) {
                $this->fixedwidth = $_POST['fixedwidth'];
            }
        }
    }

    /**
     * Loads the object Profile (ie fill the properties) from the database
     */
    function load_from_database (): bool {
        $db = $this->getDatabase();

        $id = $db->escape($this->perso->id);
        $sql = "SELECT * FROM " . TABLE_PROFILES . " WHERE perso_id = '$id'";
        if (!($result = $db->query($sql))) {
            message_die(SQL_ERROR, "Unable to query azhar_profiles", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            $this->lastError = "Profile unknown: " . $this->perso_id;
            return false;
        }

        $this->text = $row['profile_text'];
        $this->updated = $row['profile_updated'];
        $this->fixedwidth = $row['profile_fixedwidth'];

        return true;
    }

    /**
     * Saves the object to the database
     */
    function save_to_database (): void {
        $db = $this->getDatabase();

        $perso_id = $db->escape($this->perso->id);
        $text = $db->escape($this->text);
        $updated = $db->escape($this->updated);
        $fixedwidth = $this->fixedwidth ? 1 : 0;

        $sql = "REPLACE INTO " . TABLE_PROFILES . " (`perso_id`, `profile_text`, `profile_updated`, `profile_fixedwidth`) VALUES ('$perso_id', '$text', '$updated', '$fixedwidth')";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
    }

    ///
    /// Tags
    ///

    /**
     * Gets the profile's tags
     *
     * @return string The profile's tags
     */
    function get_tags () {
        $db = $this->getDatabase();

        $id = $db->escape($this->perso->id);
        $sql = "SELECT tag_code, tag_class FROM " . TABLE_PROFILES_TAGS
            . " WHERE perso_id = '$id'";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Can't get tags", '', __LINE__, __FILE__, $sql);
        }
        $tags = [];
        while ($row = $db->fetchRow($result)) {
            $tags[$row['tag_class']][] = $row['tag_code'];
        }
        return $tags;
    }

    /**
     * Gets the profile's cached tags
     *
     * @return string The profile's tags
     */
    function get_cached_tags () {
        require_once('includes/cache/cache.php');
        $cache = Cache::load();
        $key = 'zed_profile_tags_' . $this->perso->id;
        if (!$tags_html = $cache->get($key)) {
            //Regenerates tags cached html snippet
            global $smarty;
            $tags = $this->get_tags();
            if (count($tags)) {
                $smarty->assign('tags', $tags);
                $tags_html = $smarty->fetch('profile_tags.tpl');
            } else {
                $tags_html = " ";
            }
            $cache->set($key, $tags_html);
        }
        return $tags_html;
    }
}