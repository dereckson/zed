<?php

namespace Zed\Engines\Perso\Events;

use Keruald\Database\DatabaseEngine;
use Zed\Engines\Perso\PersoSelector;

abstract class BaseEvent {

    protected PersoSelector $selector;

    public function __construct (PersoSelector $selector) {
        $this->selector = $selector;
    }

    public function run () : void {
        if ($this->isTriggered()) {
            $this->handle();
        }
    }

    abstract public function isTriggered() : bool;

    abstract public function handle() : void;

    public function getDatabase () : DatabaseEngine {
        return $this->selector->db;
    }

}
