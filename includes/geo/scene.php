<?php

/*
 * Geo scene class
 *
 * 0.1    2010-01-30 17:42    DcK
 *
 * @package Zed
 * @subpackage Geo
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 */

require_once('location.php');
if (!defined('SCENE_DIR')) define('SCENE_DIR', 'content/scenes');

class GeoScene {
    /*
     * @var string Last error warning
     */
    public $lastError;
    
    /*
     * @var string File scene to serve
     */
    public $sceneFile;
       
    /*
     * @var GeoLocation the location to print the scene
     */
    public $location;
    
    /*
     * Initializes a new GeoScene instance
     * @param GeoLocation $location location the scene is to print
     */
    function __construct ($location) {
        $this->location = $location;
 
        //Gets local scene
        if ($location->containsLocalLocation) {
            if ($this->get_local_scene()) return;
        }
        
        //Gets global scene
        if ($location->containsGlobalLocation) {
            if ($this->get_global_scene()) return;
        }
        
        //If not scene found, let's set a warning
        $this->lastError = "No scene found.";
    }
    
    /*
     * Gets local scene
     * @return boolean true if a scene have been found ; otherwise, false.
     */
    private function get_local_scene () {
        return false;
    }
    
    /*
     * Gets global scene
     * @return boolean true if a scene have been found ; otherwise, false.
     */
    private function get_global_scene () {
        $location = $this->location;
        if ($location->place) {
            if ($this->try_get_scene($location->global)) {
                return true;
            }
        }
        if ($location->body) {
            if ($this->try_get_scene('B' . $location->body->code)) {
                return true;
            }
        }
        return false;
    }
    
    public static function get_file_extension ($file) {
        $pathinfo = pathinfo($file);
        return $pathinfo['extension'];
    }
    
    public function render () {
        if ($file = $this->sceneFile) {
            switch ($ext = GeoScene::get_file_extension($file)) {
                case 'png':
                case 'jpg':
                case 'gif':
                case 'bmp':
                    echo "<img src=\"$file\" />";
                    break;
                    
                case 'tpl':
                    global $smarty;
                    $template_dir = $smarty->template_dir;
                    $smarty->template_dir = getcwd();
                    
                    //$this->location is the object reference
                    //Some objects like the hypership move, so we also need to know where there are.
                    //From the template, this object location is assigned to $location
                    //To get $this->location from template, use $CurrentPerso->location
                    if ($this->location->body) {
                        $smarty->assign("location", new GeoLocation($this->location->body->location));
                    } elseif ($this->location->ship) {
                        $smarty->assign("location", new GeoLocation($this->location->ship->location));
                    }
                    
                    $smarty->assign("SCENE_URL", defined('SCENE_URL') ? SCENE_URL : '/' . SCENE_DIR);
                    lang_load('scenes.conf', $this->location->global);
                    $smarty->display($file);
                    $smarty->template_dir = $template_dir;
                    break;
                
                case 'php':
                    message_die(HACK_ERROR, ".php scene files not allowed without review", '', __LINE__, __FILE__);
                    
                default:
                    message_die(GENERAL_ERROR, "Can't handle $ext extension for $file scene", 'GeoScene render error', __LINE__, __FILE__);
            }
            echo "\n\n";
        }
    }
    
    private function try_get_scene ($code) {
        $file = SCENE_DIR . "/$code";
        $extensions = array('tpl', 'png', 'jpg', 'gif', 'bmp', 'swf', 'html', 'php');
        foreach ($extensions as $ext) {
            if (file_exists("$file.$ext")) {
                $this->sceneFile = "$file.$ext";
                return true;
            }
        }
        return false;
    }
}

?>