<?php
    function dprint_r ($var) {
        echo '<div class="debugCode"><pre>';
        print_r($var);
        echo '</pre></div>';
    }
    
    $client = new SoapClient('http://localhost:49319/PazaakService.asmx?WSDL');
    $game = $client->QuickQuickStart()->QuickQuickStartResult;
    
    echo "<h1>Game $game->GUID</h1>";
    echo "<h2>Table</h2>";
    $cards = $game->PlayerTable->string;
    if (is_array($cards)) {
        echo "<table><tr>";
        foreach ($cards as $card)
            echo "<td>$card</td>";
        echo "</tr></table>";
    } else {
        echo "<p>$cards</p>";
    }
    echo "<h2>Your hand</h2>";
    
    $cards = $game->PlayerHand->string;
    if (count($cards)) {
        echo "<table><tr>";
        foreach ($cards as $card)
            echo "<td>$card</td>";
        echo "</tr></table>";
    } else {
        echo "<p>-</p>";
    }
    
    echo "<h2>Debug</h2>";
    dprint_r($game);

?>