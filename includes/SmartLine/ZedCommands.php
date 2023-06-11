<?php

/**
 * Zed SmartLine commands.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This is the SmartLine subcontroller.
 *
 * The SmartLine is a widget allowing to add some basic CLI capability.
 *
 * It executes any command given in GET or POST request (parameter C).
 *
 * This files also provides SmartLine history helper: a method log_C to log
 * a SmartLine command and some procedural code assigning a SmartLineHistory.
 *
 * This code is inspired from Viper, a corporate PHP intranet I wrote in 2004.
 * There, the SmartLine allowed to change color theme or to find quickly user,
 * account, order or server information in a CRM context.
 *
 * @package     Zed
 * @subpackage  SmartLine
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 *
 * @todo SettingsSmartLineCommand - understand why dojo floating pane isn't rendered if we est $controller instead to redirect
 */

///
/// Register commands
///

$smartLine->register_object('debug',    DebugSmartLineCommand::class);
$smartLine->register_object('goto',     'GotoSmartLineCommand');
$smartLine->register_object('guid',     'GUIDSmartLineCommand');
$smartLine->register_object('invite',   'InviteSmartLineCommand');
$smartLine->register_object('invites',  'InviteSmartLineCommand');
$smartLine->register_object('list',     'ListSmartLineCommand');
$smartLine->register_object('requests', 'RequestsSmartLineCommand');
$smartLine->register_object('settings', 'SettingsSmartLineCommand');
$smartLine->register_object('unixtime', 'UnixTimeSmartLineCommand');
$smartLine->register_object('version',  'VersionSmartLineCommand');
$smartLine->register_object('whereami', 'WhereAmISmartLineCommand');
$smartLine->register_object('whoami',   'WhoAmISmartLineCommand');

///
/// Help (todo: move $lang array in lang folder)
///

$lang['Help']['debug'] = "Enable or disable debugger";
$lang['Help']['goto'] = "Go to a location";
$lang['Help']['guid'] = "Generate a GUID";
$lang['Help']['invite'] = "Generate an invite. To see the generated invites, invite list.";
$lang['Help']['list'] = "Lists specified objects (bodies, locations, or places)";
$lang['Help']['requests'] = "Checks if there are waiting requests";
$lang['Help']['settings'] = 'Go to settings page';
$lang['Help']['unixtime'] = "Prints current unixtime (seconds elapsed since 1970-01-01 00:00, UTC) or the specified unixtime date.";
$lang['Help']['version'] = "Gets Zed's software version info (Mercurial repository version, node id and if you're on the dev or prod site)";
$lang['Help']['whereami'] = "Where am I?";
$lang['Help']['whoami'] = "Who am I?";

/**
 * Debugger command
 */
class DebugSmartLineCommand extends SmartLineCommand {

    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
    public function run ($argv, $argc) {
        if ($argc > 1) {
            $_SESSION['debug'] = self::parseBoolean($argv[1]);
        }

        $this->SmartLine->puts("Debugger " . $this->getStatus());
    }

    private function getStatus () : string {
        return $this->isEnabled() ? "enabled" : "disabled";
    }

    private function isEnabled () : bool {
        return $_SESSION['debug'] ?? false;
    }

}

/**
 * The goto command
 *
 * Moves to the current perso to the specified location.
 */
class GotoSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     *
     * @todo allow .goto global local (e.g. .goto B0001001 T2C3)
     * @todo determine if we allow rewrite rules to bypass can_travel rules
     */
    public function run ($argv, $argc) {
        global $CurrentPerso;

        if ($argc == 1) {
            $this->SmartLine->puts("Where do you want to go?", STDERR);
            return;
        }

        if ($argc > 2) {
            $ignored_string = implode(" ", array_slice($argv, 2));
            $this->SmartLine->puts("Warning: ignoring $ignored_string", STDERR);
        }

        require_once("includes/geo/location.php");
        require_once("includes/travel/travel.php");

        $here = new GeoLocation($CurrentPerso->location_global, $CurrentPerso->location_local);
        $travel = Travel::load(); //maps content/travel.xml

        //Parses the expression, by order of priority, as :
        //  - a rewrite rule
        //  - a new global location
        //  - a new local location (inside the current global location)
        if (!$travel->try_parse_rewrite_rule($argv[1], $here, $place)) {
            try {
                $place = new GeoLocation($argv[1]);

                if ($place->equals($CurrentPerso->location_global)) {
                    $this->SmartLine->puts("You're already there.");
                    return;
                }
            } catch (Exception $ex) {
                //Global location failed, trying local location
                try {
                    $place = new GeoLocation($CurrentPerso->location_global, $argv[1]);
                } catch (Exception $ex) {
                    $this->SmartLine->puts($ex->getMessage(), STDERR);
                    return;
                }

                if ($place->equals($here)) {
                    $this->SmartLine->puts("You're already there.");
                    return;
                }
            }
        }

        //Could we really go there?
        if (!$travel->can_travel($here, $place)) {
            $this->SmartLine->puts("You can't reach that location.");
            return;
        }

        //Moves
        $CurrentPerso->move_to($place->global, $place->local);
        $this->SmartLine->puts("You travel to that location.");
        return;
    }
}

/**
 * The GUID command
 *
 * Prints a new GUID.
 *
 * guid 8 will print 8 guid
 */
class GUIDSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
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

/**
 * The invite command
 *
 * Manages invites.
 *
 * invite [add]
 *     creates a new invite code
 *
 * invite del <invite code>
 *     deletes the specified invite
 *
 * invite list
 *     prints current invite codes
 */
class InviteSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
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

/**
 * The list command
 *
 * Prints a list of bodies, locations or places.
 *
 * This can easily be extended to output any list from any table.
 */
class ListSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
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

    /**
     * Gets a custom list from the specified table and fields.
     *
     * The list will be sorted by the specified key, using ascending order.
     *
     * @param $table the table to query from the database
     * @param $key the first field to fetch, as key
     * @param $value the second field to fetch, as value
     * @param $where the WHERE clause, without the WHERE keyword (optional)
     */
    public function get_list ($table, $key, $value, $where = null) {
        global $db;
        $sql = "SELECT $key as `key`, $value as value FROM $table ";
        if ($where) {
            $sql .= "WHERE $where ";
        }
        $sql .= "ORDER BY `key` ASC";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Unable to fetch list", '', __LINE__, __FILE__, $sql);
        }
        while ($row = $db->fetchRow($result)) {
            $rows .= "<tr><td>$row[key]</td><td>$row[value]</td></tr>";
        }
        $this->SmartLine->truncate(STDERR); //kludge
        return "<table cellspacing=\"8\"><thead style=\"color: white\" scope=\"row\"><tr><th>Key</th><th>Value</th></thead><tbody>$rows</tbody></table>";
    }
}


/**
 * The requests command
 *
 * Redirects user to the requests page.
 *
 * By default only redirect if a flag indicates there's a new request.
 *
 * To forcefully goes to the request page, requests --force
 */
class RequestsSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
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

/**
 * The settings command
 *
 * Redirects user to the settings page.
 */
class SettingsSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
    public function run ($argv, $argc) {
        if (headers_sent()) {
            global $controller;
            $controller = 'controllers/settings.php';
        } else {
            header('location: ' . get_url('settings'));
        }
    }
}

/**
 * The unixtime command
 *
 * Prints current unixtime (seconds elapsed since 1970-01-01 00:00, UTC)
 * or if an unixtime is specified as argument, the matching date.
 */
class UnixTimeSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
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

/**
 * The version command
 *
 * Prints current hg revision, if we're in prod or dev environment and
 * the current revision's hash.
 *
 * The version and env information is extracted from
 *      .hg/tags.cache (indicating we're in a Mercurial repo and so in a dev environment), or from
 *      version.txt file (indicating we've deployed code in a production environment)
 *
 * e.g. r130 (development environment)
 *      Hash: 057bf394741706fd2136541e3bb07c9e60b4963d
 */
class VersionSmartLineCommand extends SmartLineCommand {
    private static function getGitHash (string $gitFolder = '.git') : string {
        $head = trim(file_get_contents("$gitFolder/HEAD"));

        if (str_starts_with($head, "ref: ")) {
             // Follows reference
             $ref = substr($head, 5);
             return file_get_contents("$gitFolder/$ref");
        }

        return $head;
    }

    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
    public function run ($argv, $argc) {
        if (file_exists('.hg/tags.cache')) {
            //Gets .hg revision
            $content = file_get_contents('.hg/tags.cache');
            $info = explode(' ', $content, 2);
            $info[] = "development environment";

            $this->SmartLine->puts("r$info[0] ($info[2])");
            $this->SmartLine->puts("Hash: $info[1]");
        } elseif (file_exists('.git/HEAD')) {
            $hash = self::getGitHash();
            $this->SmartLine->puts("Hash: $hash");
        } elseif (file_exists('version.txt')) {
            $content = file('version.txt');
            foreach ($content as $line) {
                $this->SmartLine->puts($line);
            }
        } else {
            $this->SmartLine->puts("No version information available.", STDERR);
            return false;
        }

        return true;
    }
}

/**
 * The whereami (Where am I?) command
 *
 * Prints current position, e.g. B00001001 - Tour, Hypership
 */
class WhereAmISmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
    public function run ($argv, $argc) {
        global $CurrentPerso;

        require_once("includes/geo/location.php");
        $place = new GeoLocation($CurrentPerso->location_global);
        $this->SmartLine->puts($CurrentPerso->location_global . ' - ' . $place);
    }
}


/**
 * The whoami (Who am I?) command
 *
 * Prints current position, e.g. B00001001 - Tour, Hypership
 */
class WhoAmISmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
    public function run ($argv, $argc) {
        global $CurrentPerso;
        $reply = "<span id=\"whoami.nickname\">$CurrentPerso->nickname</span> (<span id=\"whoami.name\">$CurrentPerso->name</span>), <span id=\"whoami.race\">$CurrentPerso->race</span>";
        $this->SmartLine->puts($reply);
    }
}
