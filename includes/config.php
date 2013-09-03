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
define('TABLE_CONTENT_FILES',  $prefix . 'content_files');
define('TABLE_CONTENT_LOCATIONS',  $prefix . 'content_locations');
define('TABLE_CONTENT_ZONES',  $prefix . 'content_zones');
define('TABLE_CONTENT_ZONES_LOCATIONS', $prefix . 'content_zones_locations');
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
define('TABLE_REQUESTS', $prefix . 'requests');
define('TABLE_REQUESTS_REPLIES', $prefix . 'requests_replies');
define('TABLE_SESSIONS', $prefix . 'sessions');
define('TABLE_SHIPS', $prefix . 'ships');
define('TABLE_USERS', $prefix . 'users');
define('TABLE_USERS_INVITES', $prefix . 'users_invites');
define('TABLE_USERS_AUTH', $prefix . 'users_auth');

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
 * Apache httpd, without mod_rewrite:
 *
 *   Subdirectory:
 *     - $Config['SiteURL'] = 'http://zed.dereckson.be/hypership/index.php';
 *     - $Config['BaseURL'] = '/hypership/index.php';
 *
 *   Root directory:
 *     - $Config['SiteURL'] = 'http://zed.dereckson.be/index.php';
 *     - $Config['BaseURL'] = '/index.php';
 *
 * Apache httpd, with mod_rewrite:
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
 * nginx:
 *
 *   Use same config.php settings than Apache httpd, with mod_rewrite.
 *
 *   In your server block:
 *       location / {
 *           #Serves static files if they exists, with one month cache
 *           if (-f $request_filename) {
 *               expires 30d;
 *               break;
 *           }
 *
 *           #Sends all non existing file or directory requests to index.php
 *           if (!-e request_filename) {
 *               rewrite ^(.+)$ /index.php last;
 *               #Or if you use a subdirectory:
 *               #rewrite ^(.+)$ /hypership/index.php last;
 *           }
 *       }
 *
 *       location ~ \.php$ {
 *           #Your instructions to pass query to your FastCGI process, like:
 *           fastcgi_pass   127.0.0.1:9000;
 *           fastcgi_param  SCRIPT_FILENAME  /var/www/zed$fastcgi_script_name;
 *           include        fastcgi_params;
 *       }
 *
 *
 * If you don't want to specify the server domain, you can use get_server_url:
 *      $Config['SiteURL'] = get_server_url() . '/hypership';
 *      $Config['SiteURL'] = get_server_url();
 *
 *
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
/// V. Caching                                                               ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Some data (Smarty, OpenID and sessions) are cached in the cache directory.
 *
 * Security tip: you can move this cache directory outside the webserver tree.
 */
define('CACHE_DIR', 'cache');

/*
 * Furthermore, you can also enable a cache engine, like memcached, to store
 * data from heavy database queries, or frequently accessed stuff.
 *
 * To use memcached:
 *    - $Config['cache']['engine'] = 'memcached';
 *    - $Config['cache']['server'] = 'localhost';
 *    - $Config['cache']['port']   = 11211;
 *
 * To disable cache:
 *    - $Config['cache']['engine'] = 'void';
 *    (or don't write nothing at all)
 */
$Config['cache']['engine'] = 'void';

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// VI. Sessions                                                             ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//If you want to use a common table of sessions / user handling
//with several websites, specify a different resource id for each site.
$Config['ResourceID'] = 21;

//PHP variables
ini_set('session.serialize_handler', 'wddx');
ini_set('session.save_path', CACHE_DIR . '/sessions');
ini_set('session.gc_maxlifetime', 345600);  //4 days, for week-end story pause and continue url

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// VII. Builder                                                             ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Zed can invoke a slighty modified version of HOTGLUE to build zones.
$Config['builder']['hotglue']['enable'] = true;
$Config['builder']['hotglue']['URL'] = '/apps/hotglue/index.php';

?>
