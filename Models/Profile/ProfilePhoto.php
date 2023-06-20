<?php

/**
 * Profile photo class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-01-03 21:00    Autogenerated by Pluton Scaffolding
 * 0.2    2010-02-02 00:52    Thumbnail ImageMagick generation code
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
use Zed\Models\Objects\Perso;

/**
 * Profile photo class
 *
 * This class maps the profile_photos table.
 *
 * It also provides helper methods to handle avatars or get all the photos
 * from a specified perso.
 */
class ProfilePhoto extends Entity {

    public $id;
    public $perso_id;
    public $name;
    public $description;
    public $avatar;

    public string $lastError = "";

    /**
     * Initializes a new instance of the ProfilePhoto class
     */
    function __construct (DatabaseEngine $db, $id = '') {
        $this->setDatabase($db);

        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        }
    }

    /**
     * Loads the object photo (ie fill the properties) from the $_POST array
     *
     * @param bool $readBoolean if false, don't read the bool avatar field to avoid to set by error false if the field weren't in the form.
     */
    function load_from_form ($readBoolean = true) {
        if (array_key_exists('perso_id', $_POST)) {
            $this->perso_id = $_POST['perso_id'];
        }
        if (array_key_exists('name', $_POST)) {
            $this->name = $_POST['name'];
        }
        if (array_key_exists('description', $_POST)) {
            $this->description = $_POST['description'];
        }
        if ($readBoolean) {
            $this->avatar = (bool)$_POST['avatar'];
        }
    }

    /**
     * Loads the object photo (ie fill the properties) from the database
     */
    function load_from_database () : bool {
        $db = $this->getDatabase();

        $id = $db->escape($this->id);
        $sql = "SELECT * FROM " . TABLE_PROFILES_PHOTOS . " WHERE photo_id = '" . $id . "'";
        if (!($result = $db->query($sql))) {
            message_die(SQL_ERROR, "Unable to query azhar_profiles_photos", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            $this->lastError = "photo unknown: " . $this->id;
            return false;
        }
        $this->perso_id = $row['perso_id'];
        $this->name = $row['photo_name'];
        $this->description = $row['photo_description'];
        $this->avatar = $row['photo_avatar'];
        return true;
    }

    /**
     * Promotes the photo to avatar
     */
    function promote_to_avatar () {
        $db = $this->getDatabase();

        //1 - locally
        $sql = "UPDATE " . TABLE_PROFILES_PHOTOS . " SET photo_avatar = 0 WHERE perso_id = " . $this->perso_id;
        $db->queryScalar($sql);
        $this->avatar = true;

        //2 - in perso table
        $perso = Perso::get($db, $this->perso_id);
        $perso->avatar = $this->name;
        $perso->save_to_database();
    }

    /**
     * Saves the object to the database
     */
    function save_to_database () : void {
        $db = $this->getDatabase();

        //Escapes fields
        $id = $this->id ? "'" . $db->escape($this->id) . "'" : 'NULL';
        $perso_id = $db->escape($this->perso_id);
        $name = $db->escape($this->name);
        $description = $db->escape($this->description);
        $avatar = $this->avatar ? 1 : 0;

        //Saves
        $sql = "REPLACE INTO " . TABLE_PROFILES_PHOTOS . " (`photo_id`, `perso_id`, `photo_name`, `photo_description`, `photo_avatar`) VALUES ($id, '$perso_id', '$name', '$description', $avatar)";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
        if (!$id) {
            //Gets new record id value
            $this->id = $db->nextId();
        }
    }

    /**
     * Deletes the photo
     */
    function delete () {
        $db = $this->getDatabase();

        //Deletes from disk
        $pic_tn = PHOTOS_DIR . '/' . $this->name;
        $pic_genuine = PHOTOS_DIR . '/tn/' . $this->name;
        unlink($pic_tn);
        unlink($pic_genuine);

        //Deletes from database
        $id = $db->escape($this->id);
        $sql = "DELETE FROM " . TABLE_PROFILES_PHOTOS . " WHERE photo_id = '$id' LIMIT 1";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Can't delete photo", '', __LINE__, __FILE__, $sql);
        }
    }

    /**
     * Generates a thumbnail using ImageMagick binary
     *
     * @return boolean true if the thumbnail command returns 0 as program exit code ; otherwise, false
     */
    function generate_thumbnail () {
        global $Config;
        $sourceFile = PHOTOS_DIR . DIRECTORY_SEPARATOR . $this->name;
        $thumbnailFile = PHOTOS_DIR . DIRECTORY_SEPARATOR . 'tn' . DIRECTORY_SEPARATOR . $this->name;
        $command = $Config['ImageMagick']['convert'] . " $sourceFile -resize 1000x80 $thumbnailFile";
        @system($command, $code);
        return ($code == 0);
    }

    /**
     * Gets photos from the specified perso
     *
     * @param int $perso_id the perso ID
     * @param bool $allowUnsafe if false, don't include not safe for work photos
     * @return ProfilePhoto[]
     */
    static function get_photos (DatabaseEngine $db, $perso_id, $allowUnsafe = true): array {
        $sql = "SELECT photo_id FROM " . TABLE_PROFILES_PHOTOS . " WHERE perso_id = " . $db->escape($perso_id);
        if (!$allowUnsafe) {
            $sql .= " AND photo_safe = 0";
        }
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Unable to get photos", '', __LINE__, __FILE__, $sql);
        }

        $photos = [];
        while ($row = $db->fetchRow($result)) {
            $photos[] = new ProfilePhoto($row[0]);
        }
        return $photos;
    }

    /**
     * Gets perso avatar
     *
     * @param integer $perso_id the perso to get the avatar ID
     * @param string $username the username to put in title tag
     */
    static function get_avatar (DatabaseEngine $db, $perso_id, $username = '') {
        $perso_id = $db->escape($perso_id);

        $sql = "SELECT photo_description, photo_name FROM " . TABLE_PROFILES_PHOTOS . " WHERE perso_id = '$perso_id' and photo_avatar = 1";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Unable to get avatar", '', __LINE__, __FILE__, $sql);
        }
        if ($row = $db->fetchRow($result)) {
            if (!$username) {
                $username = get_name($perso_id);
            }
            $description = $row['photo_description'] ? "$row[photo_description] ($username's avatar)" : "$username's avatar";
            $url = PHOTOS_URL . '/tn/' . $row['photo_name'];
            return "<img src=\"$url\" title=\"$username\" alt=\"$description\" />";
        } else {
            return null;
        }
    }
}