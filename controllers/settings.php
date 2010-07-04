<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Settings
 *
 * TODO: replace the on the fly preferences.xml code generation by a
 *       cached code generation
 * 
 * TODO: reduce the number of for loops in this controller
 */

//
// Loads settings
//

lang_load('settings.conf');

include('includes/settings/settings.php');
$settings = new Settings('includes/settings/preferences.xml');

//Selects relevant settings page
$pages = $settings->pages;
if (count($url) > 1) {
    //From url: /settings/account -> page account
    if (array_key_exists($url[1], $settings->pages)) {
        $page = $pages[$url[1]];
    } else {
        message_die(GENERAL_ERROR, "/settings/$url[1] isn't a valid setting page");
    }
} else {
    //Default page
    $page = array_shift($pages);
}

//Pages links
foreach ($settings->pages as $tmppage) {
    $pagesLinks[$tmppage->id] = $tmppage->title;
}

//
// Handles form
//
if (array_key_exists('settings_page', $_POST)) {
    if ($_POST['settings_page'] == $page->id) {
        //Updates settings
        $errors = array();
        $page->handle_form($errors);
        if (count($errors)) {
            //Prints error message
            $smarty->assign('WAP', implode('<br />', $errors));
        }
    } else {
        //The field settings.page isn't the current page
        //Prints an HACK_ERROR to avoid to save properties with the same names.
        $id_current = $page->id;
        $id_toSave  = $_POST['settings_page'];
        message_die(HACK_ERROR, "You're on /settings/$id_current but you want to update /settings/$id_toSave");
    }
}

//
// HTML output
//

//Serves header
define('DIJIT', true);
$title = lang_get('Settings');
$smarty->assign('PAGE_TITLE', $title);
include('header.php');

//Serves settings page;
$smarty->assign('page', $page);
$smarty->assign('pages', $pagesLinks);
$smarty->display('settings_page.tpl');

//Servers footer
include('footer.php');
 
?>