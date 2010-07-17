<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * SmartLine
 *
 * TODO: SettingsSmartLineCommand - understand why dojo floating pane isn't
 *       rendered if we est $controller instead to redirect
 */

///
/// Register commands
///

$smartLine->register_object('goto', 'GotoSmartLineCommand');
$smartLine->register_object('guid', 'GUIDSmartLineCommand');
$smartLine->register_object('invite', 'InviteSmartLineCommand');
$smartLine->register_object('invites', 'InviteSmartLineCommand');
$smartLine->register_object('list', 'ListSmartLineCommand');
$smartLine->register_object('requests', 'RequestsSmartLineCommand');
$smartLine->register_object('settings', 'SettingsSmartLineCommand');
$smartLine->register_object('unixtime', 'UnixTimeSmartLineCommand');
$smartLine->register_object('version', 'VersionSmartLineCommand');
$smartLine->register_object('whereami', 'WhereAmISmartLineCommand');


///
/// Help (todo: move $lang array in lang folder)
///

$lang['Help']['goto'] = "Go to a location";
$lang['Help']['guid'] = "Generate a GUID";
$lang['Help']['invite'] = "Generate an invite. To see the generated invites, invite list.";
$lang['Help']['list'] = "Lists specified objects (bodies, locations or places)";
$lang['Help']['requests'] = "Checks if there are waiting requests";
$lang['Help']['unixtime'] = "Prints current unixtime (seconds elapsed since 1970-01-01 00:00, UTC) or the specified unixtime date.";
$lang['Help']['version'] = "Gets Zed's software version info (Mercurial repository version, node id and if you're on the dev or prod site)";
$lang['Help']['whereami'] = "Where am I?";

///
/// goto
///

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
/// GUID
///

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
/// invite
///

class InviteSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
		require_once('includes/objects/invite.php');
		global $CurrentUser, $CurrentPerso;
		
		$command = ($argc > 1) ? strtolower($argv[1]) : '';
		switch ($command) {
			case 'list':
				$codes = Invite::get_invites_from($CurrentPerso->id);
				if (!count($codes)) {
					$this->SmartLine->puts("No invite code.");
				} else {
					foreach ($codes as $code) {
						$this->SmartLine->puts($code);
					}
				}
				break;
			
			case 'add':
			case '':
				$code = Invite::create($CurrentUser->id, $CurrentPerso->id);
				$url = get_server_url() . get_url('invite', $code);
				$this->SmartLine->puts("New invite code created: $code<br />Invite URL: $url");
				break;
			
			case 'del':
				$code = $argv[2];
				if (!preg_match("/^([A-Z]){3}([0-9]){3}$/i", $code)) {
					$this->SmartLine->puts("Invalid code format. Use invite list to get all your invite codes.", STDERR);
				} else {
					$invite = new Invite($code);
					if ($CurrentPerso->id == $invite->from_perso_id) {
						$invite->delete();
						$this->SmartLine->puts("Deleted");
					} else {
						$this->SmartLine->puts("Invalid code. Use invite list to get all your invite codes.", STDERR);
					}
				}
				break;
			
			default:
				$this->SmartLine->puts("Usage: invite [add|list|del <code>]", STDERR);
				break;
		}
	
    }
}

///
/// list
///

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
/// Requests
///

class RequestsSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
		global $CurrentPerso;
		$force = ($argc > 1) && ($argv[1] == "-f" || $argv[1] == "--force");
		if ($force || (array_key_exists('site.requests', $CurrentPerso->flags) && $CurrentPerso->flags['site.requests'])) {
			global $controller;
			$controller = 'controllers/persorequest.php';
		} else {
			$this->SmartLine->puts("No request waiting.");
		}	
    }
}

///
/// Settings
///

class SettingsSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
		if (headers_sent()) {
			global $controller;
			$controller = 'controllers/settings.php';
		} else {
			header('location: ' . get_url('settings'));
		}
    }
}

///
/// unixtime
///

class UnixTimeSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
        date_default_timezone_set('UTC');
        if ($argc == 1) {
            $this->SmartLine->puts(time());
        } elseif ($argc == 2 && is_numeric($argv[1])) {
            $this->SmartLine->puts(strftime("%Y-%m-%d %X", $argv[1]));
            $this->SmartLine->puts(get_hypership_time($argv[1]));
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

///
/// version
///

class VersionSmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
		//Gets .hg revision
		if (file_exists('.hg/tags.cache')) {
			$content = file_get_contents('.hg/tags.cache');
			$info = explode(' ', $content, 2);
			$info[] = "development environment";
		} else if (file_exists('.hg_archival.txt')) {
			$content = file('.hg_archival.txt');
			foreach ($content as $line) {
				$items = explode(' ', $line, 2);
				if ($items[0] == 'node:') $info[1] = trim($items[1]);
				if ($items[0] == 'latesttagdistance:') $info[0] = trim($items[1]);
				$info[2] = 'production environment';
			}
		} else {
			$this->SmartLine->puts("No version information available.", STDERR);
			return false;
		}
		
		$this->SmartLine->puts("r$info[0] ($info[2])");
		$this->SmartLine->puts("Hash: $info[1]");
	}
}

///
/// whereami
///

class WhereAmISmartLineCommand extends SmartLineCommand {
    public function run ($argv, $argc) {
	    global $CurrentPerso;
		
	    require_once("includes/geo/location.php");
	    $place = new GeoLocation($CurrentPerso->location_global);
	    $this->SmartLine->puts($CurrentPerso->location_global . ' - ' . $place);
    }
}

?>