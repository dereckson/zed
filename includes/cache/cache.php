<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Cache calling class.
 *
 * 0.1    2010-07-06 22:45    Initial version [DcK]
 *
 */

class Cache {
    /*
     * Gets the cache instance, initializing it if needed
     * @eturn Cache the cache instance, or null if nothing is cached
     */
    static function load () {
        global $Config;
        if (
            !array_key_exists('cache', $Config) ||
            !array_key_exists('engine', $Config['cache'])
        ) {
            //cache is not configured or engine is not specified
            return null;
        }
        
        $engine = $Config['cache']['engine'];
        $engine_file = 'includes/cache/' . $engine . '.php';
        $engine_class = 'Cache' . ucfirst($engine);
        
        if (!file_exists($engine_file)) {
            message_die(GENERAL_ERROR, "Can't initialize $engine cache engine.<br />$engine_file not found.", 'Cache');
        }
        
        require_once($engine_file);
        if (!class_exists($engine_class)) {
            message_die(GENERAL_ERROR, "Can't initialize $engine cache engine.<br />$engine_class class not found.", 'Cache');
        }
        
        return call_user_func(array($engine_class, 'load'));
    }
}

?>