<?php
/**
 * Story hook class
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
 * Story hook class
 *
 * This class allows to hook PHP code to a textual story.
 *
 * It allows the story to be completed by site elements.
 *
 * For a class implementation example:
 * @see DemoStoryHook
 */
abstract class StoryHook {
    /**
     * The current story
     *
     * @var Story
     */
    public $story;

    /*
     * The current story section
     *
     * @var StorySection
     */
    public $section;

    /**
     * The character involved in the story
     *
     * @var Perso
     */
    public $perso;

    /**
     * Constructor
     *
     * @param Story $story The story including this hook
     * @param StorySection $section The section including this hook
     */
    function __construct ($story, $section) {
        $this->story = $story;
        $this->section = $section;
        $this->perso = $GLOBALS['CurrentPerso'];

        $this->initialize();
    }

    /**
     * Initializes hook. Called after constructor.
     */
    abstract function initialize ();

    /**
     * Gets choices extra links
     *
     * @param Array $links the hooks links array
     */
    function get_choices_links (&$links) {}

    /**
     * Updates description
     *
     * @param string the description text (from section and previous hooks)
     */
    function update_description (&$description) {}

    /**
     * Adds HTML code *AT THE END* of the story content block
     *
     * @return string HTML code to print
     */
    function add_content () {}

    /**
     * Adds HTML code *AFTER* the content block
     *
     * @return string HTML code to print
     */
    function add_html () {}
}
