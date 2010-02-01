<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Login/logout
 */

if (!file_exists('/dev/urandom')) {
    //We're on Windows, without reliable source of random numbers
    define('Auth_OpenID_RAND_SOURCE', null);
}

require_once('Auth/OpenID/Consumer.php');
require_once('Auth/OpenID/DumbStore.php');
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
} elseif ($_POST['LogIn']) {
	//User have filled login form
    if ($_POST['openid']) {        
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
                $sql = "UPDATE " . TABLE_SESSIONS . " SET user_id = '$row[user_id]' WHERE session_id LIKE '$_SESSION[ID]'";
                if (!$db->sql_query($sql)) message_die(SQL_ERROR, "Impossible de procéder à la connexion", '', __LINE__, __FILE__, $sql);
                $LoginSuccessful = true;
                setcookie("LastUsername", $Login, time() + 2592000);
            }				
        } else {
            //Login n'existe pas
            $LoginError = "Login not found.";
        }
    }
} elseif ($_POST['LogOut'] || $_GET['action'] == "user.logout") {
    Logout();
}
?>