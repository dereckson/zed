<?php

/**
 * Database calling class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2015, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This file provides a calling class, which read the configuration, ensures
 * the database class for the db engine given in config exists and initializes
 * it.
 *
 * The class to call is determined from the following preference:
 * <code>
 * $Config['database']['engine'] = 'MySQL'; //will use DatabaseMySQL class.
 * </code>
 *
 * @package     Zed
 * @subpackage  Database
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2015 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * Databasecaller
 */
class Database {
    /**
     * Gets the database instance, initializing it if needed
     *
     * The correct database instance to initialize will be determined from the
     * $Config['database']['engine'] preference.
     *
     * The database class to use will be Database + (preference engine, capitalized)
     *
     * This method will creates an instance of the specified object,
     * calling the load static method from this object class.
     *
     * Example:
     * <code>
     * $Config['database']['engine'] = 'quux';
     * $db = Database::load(); //Database:load() will call DatabaseQuux:load();
     * </code>
     *
     * @return Database the database instance
     */
    static function load () {
        global $Config;
        if (
            !array_key_exists('database', $Config) ||
            !array_key_exists('engine', $Config['database'])
        ) {
            //database is not configured or engine is not specified
            message_die(GENERAL_ERROR, 'A database engine (a MySQL variant is recommended) should be configured. Please ensure you have a ["database"]["engine"] value in the configuration.', "Setup issue");
        } else {
            //engine is specified in the configuration
            $engine = $Config['database']['engine'];
        }

        $engine_file = 'includes/db/' . $engine . '.php';
        $engine_class = 'Database' . ucfirst($engine);

        if (!file_exists($engine_file)) {
            message_die(GENERAL_ERROR, "Can't initialize $engine database engine.<br />$engine_file not found.", 'Setup issue');
        }

        require_once($engine_file);
        if (!class_exists($engine_class)) {
            message_die(GENERAL_ERROR, "Can't initialize $engine database engine.<br />$engine_class class not found.", 'Setup issue');
        }
        return call_user_func(array($engine_class, 'load'));
    }
    
    static function cleanupConfiguration () {
        global $Config;
        unset($Config['database']['password']);
    }
}
