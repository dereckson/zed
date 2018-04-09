<?php

/**
 * TravelPlace class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-07-19 22:10    DcK
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

/**
 * TravelPlace class
 *
 * The TravelPlace class is a set of rules determining which moves are valid
 * in a specific place.
 *
 * @see GeoPlace
 *
 */
class TravelPlace {
    /**
     * The place code
     *
     * @var string
     */
    public $code;

    /**
     * Determines if any local location move is valid
     *
     * @var bool
     */
    public $freeLocalMove = false;

    /**
     * Array of strings, each item another place reachable
     *
     * This matches GlobalTravelTo XML tags.
     *
     * @var Array
     */
    public $globalTravelTo = array();

    /**
     * Array of array, containing [location, alias, name] entries
     *
     * This matches LocalMove XML tags.
     *
     * @var Array
     */
    public $localMoves = array();

    /**
     * Array of array, containing [expression, global_location, local_location] entries
     *
     * This matches RewriteRule XML tags.
     *
     * @var Array
     */
    public $rewriteRules = array();

        /**
     * Initializes a new TravelPlace instance, from the specified XML fragment
     *
     * @param string $xml the XML fragment to parse
     * @return TravelPlace the TravelPlace instance matching the specified XML fragment
     */
    static function from_xml ($xml) {
        $travelPlace = new TravelPlace();

        //Reads attributes: <TravelPlace code="B00001001" freeLocalMove="true">
        foreach ($xml->attributes() as $key => $value) {
            switch ($key) {
                case 'code':
                    $travelPlace->code = (string)$value;
                    break;

                case 'freeLocalMove':
                    $travelPlace->freeLocalMove = (boolean)$value;
                    break;
            }
        }

        //<GlobalTravelTo code="B00001002" />
        foreach ($xml->GlobalTravelTo as $globalTravelToXml) {
            foreach ($globalTravelToXml->attributes() as $key => $value) {
                if ($key == "code") {
                    $travelPlace->globalTravelTo[] = (string)$value;
                }
            }
        }

        //<LocalMove local_location="(0, 0, 0)" alias="C0" name="Core" />
        foreach ($xml->LocalMove as $localMoveXml) {
            $localMove = array(null, null, null);
            foreach ($localMoveXml->attributes() as $key => $value) {
                switch ($key) {
                    case 'local_location':
                        $localMove[0] = (string)$value;
                        break;

                    case 'alias':
                        $localMove[1] = (string)$value;
                        break;

                    case 'name':
                        $localMove[2] = (string)$value;
                        break;
                }
            }
            $travelPlace->localMoves[] = $localMove;
        }

        //<RewriteRule expression="/^T([1-9][0-9]*)$/" global_location="B00001001" local_location="T$1C1" />
        foreach ($xml->RewriteRule as $rewriteRuleXml) {
            $rewriteRule = array(null, null, null);
            foreach ($rewriteRuleXml->attributes() as $key => $value) {
                switch ($key) {
                    case 'expression':
                        $rewriteRule[0] = (string)$value;
                        break;

                    case 'global_location':
                        $rewriteRule[1] = (string)$value;
                        break;

                    case 'local_location':
                        $rewriteRule[2] = (string)$value;
                        break;
                }
            }
            $travelPlace->rewriteRules[] = $rewriteRule;
        }

        return $travelPlace;
    }
}
