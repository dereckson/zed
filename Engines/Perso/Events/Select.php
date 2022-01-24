<?php

namespace Zed\Engines\Perso\Events;

use InvalidArgumentException;

use Perso;

class Select extends BaseEvent {

    public function isTriggered () : bool {
        return array_key_exists('action', $_GET)
            && $_GET['action'] == 'perso.select';
    }

    public function handle () : void {
        // User has explicitly selected a perso

        if (!array_key_exists('perso_id', $_GET)) {
            throw new InvalidArgumentException(
                "The perso ID is missing from the URL ('perso_id')."
            );
        }

        $perso = new Perso($_GET['perso_id']);
        if ($perso->user_id !== $this->selector->user->id) {
            message_die(HACK_ERROR, "This isn't your perso.");
        }

        $this->selector->selectAndSetPerso($perso);
    }

}
