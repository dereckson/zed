<?php

/**
 * Content file class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2012-12-03 02:57    Forked from Content
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
 * Content class
 *
 * This class maps the content_files table.
 *
 */
class ContentFile {

/*  -------------------------------------------------------------
    Properties
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    public $id;
    public $path;
    public $user_id;
    public $perso_id;
    public $title;

/*  -------------------------------------------------------------
    Constructor, __toString
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Initializes a new ContentFile instance
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
        return $this->title ?: $this->path;
    }

/*  -------------------------------------------------------------
    Load/save class
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    /**
     * Loads the object ContentFile (ie fill the properties) from the $_POST array
     *
     * @param boolean $allowSensibleFields if false, allow only title to be defined ; otherwise, allow all fields.
     */
    function load_from_form ($allowSensibleFields = false) {
        if (array_key_exists('title', $_POST)) {
            $this->title = $_POST['title'];
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
        }
    }

    /**
     * Loads the object ContentFile (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $id = $db->sql_escape($this->id);
        $sql = "SELECT * FROM content_files WHERE content_id = '" . $id . "'";
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

        //Updates or inserts
        $sql = "REPLACE INTO content_files (`content_id`, `content_path`, `user_id`, `perso_id`, `content_title`) VALUES ($id, '$path', '$user_id', '$perso_id', '$title')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't save content", '', __LINE__, __FILE__, $sql);
        }

        if (!$this->id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
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
        $ext = strtolower($ext);
        return (is_valid_image_extension($ext) || is_valid_audio_extension($ext)
                || is_valid_video_extension($ext));
    }

    /**
     * Determines if the extension is valid
     *
     * @param string $ext The extension (without dot)
     * @return boolean true if this extension is valid ; otherwise, false.
     */
    function is_valid_image_extension ($ext) {
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
     * Determines if the extension is a valid audio file one
     *
     * @param string $ext The extension (without dot)
     * @return boolean true if this extension is valid ; otherwise, false.
     */
    function is_valid_audio_extension ($ext) {
        switch ($ext = strtolower($ext)) {
            //Sounds (HTML5 <audio> formats)
            case 'mp3':
            case 'ogg':
            case 'aac':
            case 'wav':
            case 'wave':
                return true;

            //Denied extension
            default:
                return false;
        }
    }

    /**
     * Determines if the extension is a valid video file one
     *
     * @param string $ext The extension (without dot)
     * @return boolean true if this extension is valid ; otherwise, false.
     *
     * @todo add H.264 extension
     */
    function is_valid_video_extension ($ext) {
        switch ($ext = strtolower($ext)) {
            //Video (HTML5 <video> formats)
            case 'ogg':
            case 'webm':
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
     *
     * @todo set contents chmod in config
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
}
