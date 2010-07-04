<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Settings
 */

//The method to call in your objects, to save data.
define("SETTINGS_SAVE_METHOD", "save_to_database");

require_once("page.php");

/*
 * @package Zed
 * @subpackage settings
 */
class Settings {
    
    /*
     * @var string the file path
     */
    public $file;
    
    /*
     * @var Array a collection of SettingsPage items.
     */
    public $pages;
    
    /*
     * Initializes a new instance of Settings class
     */
    function __construct ($xmlFile) {
        //Opens .xml
        if (!file_exists($xmlFile)) {
            message_die(GENERAL_ERROR, "$xmlFile not found.", "Settings load error");
        }
        
        $this->file = $xmlFile;
        $this->parse();
    }

    /*
     * Parses XML file
     */
    function parse () {
        //Parses it
        $xml = simplexml_load_file($this->file);
        foreach ($xml->page as $page) {
            //Gets page
            $page = SettingsPage::from_xml($page);
                        
            //Adds to sections array
            $this->pages[$page->id] = $page;
        }
    }
}
?>