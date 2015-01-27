<?php
/**
 * Story hook class :: spatioport
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This class allows to hook spatioport content to a story.
 *
 * It lists the ship inside the spatioport and in the surrounding space.
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
 *
 * @todo Adds spatioport services, like ship repair & co
 * @todo Adds a map of the sky, with ship around
 * @todo Consider to use the Port class instead and to move get_ships methods there.
 */

require_once('includes/objects/ship.php');
require_once('includes/geo/location.php');

$class = 'SpatioportStoryHook';

/**
 * Spatioport story hook class
 */
class SpatioportStoryHook extends StoryHook {
    /**
     * The spatioport location
     *
     * @var GeoLocation
     */
    public $location;

    /**
     * The spatioport global location
     *
     * @var string
     */
    public $location_global;

    /**
     * The spatioport local location
     *
     * @var string
     */
    public $location_local;


    /**
     * Updates and gets the current section choices
     *
     * @param Array $links The story links
     */
    function get_choices_links (&$links) {
        //$links[] = array('Examiner les vaisseaux', get_url('port','ships'));
    }

    /**
     * Initializes instance location properties
     */
    function initialize () {
        $this->location_global = $this->perso->location_global;
        $this->location_local  = $this->section->location_local;
        $this->location = new GeoLocation($this->location_global, $this->location_local);
    }

    /**
     * Appends ship list to the story description
     */
    function add_content () {
        $ships = $this->get_ships();
        if (count($ships)) {
            echo "\n<h2>Ships</h2>";
            echo "<p>Amongst the ships are at the spatioport:</p>";
            echo "\n<ul>";
            foreach ($ships as $ship) {
                $url = get_url('ship', $ship);
                echo "\n\t<li><a href=\"$url\">$ship->name</a></li>";
            }
            echo "\n</ul>";
        }

        $ships = $this->get_ships_in_space();
        if (count($ships)) {
            echo "\n<h2>In orbit</h2>";
            $place = (string)$this->location->body;
            echo "<p>Those ships are in space around $place:</p>";
            echo "\n<ul>";
            foreach ($ships as $ship) {
                $url = get_url('ship', $ship);
                echo "\n\t<li><a href=\"$url\">$ship->name</a></li>";
            }
            echo "\n</ul>";
        }
    }

    /**
     * Get ships in the spatioports
     *
     * @param string $location_global global location
     * @param string $location_local local location
     * @return array The ships in the spatioport
     */
    private function get_ships () {
        return Ship::get_ships_at($this->location_global, $this->location_local);
    }


    /**
     * Get ships in the space surrounding the spatioport
     *
     * @param string $location_global global location
     * @param string $location_local local location
     * @return array The ships in the space around the spatioport
     */
    private function get_ships_in_space () {
        return Ship::get_ships_at($this->location_global, null);
    }
}
