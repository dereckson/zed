<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Story
 */

require_once('section.php');

/*
 * @package Zed
 * @subpackage story
 */
class Story {
    /*
     * @var string the file path
     */
    public $file;
    
    /*
     * @var string the story title
     */
    public $title;
    /*
     * @var Array an array of StorySection elements
     */
    public $sections = array();
    
    /*
     * @var SimpleXMLElement the SimpleXML parser
     */
    private $xml;
    
    /*
     * @var string the index of start section in sections array
     */
    private $startSection = null;
    
    /*
     * @var Array an array of StorySection elements, indexed by location
     */
    private $sectionsByLocation = array();
    
    function __construct ($file) {
        //Opens .xml
        if (!file_exists($file)) {
            message_die(GENERAL_ERROR, "$file not found.", "Story loading error");
        }
        
        $this->file = $file;
        $this->parse();        
    }
    
    /*
     * Gets start section
     * @return StorySection the section where the story starts, or null if not defined
     */
    function get_start_section () {
        return ($this->startSection != null) ? $this->sections[$this->startSection] : null;
    }
    
    /*
     * Gets section from local location
     * @return StorySection the default section at this location, or null if not defined
     */
    function get_section_from_location ($location) {
        return array_key_exists($location, $this->sectionsByLocation) ? $this->sectionsByLocation[$location] : null;
    }
    
    /*
     * Parses XML file
     */
    function parse () {
        //Parses it
        $this->xml = simplexml_load_file($this->file);
        $this->title = (string)$this->xml->title;
        foreach ($this->xml->section as $section) {
            //Gets section
            $section = StorySection::from_xml($section, $this);
            
            //Have we a start section?
            if ($section->start) {
                //Ensures we've only one start section
                if ($this->startSection != null) {
                    message_die(GENERAL_ERROR, "Two sections have start=\"true\": $section->id and $this->startSection.", "Story error");
                }
                $this->startSection = $section->id;
            }
            
            //By location
            if ($section->location_local) {
                $this->sectionsByLocation[$section->location_local] = $section;
            }
            
            //Adds to sections array
            $this->sections[$section->id] = $section;
        }
    }
}



?>