﻿<?php

/**
 * Login/logout
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 * 
 * @package     Zed
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 *
 * @todo reenable OpenID
 * @todo Pick between DumbStore and FileStore and cleans the file accordingly.
 */

if (!file_exists('/dev/urandom')) {
    //We're on Windows, without reliable source of random numbers
    define('Auth_OpenID_RAND_SOURCE', null);
}

//require_once('Auth/OpenID/Consumer.php');
//require_once('Auth/OpenID/DumbStore.php');
//require_once('Auth/OpenID/FileStore.php');

/*
function get_openid_consumer () {
    if (!file_exists('/dev/urandom')) {
        //We're on Windows, without reliable source of random numbers
        define('Auth_OpenID_RAND_SOURCE', null);
    }
   
    $fs = new Auth_OpenID_FileStore('cache/openid');
    return new Auth_OpenID_Consumer($fs);
}
*/

function openid_login ($url) {
    global $db, $_SESSION, $LoginError, $LoginSuccessful;
    $url = $db->sql_escape($url);
    $sql = 'SELECT user_id FROM ' . TABLE_USERS_OPENID
          . " WHERE openid_url LIKE '$url'";
    if ($user_id = $db->sql_query_express($sql)) {
        $sql = "UPDATE " . TABLE_SESSIONS . " SET user_id = '$user_id' WHERE session_id LIKE '$_SESSION[ID]'";
        if (!$db->sql_query($sql)) message_die(SQL_ERROR, "Impossible de procéder à la connexion", '', __LINE__, __FILE__, $sql);
        $LoginSuccessful = true;
        setcookie("LastOpenID", $url, time() + 2592000);
        header("location: " . get_url());
    } else {
        $LoginError = "To join Zed, you need an invite. Read the source to get one.";
    }
}

if ($_GET['action'] == 'openid.login') {
    $LoginError = "OpenID temporarily disabled.";
/*
    //Gets Auth_OpenID_Consumer instance
    $fs = new Auth_OpenID_DumbStore("rien n'est sûr mais c'est une piste");
    //$fs = new Auth_OpenID_FileStore('cache/openid');
    $consumer = new Auth_OpenID_Consumer($fs);
    //$consumer = get_openid_consumer();
    
    //Completes the OpenID transaction
    $reply = $consumer->complete(get_server_url() . $_SERVER['REQUEST_URI']);
    if ($reply->status == Auth_OpenID_SUCCESS) {
        openid_login($reply->endpoint->claimed_id);
    } elseif ($reply->message) {
        $LoginError = "[OpenID] $reply->message";
    } else {
        $LoginError = "[OpenID] $reply->status";
    }    
*/
} elseif ($_POST['LogIn']) {
    //User have filled login form
    if ($_POST['openid']) {        
	$LoginError = "OpenID temporarily disabled.";
/*
        //Gets Auth_OpenID_Consumer instance
        $fs = new Auth_OpenID_DumbStore("rien n'est sûr mais c'est une piste");
        //$fs = new Auth_OpenID_FileStore('cache/openid');
        $consumer = new Auth_OpenID_Consumer($fs);
        //$consumer = get_openid_consumer();
            
        //Starts the OpenID transaction and redirects user to provider url
        if ($request = $consumer->begin($_POST['openid'])) {
            $url = $request->redirectURL(get_server_url(), "$Config[SiteURL]/?action=openid.login", false);
            header("location: $url");
            $LoginError = '<a href="' . $url . '">Click here to continue login</a>';
        } else {
            $LoginError = 'Invalid OpenID URL.';
        }
*/
    } else {
        //GESTION LOGIN
        $Login = $_POST['username'];
        $sql = "SELECT user_password, user_id FROM " . TABLE_USERS . " WHERE username = '$Login'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Impossible d'interroger le listing des utilisateurs", '', __LINE__, __FILE__, $sql);
        if ($row = $db->sql_fetchrow($result)) {
            if (!$row['user_password']) {
                $LoginError = "This account exists but haven't a password defined. Use OpenID or contact dereckson (at) espace-win.org to fix that.";
            } elseif ($row['user_password'] != md5($_POST['password'])) {
                //PASS NOT OK
                $LoginError = "Incorrect password.";
            } else {
                login($row[user_id], $Login);
                $LoginSuccessful = true;
            }				
        } else {
            //Idiot proof facility
            //Redirects people using login page as invitation claim page
            $code = $db->sql_escape($_POST['password']);
            $sql = "SELECT * FROM " . TABLE_USERS_INVITES . " WHERE invite_code = '$code'";
            if (!$result = $db->sql_query($sql)) {
                message_die(SQL_ERROR, "Can't get invites", '', __LINE__, __FILE__, $sql);
            }
            if ($row = $db->sql_fetchrow($result)) {
                $url = get_url('invite', $_POST['password']);
                header('location: ' . $url);
            }
            
            //Login not found
            $LoginError = "Login not found.";
        }
    }
} elseif ($_POST['LogOut'] || $_GET['action'] == "user.logout") {
    Logout();
}
?>
