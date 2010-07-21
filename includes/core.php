<?php

/**
 * Core: helper methods and main libraries loader
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 * 
 * @package     Zed
 * @subpackage  Pluton
 * @author      S�bastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 S�bastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Configures PHP and loads site-wide used libraries                        ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//No register globals
ini_set('register_globals', 'off');
error_reporting(E_ALL & ~E_NOTICE);

//Load libraries
include_once("config.php");               //Site config
include_once("error.php");               //Error management
include_once("mysql.php");              //MySQL layer
include_once("sessions.php");          //Sessions handler
include_once("autoload.php");         //__autoload()

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Information helper methods                                               ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/**
 * Gets the nickname from the specified perso
 * 
 * @param integer $perso_id The specified perso's ID
 * @return string The perso's nickname
 */
function get_name ($perso_id) {
	global $db;
	$perso_id = $db->sql_escape($perso_id);
    $sql = 'SELECT perso_nickname FROM '. TABLE_PERSOS . " WHERE perso_id = '$perso_id'";
	if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Can't query persos table.", '', __LINE__, __FILE__, $sql);
	$row = $db->sql_fetchrow($result);
	return $row['perso_nickname'];
}

//Gets user_id from specified username
function get_userid ($username) {
	global $db;
	$username = $db->sql_escape($username);
	$sql = 'SELECT user_id FROM '. TABLE_USERS . " WHERE username LIKE '$username'";
	if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Can't query users table.", '', __LINE__, __FILE__, $sql);
	$row = $db->sql_fetchrow($result);
	return $row['user_id'];
}

function registry_get ($key) {
    global $db;
    $key = $db->sql_escape($key);
    $sql = "SELECT registry_value FROM " . TABLE_REGISTRY . " WHERE registry_key = '$key'";
		if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Can't read registry.", '', __LINE__, __FILE__, $sql);

    $row = $db->sql_fetchrow($result);
	return $row['registry_value'];
}

function registry_set ($key, $value) {
    global $db;
    $key = $db->sql_escape($key);
    $value = $db->sql_escape($value);
    $sql = "REPLACE INTO " . TABLE_REGISTRY . " (registry_key, registry_value) VALUES ('$key', '$value')";
    if (!$db->sql_query($sql))
        message_die(SQL_ERROR, "Can't update registry", '', __LINE__, __FILE__, $sql);
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Misc helper methods                                                      ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/**
 * Generates a random string
 * @author Pierre Habart <p.habart@ifrance.com>
 *
 * @param string $format The format e.g. AAA111
 * @return string a random string
 */
function generate_random_string ($format) {
    mt_srand((double)microtime()*1000000);
    $str_to_return="";

    $t_alphabet=explode(",","A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z");
    $t_number=explode(",","1,2,3,4,5,6,7,8,9,0");

    for ($i=0;$i<strlen($format);$i++)
    {
        if (preg_match("/^[a-zA-Z]/",$format[$i]))
        {
            $add=$t_alphabet[mt_rand() % sizeof($t_alphabet)];
            if (preg_match("/^[a-z]/",$format[$i]))
                $add=strtolower($add);
        }
        elseif(preg_match("/^[0-9]/",$format[$i]))
            $add=$t_number[mt_rand() % sizeof($t_number)];
        else $add="?";

        $str_to_return.=$add;
    }
    return $str_to_return;
}

function generer_hexa($longueur) {
        mt_srand((double)microtime()*1000000);
        $str_to_return="";
        $t_number=explode(",","1,2,3,4,5,6,7,8,9,0,A,B,C,D,E,F");
        for ($i = 0 ; $i < $longueur ; $i++) {
                $str_to_return .= $t_number[mt_rand() % sizeof($t_number)];
        }
    return $str_to_return;
}

//Plural management

function s ($amount) {
	if ($amount > 1) return "s";
}

function x ($amount) {
	if ($amount > 1) return "x";
}

//Debug

/**
 * Prints human-readable information about a variable  (like the print_r command),
 * enclosed in <pre></pre> tags, to have a preformatted HTML output.
 *
 * @param mixed The expression to be printed
 */
function dprint_r ($expression) {
	echo '<pre>';
	print_r($expression);
	echo '</pre>';
}

//GUID

function new_guid() {
	$characters = explode(",","a,b,c,d,e,f,0,1,2,3,4,5,6,7,8,9");
	$guid = "";
	for ($i = 0 ; $i < 36 ; $i++) {
		if ($i == 8 || $i == 13 || $i == 18 || $i == 23) {
			$guid .= "-";
		} else {
			$guid .= $characters[mt_rand() % sizeof($characters)];
		}
	}
	return $guid;
}


function is_guid ($expression) {
    //We avoid regexp to speed up the check
    //A guid is a 36 characters string
    if (strlen($expression) != 36) return false;
    
    $expression = strtolower($expression);
	for ($i = 0 ; $i < 36 ; $i++) {
		if ($i == 8 || $i == 13 || $i == 18 || $i == 23) {
			//with dashes
			if ($expression[$i] != "-") return false;
		} else {
		    //and numbers
			if (!is_numeric($expression[$i]) && $expression[$i] != 'a' && $expression[$i] != 'b' && $expression[$i] != 'c' && $expression[$i] != 'd' && $expression[$i] != 'e' && $expression[$i] != 'f' ) return false;
		}
	}
    return true;
}

/*
 * Gets file extension
 * @param string $file the file to get the extension
 */
function get_extension ($file) {
    $dotPosition = strrpos($file, ".");
    return substr($file, $dotPosition + 1);
}

/*
 * Determines if a string starts with specified substring
 * @param string $haystack the string to check
 * @param string $needle the substring to determines if it's the start
 * @param boolean $case_sensitive determines if the search must be case sensitive
 * @return boolean true if $haystack starts with $needle ; otherwise, false.
 */
function string_starts_with ($haystack, $needle, $case_sensitive = true) {
    if (!$case_sensitive) {
        $haystack = strtoupper($haystack);
        $needle = strtoupper($needle);
    }
    if ($haystack == $needle) return true;
    return strpos($haystack, $needle) === 0;
}

/*
 * Inserts a message into the supralog
 * @param string $category the entry category
 * @param string $message the message to log
 * @param string $source the entry source.
 */
function supralog ($category, $message, $source = null) {
    global $db, $CurrentUser, $CurrentPerso;
    $category = $db->sql_query_express($category);
    $message = $db->sql_query_express($message);
    $source = $db->sql_query_express($source ? $source : $_SERVER['SERVER_ADDR']);
    $ip = $_SERVER['REMOTE_ADDR'];
	$sql = "INSERT INTO " . TABLE_LOG .
           " (entry_ip, user_id, perso_id, entry_category, entry_message, entry_source) VALUES 
             ('$ip', $CurrentUser->id, $CurrentPerso->id, '$category', '$message', '$source')";
	if ( !($result = $db->sql_query($sql)) )
		message_die(SQL_ERROR, "Can't log this entry.", '', __LINE__, __FILE__, $sql);
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Localization (l10n)                                                      ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Defines LANG constant to lang to print
 */
function initialize_lang () {
    //If $_SESSION['lang'] doesn't exist yet, find a common language
    if (!array_key_exists('lang', $_SESSION)) {
        $lang = find_lang();
        $_SESSION['lang'] = $lang ? $lang : '-';
    }
    
    if ($_SESSION['lang'] != '-')
        define('LANG', $_SESSION['lang']);
}

/*
 * Gets a common lang spoken by the site and the user
 * @return string the language
 */    
function find_lang () {
    if (file_exists('lang') && is_dir('lang')) {
        //Gets lang/ subdirectories: this is the list of available languages
        $handle = opendir('lang');
        while ($file = readdir($handle)) {
            if ($file != '.' && $file != '..' && is_dir("lang/$file")) {
                $langs[] = $file;
            }
        }

        //The array $langs contains now the language available.
        //Gets the langs the user should want:
        if (!$userlangs = get_http_accept_languages())
            return;
        
        //Gets the intersection between the both languages arrays
        //If it matches, returns first result
        $intersect = array_intersect($userlangs, $langs);
        if (count($intersect)) {
            return $intersect[0];
        }
        
        //Now it's okay with Opera and Firefox but Internet Explorer will
        //by default return en-US and not en or fr-BE and not fr, so second pass
        foreach ($userlangs as $userlang) {
            $lang = explode('-', $userlang);
            if (count($lang) > 1)
                $userlangs2[] = $lang[0];
        }
        $intersect = array_intersect($userlangs2, $langs);
        if (count($intersect)) {
            return $intersect[0];
        }
    }
}

/*
 * Returns the languages accepted by the browser, by order of priority
 * @return Array a array of languages string 
 */

function get_http_accept_languages () {
    //What language to print is sent by browser in HTTP_ACCEPT_LANGUAGE var.
    //This will be something like en,fr;q=0.8,fr-fr;q=0.5,en-us;q=0.3
    
    if (!array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
        return null;
    }
    
    $http_accept_language = explode(',', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
    foreach ($http_accept_language as $language) {
        $userlang = explode(';q=', $language);
        if (count($userlang) == 1) {
            $userlangs[] = array(1, $language);
        } else {
            $userlangs[] = array($userlang[1], $userlang[0]);
        }
    }
    rsort($userlangs);
    foreach ($userlangs as $userlang) {
        $result[] = $userlang[1];
    }
    return $result;
}

/*
 * Loads specified language Smarty configuration file
 *
 * @param string $file the file to load
 * @param mixed $sections array of section names, single section or null
 */
function lang_load ($file, $sections = null) {
    global $smarty;
    
    //Loads English file as fallback if some parameters are missing
    if (file_exists("lang/en/$file"))
        $smarty->config_load("lang/en/$file", $sections);
    
    //Loads wanted file (if it exists and a language have been defined)
    if (defined('LANG') && LANG != 'en' && file_exists('lang/' . LANG . '/' . $file))
        $smarty->config_load('lang/' . LANG . '/' . $file, $sections);
}

/*
 * Gets a specified language expression defined in configuration file
 *
 * @param string $key the configuration key matching the value to get
 * @return string The value in the configuration file
 */
function lang_get ($key) {
    global $smarty;
    
    $smartyConfValue = $smarty->config_vars[$key];
    return $smartyConfValue ? $smartyConfValue : "#$key#";
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Zed date and time helper methods                                         ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Converts a YYYYMMDD or YYYY-MM-DD timestamp to unixtime
 */
function to_unixtime ($timestamp) {
	switch (strlen($timestamp)) {
        case 8:
        //YYYYMMDD
        return mktime(0, 0, 0, substr($timestamp, 4, 2), substr($timestamp, 6, 2), substr($timestamp, 0, 4));
    
        case 10:
        //YYYY-MM-DD
        return mktime(0, 0, 0, substr($timestamp, 5, 2), substr($timestamp, 8, 2), substr($timestamp, 0, 4));
    
        default:
        throw new Exception("timestamp is not a valid YYYYMMDD or YYYY-MM-DD timestamp: $timestamp");
    }
}

/*
 * Converts a unixtime to the YYYYMMDD or YYYY-MM-DD timestamp format
 *
 * @param int $unixtime the time to convert
 * @param int $format 8 or 10. If 8 (default), will output YYYYMMDD. If 10, YYYY-MM-DD.
 */
function to_timestamp ($unixtime = null, $format = 8) {   
	//If no parameter is specified (or null, or false), current time is used
    //==== allows to_timestamp(0) to return correct 1970-1-1 value.
    if ($unixtime === null || $unixtime === false) $unixtime = time();
    
	switch ($format) {
        case 8:
        //YYYYMMDD
        return date('Ymd', $unixtime);
    
        case 10:
        //YYYY-MM-DD
        return date('Y-m-d', $unixtime);
    
        default:
        throw new Exception("format must be 8 (YYYYMMDD) or 10 (YYYY-MM-DD) and not $format.");
    }
}

/*
 * Converts a unixtime to the Hypership time format.
 */
function get_hypership_time ($unixtime = null) {
    //If unixtime is not specified, it's now
    if ($unixtime === null) $unixtime = time();
    
    //Hypership time is a count of days since launch @ 2010-07-03 00:00:00
    //Followed by a fraction of the current day /1000, like the internet time
    //but in UTC timezone and not Switzerland CET/CEST.
    //We don't need to use floor(), as we output the result at int, truncating
    //automatically decimal values instead of round it (like in C).
    $seconds = $unixtime - 1278115200;
    $days = $seconds / 86400;
    $fraction = (abs($seconds) % 86400) / 86.4;
    return sprintf("%d.%03d", $days, $fraction);
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// URL helpers functions                                                    ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/**
 * Gets URL
 * @return string URL
 */
function get_url () {
    global $Config;
    if (func_num_args() > 0) {
        $pieces = func_get_args();
        return $Config['BaseURL'] . '/' . implode('/', $pieces);
    } elseif ($Config['BaseURL'] == "" || $Config['BaseURL'] == "/index.php") {
        return "/";
    } else {
        return $Config['BaseURL'];
    }
}

/*
 * Gets page URL
 * @return string URL
 */
function get_page_url () {
    $url = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
    if (substr($url, -10) == "/index.php") {
        return substr($url, 0, -9);
    }
    return $url;
}

/*
 * Gets server URL
 * @todo find a way to detect https:// on non standard port
 * @return string the server URL
 */
function get_server_url () {
	switch ($port = $_SERVER['SERVER_PORT']) {
		case '80':
            return "http://$_SERVER[SERVER_NAME]";
        
        case '443':
            return "https://$_SERVER[SERVER_NAME]";
        
        default:
            return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]";
	}
}

/*
 * Gets $_SERVER['PATH_INFO'] or computes the equivalent if not defined.
 * @return string the relevant URL part
 */
function get_current_url () {
    global $Config;
            
    //Gets relevant URL part from relevant $_SERVER variables
    if (array_key_exists('PATH_INFO', $_SERVER)) {
        //Without mod_rewrite, and url like /index.php/controller
        //we use PATH_INFO. It's the easiest case.
        return $_SERVER["PATH_INFO"];
    }
    
    //In other cases, we'll need to get the relevant part of the URL
    $current_url = get_server_url() . $_SERVER['REQUEST_URI'];
    
    //Relevant URL part starts after the site URL
    $len = strlen($Config['SiteURL']);
    
    //We need to assert it's the correct site
    if (substr($current_url, 0, $len) != $Config['SiteURL']) {
        dieprint_r(GENERAL_ERROR, "Edit includes/config.php and specify the correct site URL<br /><strong>Current value:</strong> $Config[SiteURL]<br /><strong>Expected value:</strong> a string starting by " . get_server_url(), "Setup");
    }
    
    if (array_key_exists('REDIRECT_URL', $_SERVER)) {
        //With mod_rewrite, we can use REDIRECT_URL
        //We takes the end of the URL, ie *FROM* $len position
        return substr(get_server_url() . $_SERVER["REDIRECT_URL"], $len);
    }
    
    //Last possibility: use REQUEST_URI, but remove QUERY_STRING
    //If you need to edit here, use $_SERVER['REQUEST_URI']
    //but you need to discard $_SERVER['QUERY_STRING']
       
    //We takes the end of the URL, ie *FROM* $len position
    $url = substr(get_server_url() . $_SERVER["REQUEST_URI"], $len);
    
    //But if there are a query string (?action=... we need to discard it)	
    if ($_SERVER['QUERY_STRING']) {
        return substr($url, 0, strlen($url) - strlen($_SERVER['QUERY_STRING']) - 1);
    }
    
    return $url;
}

/*
 * Gets an array of url fragments to be processed by controller
 */
function get_current_url_fragments () {
    $url_source = get_current_url();
    if ($url_source == '/index.php') return array();
    return explode('/', substr($url_source, 1));
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// URL xmlHttpRequest helpers functions                                     ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Gets an hash value to check the integrity of URLs in /do.php calls
 * @param Array $args the args to compute the hash
 * @return the hash paramater for your xmlHttpRequest url
 */
function get_xhr_hash ($args) {
    global $Config;
    
    array_shift($args);
    return md5($_SESSION['ID'] . $Config['SecretKey'] . implode('', $args));
}

/*
 * Gets the URL to call do.php, the xmlHttpRequest controller
 * @return string the xmlHttpRequest url, with an integrity hash
 */
function get_xhr_hashed_url () {   
    global $Config;
    
    $args = func_get_args();
    $args[] = get_xhr_hash($args);
    return $Config['DoURL'] . '/' . implode('/', $args);
}

/*
 * Gets the URL to call do.php, the xmlHttpRequest controller
 * @return string the xmlHttpRequest url
 */
function get_xhr_url () {
    global $Config;
    
    $args = func_get_args();
    return $Config['DoURL'] . '/' .implode('/', $args);
}

?>
