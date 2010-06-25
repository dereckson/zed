<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Spatioport story hook
 */

require_once('includes/objects/ship.php');
require_once('includes/geo/location.php');

$class = "SpatioportStoryHook";

class SpatioportStoryHook extends StoryHook {
    public $location;
    public $location_global;
    public $location_local;
    
    function get_choices_links (&$links) {
        //$links[] = array('Examiner les vaisseaux', get_url('port','ships'));
    }
    
    function initialize () {
        $this->location_global = $this->perso->location_global;
        $this->location_local  = $this->section->location_local;
        $this->location = new GeoLocation($this->location_global, $this->location_local);
    }
    
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
    
    private function get_ships () {
        return Ship::get_ships_at($this->location_global, $this->location_local);
    }
    
    private function get_ships_in_space () {
        return Ship::get_ships_at($this->location_global, null); 
    }
}

?>