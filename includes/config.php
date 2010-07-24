<?php

/**
 * Autogenerable configuration file
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 * 
 * @package     Zed
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// I. SQL configuration                                                     ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////
 
//SQL configuration
$Config['sql']['product'] = 'MySQL';    //Only MySQL is currently implemented
$Config['sql']['host'] = 'localhost';
$Config['sql']['username'] = 'zed';
$Config['sql']['password'] = 'zed';
$Config['sql']['database'] = 'zed';

//SQL tables
$prefix = '';
define('TABLE_API_KEYS', $prefix . 'api_keys');
define('TABLE_COMMENTS', $prefix . 'comments');
define('TABLE_LOG', $prefix . 'log');
define('TABLE_LOG_SMARTLINE', $prefix . 'log_smartline');
define('TABLE_MESSAGES', $prefix . 'messages');
define('TABLE_MOTD', $prefix . 'motd');
define('TABLE_PAGES', $prefix . 'pages');
define('TABLE_PAGES_EDITS', $prefix . 'pages_edits');
define('TABLE_PERSOS', $prefix . 'persos');
define('TABLE_PERSOS_FLAGS', $prefix . 'persos_flags');
define('TABLE_PERSOS_NOTES', $prefix . 'persos_notes');
define('TABLE_PORTS', $prefix . 'ports');
define('TABLE_PROFILES', $prefix . 'profiles');
define('TABLE_PROFILES_COMMENTS', $prefix . 'profiles_comments');
define('TABLE_PROFILES_PHOTOS', $prefix . 'profiles_photos');
define('TABLE_PROFILES_TAGS', $prefix . 'profiles_tags');
define('TABLE_REGISTRY', $prefix . 'registry');
define('TABLE_SESSIONS', $prefix . 'sessions');
define('TABLE_SHIPS', $prefix . 'ships');
define('TABLE_USERS', $prefix . 'users');
define('TABLE_USERS_INVITES', $prefix . 'users_invites');
define('TABLE_USERS_OPENID', $prefix . 'users_openid');

//Geo tables
define('TABLE_BODIES', $prefix . 'geo_bodies');
define('TABLE_LOCATIONS', $prefix . 'geo_locations');   //Well... it's a view
define('TABLE_PLACES', $prefix . 'geo_places');

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// II. Site configuration                                                   ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Default theme
$Config['DefaultTheme'] = "Zed";

//Dates
date_default_timezone_set("UTC");

//Secret key, used for some verification hashes in URLs or forms.
$Config['SecretKey'] = 'Lorem ipsum dolor';

//When reading files, buffer size
define('BUFFER_SIZE', 4096);

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// III. Script URLs                                                         ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Without mod_rewrite:
 * 
 *   Subdirectory:
 *     - $Config['SiteURL'] = 'http://zed.dereckson.be/hypership/index.php';
 *     - $Config['BaseURL'] = '/hypership/index.php';
 *
 *   Root directory:
 *     - $Config['SiteURL'] = 'http://zed.dereckson.be/index.php';
 *     - $Config['BaseURL'] = '/index.php';
 *
 * With mod_rewrite:
 * 
 *   Subdirectory:
 *     - $Config['SiteURL'] = 'http://zed.dereckson.be/hypership';
 *     - $Config['BaseURL'] = '/hypership';
 *
 *     In .htaccess or your vhost definition:
 *       RewriteEngine On
 *       RewriteBase /hypership/
 *       RewriteCond %{REQUEST_FILENAME} !-f
 *       RewriteCond %{REQUEST_FILENAME} !-d
 *       RewriteRule . /hypership/index.php [L]
 *
 *   Root directory:
 *     - $Config['SiteURL'] = 'http://zed.dereckson.be';
 *     - $Config['BaseURL'] = '';
 *
 *     In .htaccess or your vhost definition:
 *       RewriteEngine On
 *       RewriteBase /
 *       RewriteCond %{REQUEST_FILENAME} !-f
 *       RewriteCond %{REQUEST_FILENAME} !-d
 *       RewriteRule . /index.php [L]
 *
 *
 * If you don't want to specify the server domain, you can use get_server_url:
 *      $Config['SiteURL'] = get_server_url() . '/hypership';
 *      $Config['SiteURL'] = get_server_url();
 * 
 * !!! No trailing slash !!!
 *   
 */

$Config['SiteURL'] = get_server_url();
$Config['BaseURL'] = '';

//AJAX callbacks URL
$Config['DoURL'] = $Config['SiteURL'] . "/do.php";

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// IV. Static content                                                       ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Where the static content is located?
//Static content = 4 directories: js, css, img and content
//On default installation, those directories are at site root.
//To improve site performance, you can use a CDN for that.
//To use 
//
//Recommanded setting: $Config['StaticContentURL'] = $Config['SiteURL'];
//Or if Zed is the site root: $Config['StaticContentURL'] = '';
//With CoralCDN: $Config['StaticContentURL'] =  . '.nyud.net';
//
$Config['StaticContentURL'] = '';
//$Config['StaticContentURL'] = get_server_url() . '.nyud.net';

//Scenes
define('SCENE_DIR', 'content/scenes');
define('SCENE_URL', $Config['StaticContentURL'] . '/' . SCENE_DIR);

//Stories
define('STORIES_DIR', "content/stories");

//Profile's photos
define('PHOTOS_DIR', 'content/users/_photos');
define('PHOTOS_URL', $Config['StaticContentURL'] . '/' . PHOTOS_DIR);

//ImageMagick paths
//Be careful on Windows platform convert could match the NTFS convert command.
$Config['ImageMagick']['convert'] = 'convert';
$Config['ImageMagick']['mogrify'] = 'mogrify';
$Config['ImageMagick']['composite'] = 'composite';
$Config['ImageMagick']['identify'] = 'identify';


////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// V. Sessions                                                              ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//If you want to use a common table of sessions / user handling
//with several websites, specify a different resource id for each site.
$Config['ResourceID'] = 21;

//PHP variables
ini_set('session.serialize_handler', 'wddx');
ini_set('session.save_path', 'cache/sessions');
ini_set('session.gc_maxlifetime', 345600);  //4 days, for week-end story pause and continue url


////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// VI. Caching                                                              ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

$Config['cache']['engine'] = 'memcached';
$Config['cache']['server'] = 'localhost';
$Config['cache']['port']   = 11211;

?>