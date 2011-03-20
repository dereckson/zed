<?php

/**
 * Footer
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This is a redirect controller to call the relevant controller,
 * according to the location.
 *
 * It handles and prints the footer elements (tutorial, SmartLine, html footer)
 *
 * @package     Zed
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

if (!defined('NO_FOOTER_EXTRA')) {
    ///
    /// Tutorials div
    ///
    if ($controller != 'explore' && (!array_key_exists('hypership.reached', $CurrentPerso->flags) || $CurrentPerso->flags['hypership.reached'] < 1)) {
        if (!DOJO) $smarty->display('tutorial/dojo.tpl');
        lang_load("tutorials.conf", "ReachHypership");
        $smarty->assign('controller', $controller);
        $smarty->display('tutorial/hypership_reach.tpl');
    }

    ///
    /// Footer options
    ///

    $smarty->assign('MultiPerso', isset($_SESSION['UserWithSeveralPersos']) && $_SESSION['UserWithSeveralPersos']);
    $smarty->assign('SmartLinePrint', (string)$CurrentPerso->get_flag('site.smartline.show') != "0");
    $smarty->assign('SmartLineFormMethod', $CurrentPerso->get_flag('site.smartline.method'));
}

///
/// HTML output
///

lang_load('footer.conf');
$smarty->display('footer.tpl');

?>
