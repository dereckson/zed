<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Raw text or HTML content
 */

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', $title);
include('header.php'); 

//Serves content
$smarty->display('raw.tpl');

//Serves footer
include('footer.php');
 
?>