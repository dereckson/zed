<?php

namespace Zed\Engines\Perso\Events;

use Zed\Models\Objects\Perso;

class TryAutoSelect extends BaseEvent {

    /**
     * @var Perso[]
     */
    private array $persos;

    public function isTriggered (): bool {
        return !$this->selector->hasPerso;
    }

    public function handle () : void {
        $this->persos = Perso::get_persos(
            $this->getDatabase(),
            $this->selector->user
        );
        $count = count($this->persos);

        if ($count === 0) {
            $this->askUserToCreatePerso();
        } elseif ($count === 1) {
            $this->autoselect();
        } else {
            $this->askUserToSelectPerso();
        }
    }

    private function askUserToCreatePerso () : never {
        $this->selector->smarty
            ->display("perso_create.tpl");
        exit;
    }

    private function autoselect () : void {
        $this->selector->selectAndSetPerso($this->persos[0]);
    }

    private function askUserToSelectPerso () : never {
        $this->selector->smarty
            ->assign("PERSOS", $this->persos)
            ->display("perso_select.tpl");

        $_SESSION['UserWithSeveralPersos'] = true;
        exit;
    }
}
