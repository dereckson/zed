<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Story hook
 */

abstract class StoryHook {
    /*
     * @var Story the current story
     */
    public $story;
    
    /*
     * @var StorySection the current story section
     */
    public $section;
    
    /*
     * @var Perso the character involved in the story
     */
    public $perso;
    
    function __construct ($story, $section) {
        $this->story = $story;
        $this->section = $section;
        $this->perso = $GLOBALS['CurrentPerso'];
        
        $this->initialize();
    }
    
    /* Initializes hook. Called after constructor */
    abstract function initialize ();
    
    /*
     * Gets choices extra links
     * @param Array $links the hooks links array
     */
    function get_choices_links (&$links) {}
    
    /*
     * Updates description
     * @param string the description text (from section and previous hooks)
     */
    function update_description (&$description) {}

    /*
     * Adds HTML code *AT THE END* of the story content block
     * @return string HTML code to print
     */
    function add_content () {}
    
    /*
     * Adds HTML code *AFTER* the content block
     * @return string HTML code to print
     */
    function add_html () {}
}

?>