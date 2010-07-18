<?php
/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Cache class: void
 *
 * 0.1    2010-07-06 22:55    Initial version [DcK]
 *
 * This class doesn't cache information, it'a void wrapper
 *  get will always return null
 *  set and delete do nothing
 *
 * This class implements a singleton pattern.
 *
 */

class CacheVoid {
    /*
     * @var CacheVoid the current cache instance
     */
    static $instance = null;
    
    /*
     * Gets the cache instance, initializing it if needed
     * @eturn Cache the cache instance, or null if nothing is cached
     */
    static function load () {       
        if (self::$instance === null) {
            self::$instance = new CacheVoid();
        }
        
        return self::$instance;
    }
    
    /*
     * Gets the specified key's data
     */
    function get ($key) {
       return null;
    }

    /*
     * Sets the specified data at the specified key
     */    
    function set ($key, $value) { }

    /*
     * Deletes the specified key's data
     */
    function delete ($key) { }
}
?>