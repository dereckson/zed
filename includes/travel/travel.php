<?php

/*
 * Travel helper class
  *
 * 0.1    2010-07-18 22:05    DcK
 *
 * @package Zed
 * @subpackage Travel
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 * This class implements a singleton pattern.
 *
 */

class Travel {
    /*
     * @var Array array of TravelPlace, each one a custom travel rule
     * This array is indeed by TravelPlace code.
     */    
    public $globalTravelTo = array();
    
    
    /*
     * Gets and initializes if needed the Travel instance
     */
    static function load () {
        require_once('includes/cache/cache.php');
        $cache = Cache::load();
        
        if (!$travel = $cache->get('zed_travel')) {
            //Initializes resource and caches it
            $travel = new Travel();
            $travel>load_xml("content/travel.xml");
            $cache->set('zed_travel', serialize($travel));
            return $travel;
        }
        
        return unserialize($travel);
    }
    
    /*
     * Loads a travel configuration XML file
     * @param string the path to the travel XML file 
     */
    function load_xml ($file) {
        $xml = simplexml_load_file($file);
        foreach ($xml->TravelPlace as $travelPlaceXml) {
            $travelPlace = TravelPlace::from_xml($travelPlaceXml);
            $this->globalTravelTo[$travelPlace->code] = $travelPlace;
        }
    }
    
    /*
     * Determines if a perso can travel from $from to $to
     * If an alias have been used for $to local location, set correct location.
     * 
     * @param GeoLocation the location where the perso is
     * @param GeoLocation the location where the perso wants to go
     * @return true if the travel move is valid ; otherwise, false.
     */
    function can_travel ($from, &$to) {
        if ($from->global != $to->global) {
            //Checks if we can locally from $from to $to place
            if (!array_key_exists($from->global, $this->globalTravelTo)) {
                return false;
            }
            $travelPlace = $this->globalTravelTo[$from->global];
            if (!in_array($to->global, $travelPlace->globalTravelTo)) {
                return false;
            }
        }
        
        if ($to->containsLocalLocation) {
            //Determines if we've custom rules about local moves in $to
            if (!array_key_exists($to->global, $this->globalTravelTo)) {
                return false;
            }
            $travelPlace = $this->globalTravelTo[$to->global];
            
            //Is it's an especially allowed movement?
            foreach ($travelPlace->localMoves as $move) {
                //move is a [location, alias, name]  array
                //If any of those 3 parameters matches $to->local, it's okay
                if (in_array($to->local, $move)) {
                    $to->local = $move[0];
                    return true;
                }
            }
            
            if ($travelPlace->freeLocalMove) {
                //We can move freely, perfect
                return true;
            }
        }
        
        return false;
    }
}

?>