<?php

/**
 * Story
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This controller is a storytelling engine allowing to explore a place like
 * in fighting fantasy gamebooks / livres dont vous êtes le héros.
 *
 * This controller is called by the explore controller and handles /explore URL.
 *
 * It uses the Story classes, the story.tpl view and content/stories/*.xml as
 * stories content datasource.
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
 *
 * @todo Create a separate release from Zed based on this controller and the Story library.
 * @todo Add code to handle actions defined in choices like inventory management
 */

//
// Helper method
//

/**
 * Gets section to print to the user
 * @param Story the story the section to get (yep, we could've global $story)
 * @return StorySection the section, or null if not found
 */
function get_section ($story) {
    global $url, $smarty, $CurrentPerso;

    //If the URL contains a choice guid, use it as story progress source
    //e.g. /explore/143f7200-766b-7b8b-e3f4-9fbfeeaeb5dd
    if (count($url) > 1) {
        $guid = $url[1];

        //Ensures we've a StorySection object in the Story variable
        if (!array_key_exists('StoryChoices', $_SESSION)) {
            $smarty->assign('WAP', lang_get('ExpiredStorySession'));
        } else {
            //Gets StoryChoice (creating a dummy section to use get_choice method)
            $section = new StorySection("void");
            $section->choices = $_SESSION['StoryChoices'];
            if (!$choice = $section->get_choice($guid)) {
                $smarty->assign('WAP', lang_get('InvalidStoryGUID'));
            }

            //TODO: add code here to handle actions defined in choices
            //e.g. item added to inventory

            //Gets section
            if ($section_id = $choice->goto) {
                if (!array_key_exists($section_id, $story->sections)) {
                    message_die(GENERAL_ERROR, "Choice <em>$choice->text</em> redirects to <em>$section_id</em> but this section doesn't exist.", "Story error");
                }
                return $story->sections[$section_id];
            }
        }
    }

    if (!$CurrentPerso->location_local) {
        //Gets start section
        return $story->get_start_section();
    }

    //Gets section matching perso location
    return $story->get_section_from_location($CurrentPerso->location_local);
}

//
// Opens .xml file
//

$file = STORIES_DIR . '/' . $CurrentPerso->location_global . '.xml';
if (!file_exists($file)) {
    message_die(GENERAL_ERROR, "If you want to write a story for this place, contact Dereckson — $file", "No story defined");
}

//
// Gets story
//

//Loads story and tries to get the section
require_once('includes/story/story.php');
$story = new Story($file);
$section = get_section($story);

//Ensures we've a section
if (!$section) {
    message_die(GENERAL_ERROR, "Nothing to do at this location. Contact Dereckson if you think it's a bug or you want to write a story here.", "Story");
}

//Performs section actions
if ($section->location_local) {
    //Moves perso to section local location
    $CurrentPerso->move_to(null, $section->location_local);
}

//Saves section in session, for choices handling
$_SESSION['StoryChoices'] = $section->choices;

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', $story->title);
include('header.php');

//Serves content
$smarty->assign("section", $section);
$smarty->display('story.tpl');

//Serves footer
$smarty->assign('screen', "Story, section $section->id");
include('footer.php');
