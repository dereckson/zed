<?php

/**
 * Story class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 * 
 * @package     Zed
 * @subpackage  Story
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * Story class
 *
 * This class is a PHP mapping from the Story XML format.
 *
 * This class also provides a collection of helper methods to explore the story.
 */
class Story {
    /**
     * The file path
     * 
     * @var string
     */
    public $file;
    
    /**
     * The story title
     * 
     * @var string
     */
    public $title;
    
    /**
     * An array of StorySection elements
     *
     * @var Array 
     */
    public $sections = array();
    
    /**
     * The SimpleXML parser
     *
     * @var SimpleXMLElement
     */
    private $xml;
    
    /**
     * The index of start section in sections array
     *
     * @var string
     */
    private $startSection = null;
    
    /**
     * An array of StorySection elements, indexed by location
     *
     * @var Array
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
    
    /**
     * Gets start section
     * 
     * @return StorySection the section where the story starts, or null if not defined
     */
    function get_start_section () {
        return ($this->startSection != null) ? $this->sections[$this->startSection] : null;
    }
    
    /**
     * Gets section from local location
     * 
     * @return StorySection the default section at this location, or null if not defined
     */
    function get_section_from_location ($location) {
        return array_key_exists($location, $this->sectionsByLocation) ? $this->sectionsByLocation[$location] : null;
    }
    
    /**
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