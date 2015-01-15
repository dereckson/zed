<?php

/**
 * Story section class
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

require_once('choice.php');
require_once('hook.php');

/**
 * Story section class
 *
 * This class is a PHP mapping from the Story XML format's <section> tag.
 *
 * This class also a method to get the section where a specific choice links to.
 */
class StorySection {
    /**
     * The section ID
     *
     * @var string
     */
    public $id;

    /**
     * The section title
     *
     * @var string
     */
    public $title;

    /**
     * The section description
     *
     * @var string
     */
    public $description;

    /**
     * @var string the local location
     */
    public $location_local;

    /**
     * @var Array the section choices (array of StoryChoice items)
     */
    public $choices = array();

    /*
     * @var Array the section hooks (array of StoryHook items)
     */
    public $hooks = array();

    /**
     * @var boolean if true, it's the story start ; otherwise, false;
     */
    public $start;

    /**
     * @var Story the story calling the section
     */
    public $story;

    /**
     * Initializes a new instance of StorySection class
     */
    function __construct ($id, $story = null) {
        $this->id = $id;
        if ($story !== null) {
            $this->story = $story;
        }
    }

    /**
     * Gets choice from specified guid
     *
     * @return StoryChoice the wanted choice, or null if it doesn't exist
     */
    function get_choice ($guid) {
        foreach ($this->choices as $choice) {
            if ($choice->guid == $guid)
                return $choice;
        }

        return null;
    }

    /**
     * Initializes a story section from an SimpleXMLElement XML fragment
     *
     * @param SimpleXMLElement $xml the XML fragment
     * @param Story $story the calling story
     * @return StorySection the section instance
     */
    static function from_xml ($xml, $story = null) {
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

        $section = new StorySection($id, $story);
        $section->title = (string)$xml->title;
        $section->description = (string)$xml->description;
        $section->location_local = (string)$xml->local;
        $section->start = $start;

        //Adds choices
        if ($xml->choices) {
            foreach ($xml->choices->choice as $choice) {
                $section->choices[] = StoryChoice::from_xml($choice);
            }
        }

        //Adds hooks
        if ($xml->hooks) {
            foreach ($xml->hooks->hook as $hook) {
                //<hook type="spatioport" /> will assign 'spatioport' to $hook;
                $hook = (string)$hook->attributes()->type;
                require_once("hook_$hook.php");
                $section->hooks[] = new $class($section->story, $section);
            }
        }

        return $section;
    }
}

?>
