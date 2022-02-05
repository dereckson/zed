<?php

namespace Zed\Engines\Database;

use Keruald\Database\DatabaseEngine;
use Keruald\Database\Database as BaseDatabase;
use Keruald\Database\Exceptions\EngineSetupException;

class Database {

    /**
     * Gets the database instance, initializing it if needed
     *
     * The correct database instance to initialize will be determined from the
     * $Config['database']['engine'] preference. Expected value is an instance
     * of DatabaseEngine.
     *
     * Example:
     * <code>
     * $Config['database']['engine'] = 'Foo\Quux';
     * $db = Database::load(); // Database::load() will call Foo\Quux::load();
     * </code>
     */
    static function load (array &$config) : DatabaseEngine {
        try {
            return BaseDatabase::initialize($config);
        } catch (EngineSetupException $ex) {
            message_die(GENERAL_ERROR, $ex->getMessage(), "Setup issue :: DB");
        }
    }
}
