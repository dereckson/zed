<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * SmartLine
 * 
 */

///
/// Register commands
///

$smartLine->register_object('goto', 'GotoSmartLineCommand');
$smartLine->register_object('guid', 'GUIDSmartLineCommand');
$smartLine->register_object('list', 'ListSmartLineCommand');
$smartLine->register_object('unixtime', 'UnixTimeSmartLineCommand');
$smartLine->register_object('whereami', 'WhereAmISmartLineCommand');

///
/// whereami
///

$lang['Help']['whereami'] = "Where am I?";

class WhereAmISmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
	global $CurrentPerso;
	require_once("includes/geo/location.php");
	$place = new GeoLocation($CurrentPerso->location_global);
	$this->SmartLine->puts($CurrentPerso->location_global . ' - ' . $place);
    }
}

///
/// GUID
///

$lang['Help']['GUID'] = "Generate a GUID";

class GUIDSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
	if ($argc > 1 && is_numeric($argv[1])) {
	    for ($i = 0 ; $i < $argv[1] ; $i++) {
		$this->SmartLine->puts(new_guid());
	    }
	    return;
	}
	
	$this->SmartLine->puts(new_guid());
	
    }
}

///
/// goto
///

$lang['Help']['goto'] = "Go to a location";

class GotoSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
	global $CurrentPerso;
	
	if ($argc == 1) {
	    $this->SmartLine->puts("Where do you want to go?", STDERR);
	    return;
	}
	
	require_once("includes/geo/location.php");	
	try {
	    $place = new GeoLocation($argv[1]);
	} catch (Exception $ex) {
	    $this->SmartLine->puts($ex->getMessage(), STDERR);
	    return;
	}
	
	if ($place->equals($CurrentPerso->location_global)) {
	    $this->SmartLine->puts("You're already there.");
	    return;
	}
	
	if (!$place->exists()) {
	    $this->SmartLine->puts("This place doesn't seem to exist.");
	    return;
	}
	
	$this->SmartLine->puts("TODO: code travel assistant");
    }    
}

///
/// list
///

$lang['Help']['list'] = "Lists specified objects (bodies, locations or places)";

class ListSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
        if ($argc == 1) {
            $this->SmartLine->puts("Available lists: bodies, locations, places");
            return;
        }
        
        switch ($objects = $argv[1]) {
            case 'bodies':
                $list = $this->get_list(TABLE_BODIES, "CONCAT('B', body_code)", "body_name");
                $this->SmartLine->puts($list);
                break;
	    
	    case 'locations':
                $list = $this->get_list(TABLE_LOCATIONS, "location_code", "location_name");
                $this->SmartLine->puts($list);
                break;		
            
            case 'places':
                if ($argv[2] == "-a" || $argv[2] == "--all") {
                    //Global bodies places list
                    $list = $this->get_list(TABLE_PLACES, "CONCAT('B', body_code, place_code)", "place_name");
                } else {
                    //Local places (or equivalent) list
                    global $CurrentPerso;
                    switch ($CurrentPerso->location_global[0]) {
                        case 'B':
                            $body_code = substr($CurrentPerso->location_global, 1, 5);
                            $list = $this->get_list(TABLE_PLACES, "CONCAT('B', body_code, place_code)", "place_name", "body_code = $body_code");
                            break;
                                                                        
                        case 'S':
                            $this->SmartLine->puts("I don't have a map of the spaceship.", STDERR);
                            return;
                    
                        default:
                            $this->SmartLine->puts("Unknown location type. Can only handle B or S.", STDERR);
                            return;
                    }
                }
                $this->SmartLine->puts($list);
                break;
            
            default:
                $this->SmartLine->puts("Unknown objects to list: $objects", STDERR);
        }
    
    }
    
    public function get_list ($table, $key, $value, $where = null) {
        global $db;
        $sql = "SELECT $key as `key`, $value as value FROM $table ";
        if ($where) $sql .= "WHERE $where ";
        $sql .= "ORDER BY `key` ASC";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to fetch list", '', __LINE__, __FILE__, $sql);
        }
        while ($row = $db->sql_fetchrow($result)) {
            $rows .= "<tr><td>$row[key]</td><td>$row[value]</td></tr>";
        }
        $this->SmartLine->truncate(STDERR);
        return "<table cellspacing=\"8\"><thead style=\"color: white\" scope=\"row\"><tr><th>Key</th><th>Value</th></thead><tbody>$rows</tbody></table>";
    }
}

///
/// unixtime
///

$lang['Help']['unixtime'] = "Prints current unixtime (seconds elapsed since 1970-01-01 00:00, UTC) or the specified unixtime date.";

class UnixTimeSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
	date_default_timezone_set('UTC');
	if ($argc == 1) {
	    $this->SmartLine->puts(time());
	} elseif ($argc == 2 && is_numeric($argv[1])) {
	    $this->SmartLine->puts(strftime("%Y-%m-%d %X", $argv[1]));
	} else {
	    array_shift($argv);
	    $date = implode(' ', $argv);
	    if ($time = strtotime($date) !== false) {
		    $this->SmartLine->puts("Unixtime from $date: <span class=\"highlight\">$time</span>");
		} else {
		    $this->SmartLine->puts("$date isn't a unixtime nor a valid date strtotime is able to parse.", STDERR);
		}
	}
    }
}

?>