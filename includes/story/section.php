<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Story section
 */

require_once('choice.php');

class StorySection {
    /*
     * @var string the section ID
     */
    public $id;

    /*
     * @var string the section title
     */
    public $title;

    /*
     * @var string the section description
     */
    public $description;
    
    /*
     * @var string the local location
     */
    public $location_local;
    
    /*
     * @var Array the section choices (array of StoryChoice items)
     */
    public $choices = array();
    
    /*
     * @var boolean if true, it's the story start ; otherwise, false;
     */
    public $start;
    
    function __construct ($id) {
        $this->id = $id;
    }

    /*
     * Gets choice from specified guid
     * @return StoryChoice the wanted choice, or null if it doesn't exist
     */
    function get_choice ($guid) {
        foreach ($this->choices as $choice) {
            if ($choice->guid == $guid)
                return $choice;
        }

        return null;
    }
    
    /*
     * Intializes a story section from an XML document
     * @param string $xml the XML document
     * @return StorySection the section instance
     */
    static function from_xml ($xml) {
        //Reads attributes
        $id = '';
        $start = false;
        foreach ($xml->attributes() as $key => $value) {
            switch ($key) {
                case 'start':
                    if ($value) $start = true;
                    break;
                
                case 'id':
                    $id = (string)$value;
                    break;
                
                default:
                    message_die(GENERAL_ERROR, "Unknown attribute: $key = \"$value\"", "Story error");
            }
        }
        
        if (!$id) {
            message_die(GENERAL_ERROR, "Section without id. Please add id='' in <section> tag", "Story error");
        }
        
        $section = new StorySection($id);
        $section->title = (string)$xml->title;
        $section->description = (string)$xml->description;
        $section->location_local = (string)$xml->local;
        $section->start = $start;
        if ($xml->choices) {
            foreach ($xml->choices->choice as $choice) {
                $section->choices[] = StoryChoice::from_xml($choice);
            }
        }
        return $section;
    }
}

?>
