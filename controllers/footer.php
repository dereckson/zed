<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Footer
 */

///
/// Tutorials div
///

if ($CurrentPerso->flags['hypership.reached'] < 1 && $controller != 'explore') {
    if (!DOJO) $smarty->display('tutorial/dojo.tpl');
    lang_load("tutorials.conf", "ReachHypership");
    $smarty->display('tutorial/hypership_reach.tpl');
}

///
/// HTML output
///

$smarty->assign('MultiPerso', isset($_SESSION['UserWithSeveralPersos']) && $_SESSION['UserWithSeveralPersos']);
lang_load('footer.conf');
$smarty->display('footer.tpl');
 
?>