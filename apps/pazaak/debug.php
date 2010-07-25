<?php

/**
 * Pazaak webservice client, debug console
 *
 * This is a sandbox to test the Pazaak web service.
 *
 * 2010-07-20: Pazaak will be noted as a deprecated project at 2010-09-15.
 * 
 * @package     Zed
 * @subpackage  Pazaak
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * Prints human-readable information about a variable  (like the print_r command),
 * enclosed in <div class="debugCode"><pre></pre></div> tags,
 * to have a preformatted HTML output.
 *
 * @param mixed The expression to be printed
 */
function dprint_r ($expression) {
    echo '<div class="debugCode"><pre>';
    print_r($expression);
    echo '</pre></div>';
}

$client = new SoapClient('http://10.0.0.4:49319/PazaakService.asmx?WSDL');
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