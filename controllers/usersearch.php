<?php

/**
 * User search
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This is a controller doing nothing else than call header and footer.
 *
 * The controller uses the usersearch.tpl and directory views (cf. Azhàr code)
 *
 * Not yet implemented, It should handle /users URL
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
 *
 * @todo implement it
 */

//Libs
require_once('includes/objects/ProfilePhoto.php');

//
// Does the search
//

//Search type
switch ($resource = $url[1]) {
    case '':

        break;

    case 'online':
        $sql = "SELECT u.username, u.user_id, u.user_longname FROM " .
               TABLE_USERS . " u, " . TABLE_SESSIONS .
               " s WHERE s.online = 1 AND u.user_id = s.user_id
               ORDER BY HeureLimite DESC";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to query the table", '', __LINE__, __FILE__, $sql);
        }
        $i = 0;
        while ($row = $db->sql_fetchrow($result)) {
            $users[$i]->id = $row['user_id'];
            $users[$i]->username = $row['username'];
            $users[$i]->longname = $row['user_longname'];
            $i++;
        }

        $title = sprintf(lang_get('UsersOnline'), $i, s($i));
        break;

    case 'directory':
        $sql = 'SELECT username, user_longname FROM ' . TABLE_USERS .
               ' WHERE user_active < 2 ORDER by user_longname ASC';
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to query the table", '', __LINE__, __FILE__, $sql);
        }
        $i = 0;
        while ($row = $db->sql_fetchrow($result)) {
            $users[$i]->username = $row['username'];
            $users[$i]->longname = $row['user_longname'];
            $i++;
        }
        $title = lang_get('Directory');
        $mode = 'directory';
        break;

    default:
        $smarty->assign('WAP', lang_get('Nay'));
        break;
}

switch ($mode) {
    case 'directory':
        $template = 'directory.tpl';
        $smarty->assign('USERS', $users);
        break;

    default:
        //Prepares avatars
        if (count($users)) {
            foreach ($users as $user) {
                $name = $user->longname ? $user->longname : $user->username;
                $user->avatar = ProfilePhoto::get_avatar($user->id, $name);
            }
        }
        $template = 'usersearch.tpl';
        $smarty->assign('TITLE', $title);
        $smarty->assign('USERS', $users);
        break;
}

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_CSS', 'usersearch.css');
$smarty->assign('PAGE_TITLE', $title);
include('header.php');

//Serves content
if ($template)
    $smarty->display($template);

//Serves footer
include('footer.php');
