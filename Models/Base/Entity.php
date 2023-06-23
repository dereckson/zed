<?php

namespace Zed\Models\Base;

use Zed\Engines\Database\WithDatabase;

abstract class Entity implements EntityDatabaseInterface  {

    ///
    /// Database layer
    ///

    use WithDatabase;

}
