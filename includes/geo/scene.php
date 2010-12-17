<?php

/**
 * Geo scene class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  Geo
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

require_once('location.php');
require_once('octocube.php');
require_once('sceneindex.php');

if (!defined('SCENE_DIR')) {
    /**
     * The directory containing scenes files
     */
    define('SCENE_DIR', 'content/scenes');
}

/**
 * Geo scene class
 *
 * This class provides methods to determine and renders the local scene.
 */
class GeoScene {
    /**
     * Last error or warning
     *
     * @var string
     */
    public $lastError;

    /**
     * File scene to serve
     *
     * @var string
     */
    public $sceneFile;

    /**
     * The location of the scene to print
     *
     * @var GeoLocation
     */
    public $location;

    /**
     * Initializes a new GeoScene instance
     *
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

    /**
     * Gets local scene
     *
     * @return boolean true if a scene have been found ; otherwise, false.
     */
    private function get_local_scene () {
        //From the index
        $index = GeoSceneIndex::Load(SCENE_DIR);
        if ($tpl = $index->get_local_template($this->location->global, $this->location->local)) {
            $this->sceneFile = SCENE_DIR . '/' . $tpl;
            return true;
        }
        
        //From filename
        $expression = $this->location->global . ' ' . $this->location->local;
        if ($this->try_get_scene($expression)) {
            return true;
        }
        return false;
    }

    /**
     * Gets global scene
     *
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

    /**
     * Gets file extension
     *
     * @param string $file the file path
     * @return string the file extension
     */
    public static function get_file_extension ($file) {
        $pathinfo = pathinfo($file);
        return $pathinfo['extension'];
    }

    /**
     * Renders the file
     *
     * @todo Add standard code to render .swf Flash/ShockWave files.
     */
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

    /**
     * Tries to get the scene file.
     *
     * It will tries to find in the scene directory a file with $code as name,
     * and .tpl .png .gif .bmp .swf .html or .php as extension.
     *
     * @param string the location code (and filename)
     * @return bool true if a scene file have been found and set ; otherwise, false.
     */
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
    
    /**
     * Reads scene templates and indexes information
     */
    public static function index_scene_templates (&$global_templates, &$local_templates, &$updated) {
        $global_templates = array();
        $local_templates = array();
        $updated = filemtime(SCENE_DIR);
        if ($handle = opendir(SCENE_DIR)) {
            while (false !== ($file = readdir($handle))) {
                 if (GeoScene::get_file_extension($file) == 'tpl') {
                    $template = file_get_contents(SCENE_DIR . '/' . $file, false, NULL, 0, 1024);
                    $location = self::get_template_location($template);
                    if ($location[1] == NULL) {
                        $global_templates[$location[0]] = $file;
                    } else {
                        $local_templates[$location[0]][$location[1]] = $file;
                    }
                 }
            }
            closedir($handle);
        }
    }
    
    private static function get_template_location ($template) {
        $location = array(NULL, NULL);
        
        //Gets global location
        $pos1 = strpos($template, "Global location: ") + 17;
        $pos2 = strpos($template, "\n", $pos1);
        $location[0] = trim(substr($template, $pos1, $pos2 - $pos1));
        
        //Gets local location
        $pos1 = strpos($template, "Local location: ");
        if ($pos1 !== false) {
            $pos1 += 16;
            $pos2 = strpos($template, "\n", $pos1);
            $location[1] = trim(substr($template, $pos1, $pos2 - $pos1));
        }
       
        return $location;
    }
}

?>