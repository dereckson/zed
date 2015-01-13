<?php

/**
 * Explore current location
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This is a redirect controller to call the relevant controller,
 * according to the location.
 *
 * It handles /explore URL
 *
 * @package     Zed
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

//
// Determines the kind of content user wants to explore
//

$explore_controller = '';

if (file_exists(STORIES_DIR . '/' . $CurrentPerso->location_global . '.xml')) {
    $explore_controller = 'story';
}

//
// No relevant controller found
//

if ($explore_controller == '') {
    message_die(GENERAL_ERROR, "<p>Congratulations! You've just found a shiny new place.</p><p>You've so the opportunity to define what this place should be, writing a story, preparing a puzzle or some images.</p><p>If you're interested, contact Dereckson.</p>", "Exploration error");
}

//
// Calls relevant controller
//

include($explore_controller . '.php');

?>