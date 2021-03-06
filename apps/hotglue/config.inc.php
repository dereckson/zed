<?php


/**
 *	These are the default configuration settings of this hotglue 
 *	installation. Do not edit this file directly but overwrite specific 
 *	variables by setting them in the user-config.inc.php file, which 
 *	will not be overwritten by future updates.
 */

error_reporting(E_ALL);						// see php documentation

// try to include user configuration
@include('user-config.inc.php');

/**
 * Gets the server URL
 * @todo find a way to detect https:// on non standard port
 *
 * @return string the server URL
 */
function get_server_url () {
    switch ($port = $_SERVER['SERVER_PORT']) {
        case '80':
            return "http://$_SERVER[SERVER_NAME]/";

        case '443':
            return "https://$_SERVER[SERVER_NAME]/";

        default:
            return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/";
    }
}


// otherwise fall back to these defaults
@define('ALWAYS_PROMPT_CREATE_PAGE', false);	// invoke the "create page" controller when trying to access a non-existing page even if the user is not logged in yet (otherwise they receive a 404)
@define('AUTH_METHOD', 'none');			// can be digest, basic or none
@define('AUTH_USER', 'admin');
@define('AUTH_PASSWORD', 'changeme');
@define('BASE_URL', get_server_url() . 'apps/hotglue/');
@define('ZED_URL', '');
@define('CACHE_TIME', 60*60);				// cache time in seconds (zero to disable)
@define('CONTENT_DIR', 'content');			// content directory, must be writable
@define('DEFAULT_PAGE', 'start');
@define('FAVICON', 'img/favicon.ico');		// can be empty or an absolute url
@define('HOTGLUE_VERSION', '0.99.1');		// expected api.version.patchlevel
@define('IE8_COMPAT', true);				// try to be compatible with internet explorer 8 in viewing mode
@define('JQUERY', 'js/jquery-1.4.4.min.js');// can be an absolute url
@define('LOCK_TIME', 5000);					// maximum time in ms to wait for an object lock
@define('LOG_FILE', 'content/log.txt');		// log file, must be writable
@define('LOG_LEVEL', 'warn');				// minimum log level (can be error, warn, info, debug)
@define('SHORT_URLS', false);				// use short urls
@define('SHOW_FRONTEND_ERRORS', true);
@define('SITE_NAME', 'hotglue 1.0');
@define('SNAPSHOT_MAX_AGE', 60*60*24*7);	// auto- revisions are automatically deleted after n seconds (zero to disable)
@define('SNAPSHOT_MIN_AGE', 60*60);			// auto- revisions are created every n seconds (zero to disable)
@define('USE_MIN_FILES', true);				// use minified files if possible (see also JQUERY define)
// default modules
@define('IMAGE_JPEG_QUAL', 80);				// quality for jpeg resizing (0 < 100)
@define('IMAGE_PNG_QUAL', 5);				// quality for png resizing (9 < 0)
@define('IMAGE_RESIZING', true);			// resize uploaded images on the server (needs gd installed)
@define('IMAGE_UPLOAD_RESIZE_LARGER', '120%');	// automatically resize uploaded image when larger than n% of window width or height (set to 0% to disable)
@define('IMAGE_UPLOAD_RESIZE_TO', '80%');		// target size in n% of window width or height
@define('OBJECT_DEFAULT_COLORS', '#61b9cf #ff00ff #ffff00');		// default colors for new objects (space-separated string)
@define('PAGE_DEFAULT_GRID_X', 50);			// default grid x spacing in px
@define('PAGE_DEFAULT_GRID_Y', 50);			// default grid y spacing in px
@define('PAGE_GUIDES_X', '');				// show a grid line after n horizontal px (space-separated string)
@define('PAGE_GUIDES_Y', '');				// show a grid line after n vertical px (space-separated string)
@define('PAGES_NEED_AUTH', true);			// page browser needs authentication
@define('REVISIONS_NEED_AUTH', true);		// revisions browser needs authentication
@define('TEXT_AUTO_BR', true);				// automatically add <br> elements for newlines
@define('VIDEO_START_ON_CLICK', true);		// start video on click when autoplay is off

/**
 *	use this function to get the site's base url
 *
 *	@return string base url (not html-encoded)
 */
function base_url()
{
	global $base_url_cached;

    //dieprint_r("Hello, we're in base_url and the cached value is $base_url_cached");

	$temp = BASE_URL;
	if (!empty($temp)) {
		return $temp;
	} elseif (!isset($base_url_cached)) {
		if (empty($_SERVER['HTTPS'])) {
			$base_url_cached = 'http://'.$_SERVER['HTTP_HOST'];
			if ($_SERVER['SERVER_PORT'] != '80') {
				$base_url_cached .= $_SERVER['SERVER_PORT'];
			}
		} else {
			$base_url_cached = 'https://'.$_SERVER['HTTP_HOST'];
			if ($_SERVER['SERVER_PORT'] != '443') {
				$base_url_cached .= $_SERVER['SERVER_PORT'];
			}
		}
		$base_url_cached .= dirname($_SERVER['PHP_SELF']);
		// make sure we have a trailing slash at the end
		if (substr($base_url_cached, -1) != '/') {
			$base_url_cached .= '/';
		}
	}

	return $base_url_cached;
}


?>
