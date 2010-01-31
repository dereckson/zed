<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * HTML content
 */

if (!$code = $db->sql_escape($url[1])) {
    message_die(HACK_ERROR, "/page/ must be followed by page code");
}

//
// Handles editor form
//

if ($_POST['code']) {
    //Ask flag admin.pages.editor
    $CurrentPerso->request_flag('admin.pages.editor');
    
    //Gets version
    $sql = "SELECT MAX(page_version) + 1 FROM " . TABLE_PAGES_EDITS .
            " WHERE page_code = '$code'";
    if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Can't fetch pages", '', __LINE__, __FILE__, $sql);
    $row = $db->sql_fetchrow($result);
    $page_version = ($row[0] == "") ? 0 : $row[0];
    
    //Gets other fields
    $page_code = $db->sql_escape($code);
    $page_title = $db->sql_escape($_POST['title']);
    $page_content = $db->sql_escape($_POST['content']);
    $page_edit_reason = $db->sql_escape($_POST['edit_reason']);
    $page_edit_user_id = $CurrentPerso->user_id;
    $page_edit_time = time();
    
    //Saves archive version
    $sql = "INSERT INTO " . TABLE_PAGES_EDITS . " (`page_code`, `page_version`, `page_title`, `page_content`, `page_edit_reason`, `page_edit_user_id`, `page_edit_time`) VALUES ('$page_code', '$page_version', '$page_title', '$page_content', '$page_edit_reason', '$page_edit_user_id', '$page_edit_time')";
    if (!$db->sql_query($sql)) {
        message_die(SQL_ERROR, "Can't save page", '', __LINE__, __FILE__, $sql);
    }
    
    //Saves prod version
    $sql = "REPLACE INTO " . TABLE_PAGES . " (`page_code`, `page_title`, `page_content`) VALUES ('$page_code', '$page_title', '$page_content')";
    if (!$db->sql_query($sql)) {
        message_die(SQL_ERROR, "Can't save page", '', __LINE__, __FILE__, $sql);
    }
    
    $smarty->assign('NOTIFY', "Page $page_code saved, version $page_version.");
}

//
// Gets page
//

$sql = "SELECT page_title, page_content, page_code FROM " . TABLE_PAGES . " WHERE page_code LIKE '$code'";
if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Can't get pages", '', __LINE__, __FILE__, $sql);
$row = $db->sql_fetchrow($result);

switch ($_GET['mode']) {   
    case 'edit':
        $CurrentPerso->request_flag('admin.pages.editor');
        $template = 'page_edit.tpl';
        if ($row) {
            $smarty->assign('PAGE_TITLE', $row['page_title']);
            $smarty->assign('page', $row);
        } else {
            $smarty->assign('PAGE_TITLE', $code);
            $page['page_code'] = $code;
            $smarty->assign('page', $page);
            unset($page);
        }
        $smarty->assign('PAGE_JS', 'FCKeditor/fckeditor.js');
        break;
    
    default: 
        if ($row) {
            $smarty->assign('PAGE_TITLE', $row['page_title']);
            $content = "<h1>$row[page_title]</h1>\n$row[page_content]";
        } else {
            $smarty->assign('PAGE_TITLE', lang_get('PageNotFound'));
            $content = lang_get('PageNotFound');
        }
        
        //Adds edit link
        if ($CurrentPerso->flags['admin.pages.editor']) {
            $content .= '<p class="info" style="text-align: right">[ <a href="?mode=edit">Edit page</a> ]</p>';
        }
        $template = 'raw.tpl';
        $smarty->assign('CONTENT', $content);
        break;
}
    

//
// HTML output
//

//Serves header
include('header.php'); 

//Serves content
$smarty->display($template);

//Serves footer
include('footer.php');
 
?>