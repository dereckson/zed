<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * void content
 */

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', $title);
include('header.php');

//Doesn't serve any content;

//Servers footer
include('footer.php');
 
?>