<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Footer
 */
   
if (!defined('NO_FOOTER_EXTRA')) {
    ///
    /// Tutorials div
    ///
    if ($CurrentPerso->flags['hypership.reached'] < 1 && $controller != 'explore') {
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