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

$smartLine->register_object('goto', 'GotoLineCommand');
$smartLine->register_object('list', 'ListSmartLineCommand');
$smartLine->register_object('unixtime', 'UnixTimeSmartLineCommand');

///
/// list
///
$lang['Help']['list'] = "Lists specified objects (bodies)";

class ListSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
        if ($argc == 1) {
            $this->SmartLine->puts("Available lists: bodies");
            return;
        }
        
        switch ($objects = $argv[1]) {
            case 'bodies':
                $list = $this->get_list(TABLE_BODIES, "CONCAT('B', body_code)", "body_name");
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
        return "<table cellpadding=4><thead scope=\"row\"><tr><th>Key</th><th>Value</th></thead><tbody>$rows</tbody></table>";
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