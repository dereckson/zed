<?php

/**
 * Travel helper class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-07-18 22:05    DcK
 *
 * @package     Zed
 * @subpackage  Travel
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

require_once('place.php');

/**
 * Travel helper class
 *
 * The Travel class reads content/travel.xml to get travel special rules
 *
 * It so be able to provide methods determining if a move is or not valid.
 *
 * This class implements a singleton pattern.
 */
class Travel {
    /**
     * Array of TravelPlace, each one a custom travel rule
     *
     * This array is indexed by TravelPlace code.
     *
     * @var Array
     */
    public $globalTravelTo;

    /**
     * Constructor
     */
    function __construct () {
        //Initializes array
        $this->globalTravelTo = array();
    }

    /**
     * Gets and initializes if needed the Travel instance
     *
     * @return Travel the Travel instance
     */
    static function load () {
        require_once('includes/cache/cache.php');
        $cache = Cache::load();

        if (!$travel = $cache->get('zed_travel')) {
            //Initializes resource and caches it
            $travel = new Travel();
            $travel->load_xml("content/travel.xml");
            $cache->set('zed_travel', serialize($travel));
            return $travel;
        }

        return unserialize($travel);
    }

    /**
     * Loads a travel configuration XML file
     *
     * @param string the path to the travel XML file
     */
    function load_xml ($file) {
        $xml = simplexml_load_file($file);
        foreach ($xml->TravelPlace as $travelPlaceXml) {
            $travelPlace = TravelPlace::from_xml($travelPlaceXml);
            $this->globalTravelTo[$travelPlace->code] = $travelPlace;
        }
    }

    /**
     * Tries to parse the specified expression, according the rewrite rules
     * (for example defined by the <RewriteRule> xml tags)
     *
     * @param string $expression the expression to parse
     * @param GeoLocation the location where the perso is
     * @param GeoLocation the location where the perso wants to go
     *
     * @return boolean true if the expression have been parsed ; otherwise, false.
     */
    function try_parse_rewrite_rule ($expression, $from, &$to) {
        //Relevant write rules depends from the location the perso is ($from)
        $travelPlace = $this->globalTravelTo[$from->global];
        foreach ($travelPlace->rewriteRules as $rule) {
            //$rule is an array [expression, global_location, local_location]
            $subpatterns = array();
            $result = preg_match($rule[0], $expression, $subpatterns);
            if ($result > 0) {
                //$subpatterns is an array with:
                //  - at indice 0, the full matched regexp
                //  - from 1 to n, the (groups) inside the regexp
                //We need so to replace $1 by $subpatterns[1] and so on.
                for ($i = count($subpatterns) - 1 ; $i > 0 ; $i--) {
                    $rule[1] = str_replace('$' . $i, $subpatterns[$i], $rule[1]);
                    $rule[2] = str_replace('$' . $i, $subpatterns[$i], $rule[2]);
                }
                $to = new GeoLocation($rule[1], $rule[2]);
                return true;
            }
        }
        return false;
    }

    /**
     * Determines if a perso can travel from $from to $to
     *
     * If an alias have been used for $to local location, set correct location.
     *
     * @param GeoLocation the location where the perso is
     * @param GeoLocation the location where the perso wants to go
     * @return boolean true if the travel move is valid ; otherwise, false.
     *
     * @todo From B00001002, goto C1 doesn't work. Alias seems ignored.
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

            //Local move not allowed
            return false;
        }

        return true;
    }
}

?>
