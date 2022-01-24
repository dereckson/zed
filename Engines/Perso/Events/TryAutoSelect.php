<?php

namespace Zed\Engines\Perso\Events;

use Perso;

class TryAutoSelect extends BaseEvent {

    /**
     * @var Perso[]
     */
    private array $persos;

    public function isTriggered (): bool {
        return !$this->selector->hasPerso;
    }

    public function handle () : void {
        $this->persos = Perso::get_persos($this->selector->user->id);
        $count = count($this->persos);

        if ($count === 0) {
            $this->askUserToCreatePerso();
        } elseif ($count === 1) {
            $this->autoselect();
        } else {
            $this->askUserToSelectPerso();
        }
    }

    private function askUserToCreatePerso () : void {
        $this->selector->smarty
            ->display("perso_create.tpl");
        exit;
    }

    private function autoselect () : void {
        $this->selector->selectAndSetPerso($this->persos[0]);
    }

    private function askUserToSelectPerso () : void {
        $this->selector->smarty
            ->assign("PERSOS", $this->persos)
            ->display("perso_select.tpl");

        $_SESSION['UserWithSeveralPersos'] = true;
        exit;
    }
}
