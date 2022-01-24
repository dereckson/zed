<?php

namespace Zed\Engines\Perso\Events;

class Logout extends BaseEvent {

    public function isTriggered () : bool {
        return array_key_exists('action', $_GET)
            && $_GET['action'] == 'perso.logout'
            && $this->selector->perso !== null;
    }

    public function handle () : void {
        // User wants to change perso
        $this->selector->perso->on_logout();
        $this->selector->hasPerso = false;
    }

}
