<?php

/**
 * API security
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This file provides a cerbere function, to assert the user is correctly
 * authenticated in the API call.
 * 
 * @package     Zed
 * @subpackage  API
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * Determines if localhost calls could be passed.
 *
 * If true, any call from localhost is valid. Otherwise, normal security rules are applied.
 */
define('ALLOW_LOCALHOST', false);

/**
 * Determines if error should be printed.
 *
 * If true, the error will be printed according the FORMAT_ERROR setting. Otherwise, a blank page will be served.
 */
define('OUTPUT_ERROR', true);

/**
 * Determines if the error must be formatted.
 *
 * If true, any error will be sent to api_output ; otherwise, it will be printed as is.
 */
define('FORMAT_ERROR', false);


if (!defined('TABLE_API_KEYS')) {
    /**
     * The table where are located the API keys
     */
    define('TABLE_API_KEYS', 'api_keys');
}

/**
 * Checks if creditentials are okay and exits if not
 *
 * If the creditentials aren't valid, it will prints an error message if
 * OUTPUT_ERROR is defined and true.
 *
 * This error message will be formatted through the api_output function if
 * FORMAT_ERROR is defined and true ; otherwise, it will be print as is.
 *
 * To help debug, you can also define ALLOW_LOCALHOST. If this constant is
 * defined and true, any call from localhost will be accepted, without checking
 * the key.
 *
 * @see cerbere_die
 */
function cerbere () {
    //If ALLOW_LOCALHOST is true, we allow 127.0.0.1 queries
    //If you use one of your local IP in your webserver vhost like 10.0.0.3
    //it could be easier to create yourself a test key
    if (ALLOW_LOCALHOST && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
        return;
    }
    
    //No key, no authentication
    if (!$guid = $_REQUEST['key']) {
        cerbere_die('You must add creditentials to your request.');
    }
    
    //Authenticates user
    global $db;
    $guid = $db->sql_escape($guid);
    $sql = "SELECT key_active FROM " . TABLE_API_KEYS .
           " WHERE key_guid like '$guid'";
    if (!$result = $db->sql_query($sql)) {
        message_die(SQL_ERROR, "Can't get key", '', __LINE__, __FILE__, $sql);
    }
    if ($row = $db->sql_fetchrow($result)) {
        if ($row['key_active']) {
            //key_hits++
            $sql = "UPDATE " . TABLE_API_KEYS . " SET key_hits = key_hits + 1" .
                   " WHERE key_guid like '$guid'";
            if (!$db->sql_query($sql))
                message_die(SQL_ERROR, "Can't record api call", '', __LINE__, __FILE__, $sql);
        } else {
            cerbere_die("Key disabled."); 
        }
    } else {
       cerbere_die("Key doesn't exist."); 
    }
}

/**
 * Prints a message in raw or API format, then exits.
 *
 * The error message will be formatted through api_output if the constant
 * FORMAT_ERROR is defined and true. Otherwise, it will be printed as is.
 *
 * @param string $message The error message to print
 */
function cerbere_die ($message) {
    if (OUTPUT_ERROR) {
        if (FORMAT_ERROR) {
            api_output($message, 'error');
        } else {
            echo $message;
        }
    }
    exit;
}

?>