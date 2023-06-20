<?php

/**
 * Geo scene index class.
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
 *
 * This class implements a singleton pattern.
 */

namespace Zed\Models\Geo;

use Cache;

use Exception;

/**
 * Geo scene class
 *
 * This class provides an index of available scene template files.
 */
class SceneIndex {
    /**
     * Global location templates array
     *
     * Keys are global location codes.
     * Values the relevant template file.
     */
    public array $global_templates;

    /**
     * Local location templates 2D array
     *
     * Keys are global and local location codes.
     * Values the srelevant template file.
     *
     * e.g. $local_templates['B00017001']['(10, 50, 8)'] => 'B00017_port.tpl'
     */
    public array $local_templates;

    /**
     * Time of the last updated file in the scenes directory
     *
     * @var int
     */
    public $updated;

    /**
     * The directory where templates are stored
     *
     * @var string
     */
    public $directory;

    /**
     * The current index instance array
     *
     * Keys are scene directories (string)
     * Items are SceneIndex instances
     */
    static array $instance = [];

    /**
     * Gets the index instance, initializing it if needed
     *
     * @return index the index instance
     */
    static function load ($directory) {
        //Creates the index object if needed
        if (!array_key_exists($directory, self::$instance)) {
            self::$instance[$directory] = new SceneIndex($directory);
        }

        return self::$instance[$directory];
    }

    /**
     * Initializes a new SceneIndex instance
     *
     * @param string $directory the scene templates directory
     */
    public function __construct ($directory) {
        $this->directory = $directory;
        if (!$this->get_cached_information() || !$this->is_up_to_date()) {
                $this->refresh_information();
                $this->set_cached_information();
        }
    }

    /**
     * Caches index data
     */
    public function set_cached_information () {
        $cache = Cache::load();
        $cache->set('zed_sceneindex', serialize($this));
    }

    /**
     * Gets index from cache
     */
    public function get_cached_information () {
        $cache = Cache::load();
        $cached_index = $cache->get('zed_sceneindex');

        if ($cached_index === false) {
            return false;
        }

        $index = unserialize($cached_index);

        $this->global_templates = $index->global_templates;
        $this->local_templates = $index->local_templates;
        $this->updated = $index->updated;
        $this->directory = $index->directory;

        return true;
    }

    /**
     * Reads scene templates and indexes information
     */
    public function refresh_information () {
        $this->global_templates = [];
        $this->local_templates = [];
        $this->updated = filemtime($this->directory);
        if ($handle = opendir($this->directory)) {
            while (false !== ($file = readdir($handle))) {
                 if (Scene::get_file_extension($file) == 'tpl') {
                    $template = file_get_contents($this->directory . '/' . $file, false, null, 0, 1024);
                    $location = self::get_template_location($template);
                    if ($location[1] !== null) {
                        $this->local_templates[$location[0]][$location[1]] = $file;
                    } elseif ($location[0] != null) {
                        $this->global_templates[$location[0]] = $file;
                    }
                 }
            }
            closedir($handle);
        }
    }

    /**
     * Determines if the information is still up to date
     *
     * @return bool true if the information is up to date ; otherwise, false.
     */
    public function is_up_to_date () {
        return ($this->updated == filemtime($this->directory));
    }

    /**
     * Gets template location
     *
     * @return Array an string array of the location (two items; global, local)
     *  At key 0, a string with global location, or NULL if not specified
     *  At key 1, a string with local location, or NULL if not specified
     */
    private static function get_template_location ($template) {
        $location = [null, null];

        //Gets global location
        $pos1 = strpos($template, "Global location: ");
        if ($pos1 === false) {
            throw new Exception("No location in template. Any template file must contain a comment line with the string 'Global location: ' followed by the global location matching the template. It should also contain a line 'Local location: ' when applicable.");
        }
        $pos1 += 17;
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

    /**
     * Gets local template file from index
     *
     * @param string $location_global the global location
     * @param string $location_global the local location
     * @return string the relevant template scene file, or NULL if not existing
     */
    public function get_local_template ($location_global, $location_local) {
        if (isset($this->local_templates[$location_global][$location_local])) {
            return $this->local_templates[$location_global][$location_local];
        }
        return null;
    }

    /**
     * Gets global template file from index
     *
     * @param string $location_global the global location
     * @return string the relevant template scene file, or NULL if not existing
     */
    public function get_global_template ($location_global) {
        if (isset($this->global_templates[$location_global])) {
            return $this->global_templates[$location_global];
        }
        return null;
    }
}
