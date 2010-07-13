<?php
/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Cache class: memcached
 *
 * 0.1    2010-07-06 22:55    Initial version [DcK]
 *
 * !!! This class uses the Memcached extension AND NOT the Memcache ext !!!!
 *
 * References:
 *  - http://www.php.net/manual/en/book/memcached.php
 *  - http://memcached.org
 *
 * This class implements a singleton pattern.
 *
 */

class CacheMemcached {
    
    /*
     * @var CacheMemcached the current cache instance
     */
    static $instance = null;

    /*
     * @var Memcached the Memcached object
     */    
    private $memcached = null;
    
    /*
     * Gets the cache instance, initializing it if needed
     * @eturn Cache the cache instance, or null if nothing is cached
     */
    static function load () {       
        //Checks extension is okay
        if (!extension_loaded('memcached')) {
            if (extension_loaded('memcache')) {
                message_die(GENERAL_ERROR, "Can't initialize $engine cache engine.<br />PHP extension memcached not loaded.<br /><strong>!!! This class uses the Memcached extension AND NOT the Memcache extension (this one is loaded) !!!</strong>", 'Cache');
            } else {
                message_die(GENERAL_ERROR, "Can't initialize $engine cache engine.<br />PHP extension memcached not loaded.", 'Cache');
            }
        }
    
        //Creates the Memcached object if needed
        if (self::$instance === null) {
            global $Config;
            
            self::$instance = new CacheMemcached();
            self::$instance->memcached = new Memcached();
            self::$instance->memcached->addServer(
                $Config['cache']['server'],
                $Config['cache']['port']
            );
        }
        
        return self::$instance;
    }
    
    /*
     * Gets the specified key's data
     */
    function get ($key) {
       return $this->memcached->get($key);
    }

    /*
     * Sets the specified data at the specified key
     */    
    function set ($key, $value) {
        return $this->memcached->set($key, $value);
    }

    /*
     * Deletes the specified key's data
     */
    function delete ($key) {
        return $this->memcached->delete($key);
    }
}
?>