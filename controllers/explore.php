<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Explore current location
 *
 * This is a redirect controller to call the relevant controller,
 * according to the location.
 * 
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