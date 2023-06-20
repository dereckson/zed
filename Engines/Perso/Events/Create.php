<?php

namespace Zed\Engines\Perso\Events;

use Zed\Models\Objects\Perso;

/**
 * This event is triggered when a perso is created,
 * for example after a new perso form posted.
 */
class Create extends BaseEvent {

    private ?Perso $createdPerso = null;
    private array $errors = [];

    public function isTriggered () : bool {
        return
            array_key_exists('form', $_POST)
            &&
            $_POST['form'] === 'perso.create';
    }

    public function handle () : void {
        $isCreated = Perso::create_perso_from_form(
            $this->getDatabase(),
            $this->selector->user,
            $this->createdPerso,
            $this->errors
        );

        if ($isCreated) {
            // We've got a winner.
            $this->login();
        } else {
            // Let's try again.
            $this->printAgainCreatePersoForm();
        }
    }

    private function login () : void {
        $this->selector->smarty
            ->assign('NOTIFY', lang_get('NewCharacterCreated'));

        $this->selector->selectAndSetPerso($this->createdPerso);
    }

    private function printAgainCreatePersoForm () : void {
        $this->selector->smarty
            ->assign('WAP', join("<br />", $this->errors))
            ->assign('perso', $this->createdPerso);
    }

}
