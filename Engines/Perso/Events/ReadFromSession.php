<?php

namespace Zed\Engines\Perso\Events;

use Perso;

class ReadFromSession extends BaseEvent {

    public function isTriggered (): bool {
        return isset($this->selector->user->session['perso_id']);
    }

    public function handle (): void {
        // Gets perso ID from the session data
        $perso = Perso::get($this->selector->user->session['perso_id']);

        $this->selector->setPerso($perso);
    }
}
