<?php

namespace Zed\Models\Base;

use Keruald\Database\DatabaseEngine;

trait WithDatabase {

    private DatabaseEngine $db;

    /**
     * @return DatabaseEngine
     */
    public function getDatabase () : DatabaseEngine {
        return $this->db;
    }

    /**
     * @param DatabaseEngine $db
     */
    public function setDatabase(DatabaseEngine $db) : void {
        $this->db = $db;
    }

}
