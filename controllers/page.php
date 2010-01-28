<?php

/*
 * Azhàr, faeries intranet
 * (c) 2009-2010, Wolfæym, some rights reserved
 * Released under BSD license
 *
 * Raw text or HTML content
 */

//
// Gets page
//

$code = $db->sql_escape($url[1]);
$sql = "SELECT page_title, page_content FROM azhar_pages WHERE page_code = '$code'";
if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Can't get events", '', __LINE__, __FILE__, $sql);

if ($row = $db->sql_fetchrow($result)) {
    $smarty->assign('PAGE_TITLE', $row['page_title']);
    $smarty->assign('CONTENT', "<h1>$row[page_title]</h1>\n$row[page_content]");
} else {
    $smarty->assign('PAGE_TITLE', lang_get('PageNotFound'));
    $smarty->assign('CONTENT', lang_get('PageNotFound'));
}

//
// HTML output
//

//Serves header
include('header.php'); 

//Servers content
$smarty->display('raw.tpl');

//Servers footer
include('footer.php');
 
?>