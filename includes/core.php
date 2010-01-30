<?php
//No register globals
ini_set('register_globals', 'off');
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL & ~E_NOTICE);

//Load libraries
include_once("config.php");               //Site config
include_once("error.php");               //Error management
include_once("mysql.php");              //MySQL layer
include_once("sessions.php");          //Sessions handler

//Helpers

//Gets username from specified user_id
function get_name ($id) {
	global $db;
	$sql = 'SELECT perso_nickname FROM '. TABLE_PERSOS . " WHERE perso_id = '$id'";
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

// ------------------------------------------------------------------------- //
// Chaîne aléatoire                                                          //
// ------------------------------------------------------------------------- //
// Auteur: Pierre Habart                                                     //
// Email:  p.habart@ifrance.com                                              //
// Web:                                                                      //
// ------------------------------------------------------------------------- //

function genereString($format)
{
    mt_srand((double)microtime()*1000000);
    $str_to_return="";

    $t_alphabet=explode(",","A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z");
    $t_number=explode(",","1,2,3,4,5,6,7,8,9,0");

    for ($i=0;$i<strlen($format);$i++)
    {
        if (ereg("^[a-zA-Z]",$format[$i]))
        {
            $add=$t_alphabet[mt_rand() % sizeof($t_alphabet)];
            if (ereg("^[a-z]",$format[$i]))
                $add=strtolower($add);
        }
        elseif(ereg("^[0-9]",$format[$i]))
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

function dprint_r ($mixed) {
	echo "<pre>", print_r($mixed, true), "</pre>";
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

//Gets file extension
function get_extension ($file) {
    $dotPosition = strrpos($file, ".");
    return substr($file, $dotPosition + 1);
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
    
    //Loads wanted file
    if (LANG != 'en' && file_exists('lang/' . LANG . '/' . $file))
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
    
    //Hypership time is a count of days since launch @ 2010-01-25 00:00:00
    //Followed by a fraction of the current day /1000, like the internet time
    //but in UTC timezone and not Switzerland CET/CEST.
    //We don't need to use floor(), as we output the result at int, truncating
    //automatically decimal values instead of round it (like in C).
    $seconds = $unixtime - 1264377600;
    $days = $seconds / 86400;
    $fraction = ($seconds % 86400) / 86.4;
    return sprintf("%d.%03d", $days, $fraction);
}

/*
 * Gets URL
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

?>