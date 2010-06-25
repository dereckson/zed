<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Story hook example code
 */

$class = "DemoStoryHook";

class DemoStoryHook extends StoryHook {
    function initialize () {}
    
    function update_description (&$description) {
        //Performs the rot13 transform of the current description
        $description = str_rot13($description);
        
        //Appends a string to the current description
        $description .= "\n\nWazzzzzzzzzzzzzzzzaaaaaaaaaaaaaaaaaaaaaa";
    }
    
    function get_choices_links (&$links) {
        //Adds a link to /push
        $links[] = array(lang_get("PushMessage"), get_url('push'));
    }
    
    function add_html () {
        //Adds a html block
        return '<div class="black">Lorem ipsum dolor</div>';
    }
}
?>