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

namespace Zed\Models\Geo;

use Zed\Models\Content\Zone;

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
class Scene extends Entity {
    /**
     * Last error or warning
     *
     * @var string
     */
    public $lastError = "";

    /**
     * File scene to serve
     *
     * @var string
     */
    public $sceneFile;

    /**
     * The location of the scene to print
     *
     * @var Location
     */
    public $location;

    /**
     * Initializes a new Scene instance
     *
     * @param Location $location location the scene is to print
     */
    function __construct ($location) {
        $this->setDatabase($location->getDatabase());

        $this->location = $location;

        //Gets local scene
        if ($location->containsLocalLocation) {
            if ($this->get_local_scene()) {
                return;
            }
        }

        //Gets global scene
        if ($location->containsGlobalLocation) {
            if ($this->get_global_scene()) {
                return;
            }
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
        $index = SceneIndex::Load(SCENE_DIR);
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
    public static function get_file_extension (string $file) : string {
        $pathinfo = pathinfo($file);

        return $pathinfo['extension'] ?? "";
    }

    /**
     * Renders the file
     */
    public function render () {
        $db = $this->getDatabase();

        if ($file = $this->sceneFile) {
            switch ($ext = Scene::get_file_extension($file)) {
                case 'png':
                case 'jpg':
                case 'gif':
                case 'bmp':
                    echo "<img src=\"$file\" />";
                    break;

                case 'tpl':
                    global $smarty, $Config;

                    //$this->location is the object reference
                    //Some objects like the hypership move, so we also need to know where there are.
                    //From the template, this object location is assigned to $location
                    //To get $this->location from template, use $CurrentPerso->location
                    if ($this->location->body) {
                        $smarty->assign("location", new Location($db, $this->location->body->location));
                    } elseif ($this->location->ship) {
                        $smarty->assign("location", new Location($db, $this->location->ship->location));
                    }

                    //Gets zone information
                    if ($zone = Zone::at($db, $this->location->global, $this->location->local)) {
                        $smarty->assign('zone', $zone);
                    }

                    //Scene-specific variables
                    $smarty->assign("SCENE_URL", defined('SCENE_URL') ? SCENE_URL : '/' . SCENE_DIR);
                    if ($Config['builder']['hotglue']['enable']) {
                         $smarty->assign("HOTGLUE", $Config['builder']['hotglue']['URL']);
            }
                    lang_load('scenes.conf', $this->location->global);

                    //Displays scene
                    $smarty->display($file);
                    break;

                case 'php':
                    message_die(HACK_ERROR, ".php scene files not allowed without review", '', __LINE__, __FILE__);

                default:
                    message_die(GENERAL_ERROR, "Can't handle $ext extension for $file scene", 'Scene render error', __LINE__, __FILE__);
            }
            echo "\n\n";
        }
    }

    /**
     * Tries to get the scene file.
     *
     * It will try to find in the scene directory a file with $code as name,
     * and .tpl .png .gif .bmp .swf .html or .php as extension.
     *
     * @param string the location code (and filename)
     * @return bool true if a scene file have been found and set ; otherwise, false.
     */
    private function try_get_scene ($code) {
        $file = SCENE_DIR . "/$code";
        $extensions = ['tpl', 'png', 'jpg', 'gif', 'bmp', 'swf', 'html', 'php'];
        foreach ($extensions as $ext) {
            if (file_exists("$file.$ext")) {
                $this->sceneFile = "$file.$ext";
                return true;
            }
        }
        return false;
    }
}
