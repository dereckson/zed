<?php

/*
 * TravelPlace class
  *
 * 0.1    2010-07-19 22:10    DcK
 *
 * @package Zed
 * @subpackage Travel
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 */
class TravelPlace {
    /*
     * @var string the place code
     */    
    public $code;
    
    /*
     * @var boolean determines if any local location move is valid
     */        
    public $freeLocalMove = false;
    
    /*
     * @var Array array of strings, each item another place reachable
     */    
    public $globalTravelTo = array();
    
    /*
     * @var Array array of array, containing [location, alias, name] entries
     */
    public $localMoves = array();
    
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
        
        return $travelPlace;
    }
}

?>