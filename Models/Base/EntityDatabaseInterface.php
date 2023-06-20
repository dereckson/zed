<?php

namespace Zed\Models\Base;

use Keruald\Database\DatabaseEngine;

/*
 * The save_to_database and load_from_database methods
 * can use $db = $this->getDatabase();
 */

interface EntityDatabaseInterface {

    public function save_to_database () : void;

    public function load_from_database () : bool;

    public function getDatabase () : DatabaseEngine;

}
