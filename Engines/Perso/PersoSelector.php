<?php

namespace Zed\Engines\Perso;

use Smarty;

use Zed\Engines\Perso\Events\Create;
use Zed\Engines\Perso\Events\Logout;
use Zed\Engines\Perso\Events\ReadFromSession;
use Zed\Engines\Perso\Events\Select;
use Zed\Engines\Perso\Events\TryAutoSelect;

use Perso;
use User;

use LogicException;

class PersoSelector {

    ///
    /// Properties
    ///

    public User $user;
    public Smarty $smarty;
    public Perso $perso;
    public bool $hasPerso = false;

    ///
    /// Constructors
    //

    /**
     * @param User $user The currently logged user
     */
    public function __construct (User $user, Smarty $smarty) {
        $this->smarty = $smarty;
        $this->user = $user;
    }

    ///
    /// Events processing
    ///

    /**
     * Select a perso using several strategies in a sequential order.
     *
     * Order matters: for example, to be able to log a perso out,
     * we first need to select it from the session.
     *
     * @return \Zed\Engines\Perso\Events\BaseEvent[]
     */
    private function getDefaultEvents () : array {
        return [
            // Strategy 1. Look in session if the perso is already selected.
            new ReadFromSession($this),

            // Strategy 2. Process forms and actions from URL.
            new Create($this),
            new Logout($this),
            new Select($this),

            // Strategy 3. Try to autoselect a perso or ask user for one.
            new TryAutoSelect($this),
        ];
    }

    private function handleEvents () : void {
        $events = $this->getDefaultEvents();

        foreach ($events as $event) {
            $event->run();
        }
    }

    /**
     * Run all the workflow to get a perso.
     */
    public static function load (User $user, Smarty $smarty) : Perso {
        $selector = new self($user, $smarty);
        $selector->handleEvents();

        if (!$selector->hasPerso) {
            throw new LogicException(<<<'EOD'
                The selector has processed the different events and scenarii
                to pick a perso. The expectation after all events have been
                handled is we've selected a perso or have printed any view
                to create or select one.
                
                As such, this code should be unreachable. Debug the different
                'isTriggered' and 'handle' methods of the events to ensure the
                last event exit or return a perso.
                EOD);
        }

        $smarty->assign('CurrentPerso', $selector->perso);

        return $selector->perso;
    }

    ///
    /// Properties
    ///

    /**
     * Select a perso, as a fresh login.
     *
     * If the perso is already logged in,
     * we can call PersoSelector::setPerso instead.
     */
    public function selectAndSetPerso (Perso $perso) : void {
        $perso->on_select();
        $this->setPerso($perso);
    }

    public function setPerso (Perso $perso) : void {
        $this->perso = $perso;
        $this->hasPerso = true;
    }

}
