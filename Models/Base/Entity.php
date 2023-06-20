<?php

namespace Zed\Models\Base;

abstract class Entity implements EntityDatabaseInterface  {

    ///
    /// Database layer
    ///

    use WithDatabase;

}
