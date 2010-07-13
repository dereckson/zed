<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Profile class.
 *
 * 0.1    2010-01-02 16:49    Autogenerated by Pluton Scaffolding
 *                            Import from Azh�r
 * 0.2    2010-07-05 03:56    Tags
 *
 */

class Profile {

    public $perso_id;
    public $text;
    public $updated;
    public $fixedwidth;
    
    function __construct ($perso_id) {
        $this->perso_id = $perso_id;
        $this->load_from_database();
    }
    
    //Loads the object Profile (ie fill the properties) from the $_POST array
    function load_from_form ($read_boolean = true) {
        if (array_key_exists('perso_id', $_POST)) $this->perso_id = $_POST['perso_id'];
        if (array_key_exists('text', $_POST)) $this->text = $_POST['text'];
        if (array_key_exists('updated', $_POST)) $this->updated = $_POST['updated'];
        if ($read_boolean) {
            if (array_key_exists('fixedwidth', $_POST)) $this->fixedwidth = $_POST['fixedwidth'];
        }
    }
    
    //Loads the object Profile (ie fill the properties) from the database
    function load_from_database () {
        global $db;
        $id = $db->sql_escape($this->perso_id);
        $sql = "SELECT * FROM " . TABLE_PROFILES . " WHERE perso_id = '$id'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query azhar_profiles", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Profile unkwown: " . $this->perso_id;
            return false;
        }
        
        $this->text = $row['profile_text'];
        $this->updated = $row['profile_updated'];
        $this->fixedwidth = $row['profile_fixedwidth'];
        
        return true;
    }
    
    //Saves the object to the database
    function save_to_database () {
        global $db;
        
        $perso_id = $db->sql_escape($this->perso_id);
        $text = $db->sql_escape($this->text);
        $updated = $db->sql_escape($this->updated);
        $fixedwidth = $this->fixedwidth ? 1 : 0;
        
        $sql = "REPLACE INTO " . TABLE_PROFILES . " (`perso_id`, `profile_text`, `profile_updated`, `profile_fixedwidth`) VALUES ('$perso_id', '$text', '$updated', '$fixedwidth')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }
    }
    
    ///
    /// Tags
    ///
    
    function get_tags () {
        global $db;
        $id = $db->sql_escape($this->perso_id);
        $sql = "SELECT tag_code, tag_class FROM " . TABLE_PROFILES_TAGS
             . " WHERE perso_id = '$id'";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't get tags", '', __LINE__, __FILE__, $sql);
        }
        $tags = array();
        while ($row = $db->sql_fetchrow($result)) {
            $tags[$row['tag_class']][] = $row['tag_code'];
        }
        return $tags;
    }
    
    function get_cached_tags () {
        require_once('includes/cache/cache.php');
        $cache = Cache::load();
        $key = 'zed_profile_tags_' . $this->perso_id;
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
    
?>