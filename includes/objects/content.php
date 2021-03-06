<?php

/**
 * Content class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-02-24 15:57    Autogenerated by Pluton Scaffolding
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
 *
 * @deprecated
 */

/**
 * Content class
 *
 * This class maps the content view.
 *
 * This view shows the content_files and content_locations tables.
 *
 * This class also provides helper methods, to handle files, generate thumbnails
 * or get local content from a specific location.
 *
 * [DESIGN BY CONTRACT] This class works only with the following assertions:
 *   i.  Each content have EXACTLY ONE location
 *   ii. Location fields will not be modified
 *
 * If a content have more than one location, only the first occurrence in
 * content_locations table will be considered.
 *
 * If a content have no location, it will be ignored.
 *
 * If you edit content location, then call saveToDatabase, you will create
 * a new location but future instances will contain first not deleted location.
 *
 * @todo remove dbc temporary limitations (cf. /do.php upload_content and infra)
 * @todo create a class ContentLocation and move location fields there
 * @todo validate SQL schema and add in config.php TABLE_CONTENT tables
 *
 * @deprecated
 */
class Content {

/*  -------------------------------------------------------------
    Properties
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    public $id;
    public $path;
    public $user_id;
    public $perso_id;
    public $title;

    public $location_global = null;
    public $location_local  = null;
    public $location_k      = null;

    public $perso_name;
    public $perso_nickname;

/*  -------------------------------------------------------------
    Constructor, __toString
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Initializes a new Content instance
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
     * Returns a string representation of current Content instance
     *
     * @return string the content title or path if title is blank.
     */
    function __toString () {
        return $this->title ? $this->title : $this->path;
    }

/*  -------------------------------------------------------------
    Load/save class
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Loads the object Content (ie fill the properties) from the $_POST array
     *
     * @param boolean $allowSensibleFields if false, allow only location_local, location_k and title to be defined ; otherwise, allow all fields.
     */
    function load_from_form ($allowSensibleFields = false) {
        if (array_key_exists('title', $_POST)) {
            $this->title = $_POST['title'];
        }
        if (array_key_exists('location_local', $_POST)) {
            $this->location_local = $_POST['location_local'];
        }
        if (array_key_exists('location_k', $_POST)) {
            $this->location_k = $_POST['location_k'];
        }

        if ($allowSensibleFields) {
            if (array_key_exists('path', $_POST)) {
                $this->path = $_POST['path'];
            }
            if (array_key_exists('user_id', $_POST)) {
                $this->user_id = $_POST['user_id'];
            }
            if (array_key_exists('perso_id', $_POST)) {
                $this->perso_id = $_POST['perso_id'];
            }
            if (array_key_exists('location_global', $_POST)) {
                $this->location_global = $_POST['location_global'];
            }
        }
    }

    /**
     * Loads the object Content (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM content WHERE content_id = '" . $id . "'";
        if ( !($result = $db->sql_query($sql)) ) {
            message_die(SQL_ERROR, "Unable to query content", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Content unknown: " . $this->id;
            return false;
        }
        $this->load_from_row($row);
        return true;
    }

    /**
     * Loads the object from row
     */
    function load_from_row ($row) {
        $this->id = $row['content_id'];
        $this->path = $row['content_path'];
        $this->user_id = $row['user_id'];
        $this->perso_id = $row['perso_id'];
        $this->title = $row['content_title'];
        $this->location_global = $row['location_global'];
        $this->location_local = $row['location_local'];
        $this->location_k = $row['location_k'];

        if (array_key_exists('perso_name', $row)) {
            $this->perso_name = $row['perso_name'];
        }
        if (array_key_exists('perso_nickname', $row)) {
            $this->perso_nickname = $row['perso_nickname'];
        }
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $path = $db->sql_escape($this->path);
        $user_id = $db->sql_escape($this->user_id);
        $perso_id = $db->sql_escape($this->perso_id);
        $title = $db->sql_escape($this->title);

        $location_global = ($this->location_global !== null) ? "'" . $db->sql_escape($this->location_global) . "'" : 'NULL';
        $location_local = ($this->location_local !== null) ? "'" . $db->sql_escape($this->location_local) . "'" : 'NULL';
        $location_k = ($this->location_k !== null) ? "'" . $db->sql_escape($this->location_k) . "'" : 'NULL';

        //Updates or inserts
        $sql = "REPLACE INTO content_files (`content_id`, `content_path`, `user_id`, `perso_id`, `content_title`) VALUES ($id, '$path', '$user_id', '$perso_id', '$title')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't save content", '', __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }

        //Saves location
        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $sql = "REPLACE INTO content_locations (location_global, location_local, location_k, content_id) VALUES ($location_global, $location_local, $location_k, $id)";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't save content location", '', __LINE__, __FILE__, $sql);
        }
    }

/*  -------------------------------------------------------------
    File handling helper methods
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Determines if the extension is valid
     *
     * @param string $ext The extension (without dot)
     * @return boolean true if this extension is valid ; otherwise, false.
     */
    function is_valid_extension ($ext) {
        switch ($ext = strtolower($ext)) {
            //Pictures
            case 'jpg':
            case 'gif':
            case 'png':
            case 'bmp':
            case 'xbm':
                return true;

            //Denied extension
            default:
                return false;
        }
    }

    /**
     * Creates a directory
     *
     * @param string $dir the directory to create
     */
    function create_directory ($directory) {
        if (!file_exists($directory)) {
            @mkdir($directory); //Creates new directory, chmod 777
        }
    }

    /**
     * Handles uploaded file
     *
     * @return bool true if the file have been handled
     */
    function handle_uploaded_file ($fileArray) {
        if (count($fileArray) && $fileArray['error'] == 0) {
            $this->create_directory("content/users/$this->user_id");
            $this->path = "content/users/$this->user_id/$fileArray[name]";
            if (!self::is_valid_extension(get_extension($fileArray[name]))) {
                return false;
            }
            if (move_uploaded_file($fileArray['tmp_name'], $this->path)) {
                return true;
            } else {
                $this->path = null;
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Generates a thumbnail using ImageMagick binary
     *
     * @return boolean true if the thumbnail command returns 0 as program exit code ; otherwise, false
     */
    function generate_thumbnail () {
        global $Config;

        //Builds thumbnail filename
        $sourceFile = $this->path;
        $pos = strrpos($this->path, '.');
        $thumbnailFile = substr($sourceFile, 0, $pos) . 'Square' . substr($sourceFile, $pos);

        //Executes imagemagick command
        $command = $Config['ImageMagick']['convert'] . " \"$sourceFile\" -resize 162x162 \"$thumbnailFile\"";
        @system($command, $code);

        //Returns true if the command have exited with errorcode 0 (= ok)
        return ($code == 0);
    }

/*  -------------------------------------------------------------
    Gets content
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Gets content at specified location
     *
     * @param string $location_global global content location
     * @param string $location_local   local content location
     * @return Array array of Content instances
     */
    static function get_local_content ($location_global, $location_local) {
        global $db;

        //Get contents at this location
        $location_global = $db->sql_escape($location_global);
        $location_local  = $db->sql_escape($location_local);

        $sql = "SELECT c.*, p.perso_nickname, p.perso_name FROM content c, persos p WHERE c.location_global = '$location_global' AND c.location_local = '$location_local' AND p.perso_id = c.perso_id ORDER BY location_k ASC";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get content", '', __LINE__, __FILE__, $sql);
        }

        //Fills content array
        $contents = [];
        while ($row = $db->sql_fetchrow($result)) {
            $content = new Content();
            $content->load_from_row($row);
            $contents[] = $content;
        }

        return $contents;
    }

}
