<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Story choice
 */

class StoryChoice {
    public $goto;
    public $text;
    public $guid;
    
    function __construct () {
        //The guid allows to build temporary URLs to get to right choice
        $this->guid = new_guid();
    }
    
    function __toString () {
        return $this->text;
    }
    
    /*
     * Initializes a new instance of StoryChoice class from a XML element
     * @param SimpleXMLElement the xml element to parse
     * @return StoryChoice the story choice class
     */
    static function from_xml ($xml) {
        $choice = new StoryChoice();
        
        //Parses attribute
        foreach ($xml->attributes() as $key => $value) {
            switch ($key) {
                case 'goto':
                    $choice->$key = (string)$value;
                    break;
    
                default:
                    message_die(GENERAL_ERROR, "Unknown attribute: $key = \"$value\"", "Story error");
            }
        }
        
        //Parses content
        $choice->text = (string)$xml;

        return $choice;
    }
}