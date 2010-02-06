<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * API security
 * 
 */

define('ALLOW_LOCALHOST', false);
define('OUTPUT_ERROR', true);
define('FORMAT_ERROR', false);
if (!defined('TABLE_API_KEYS')) define('TABLE_API_KEYS', 'api_keys');

/*
 * Checks if creditentials are okay and exits after a message error if not
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