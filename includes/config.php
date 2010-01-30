<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Autogenerable configuration file
 */
 
//SQL configuration
$Config['sql']['product'] = 'MySQL';
$Config['sql']['host'] = 'localhost';
$Config['sql']['username'] = 'zed';
$Config['sql']['password'] = 'zed';
$Config['sql']['database'] = 'zed';

//GRANT ALL PRIVILEGES on zed.* TO 'zed'@'localhost' IDENTIFIED by 'zed';

//SQL tables
$prefix = '';
define('TABLE_API_KEYS', $prefix . 'api_keys');
define('TABLE_COMMENTS', $prefix . 'comments');
define('TABLE_LOG_SMARTLINE', $prefix . 'log_smartline');
define('TABLE_MESSAGES', $prefix . 'messages');
define('TABLE_MOTD', $prefix . 'motd');
define('TABLE_PERSOS', $prefix . 'persos');
define('TABLE_PERSOS_FLAGS', $prefix . 'persos_flags');
define('TABLE_PROFILES', $prefix . 'profiles');
define('TABLE_PROFILES_COMMENTS', $prefix . 'profiles_comments');
define('TABLE_PROFILES_PHOTOS', $prefix . 'profiles_photos');
define('TABLE_SESSIONS', $prefix . 'sessions');
define('TABLE_USERS', $prefix . 'users');
define('TABLE_USERS_OPENID', $prefix . 'users_openid');

define('TABLE_BODIES', $prefix . 'geo_bodies');
define('TABLE_LOCATIONS', $prefix . 'geo_locations');       //View
define('TABLE_PLACES', $prefix . 'geo_places');

//Script URL
$Config['BaseURL'] = '/index.php';

//Default theme
$Config['DefaultTheme'] = "Zed";

//Dates
date_default_timezone_set("UTC");

//Sessions
//If you want to use a common table of sessions / user handling
//with several websites, specify a different resource id for each site.
$Config['ResourceID'] = 21;

?>