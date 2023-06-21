<?php

/**
 * Story hook class: example code
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This class illustrates how to use the StoryHook class.
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

$class = "DemoStoryHook";

/**
 * Story hook demo class
 */
class DemoStoryHook extends StoryHook {
    /**
     * Initializes resources
     *
     * @see StoryHook::initialize
     *
     * The initialize method is called after the constructor and is mandatory,
     * even if you've nothing to initialize, as it's an abstract method.
     */
    function initialize () {}

    /**
     * Updates the current section description.
     *
     * @param string $description the description to update
     * @see StoryHook::update_description
     *
     */
    function update_description (string &$description) {
        //Performs the rot13 transform of the current description
        $description = str_rot13($description);

        //Appends a string to the current description
        $description .= "\n\nWazzzzzzzzzzzzzzzzaaaaaaaaaaaaaaaaaaaaaa";
    }

    /**
     * Updates the current section choices
     *
     * @param Array $links the section links
     *@see StoryHook::get_choices_links
     *
     */
    function get_choices_links (array &$links) {
        //Adds a link to /push
        $links[] = [lang_get("PushMessage"), get_url('push')];
    }

    /**
     * Adds content after our story content block
     *
     * @see StoryHook::add_html
     */
    function add_html () {
        //Adds a html block
        return '<div class="black">Lorem ipsum dolor</div>';
    }
}
