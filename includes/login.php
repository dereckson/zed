<?php

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
 * @todo Pick between DumbStore and FileStore and cleans the file accordingly.
 */

///
/// OpenID helper code
///

require_once('Auth/OpenID/Consumer.php');
require_once('Auth/OpenID/FileStore.php');

/**
 * Gets an Auth_OpenID_Consumer instance
 *
 * @return Auth_OpenID_Consumer the instance
 */
function get_openid_consumer () {
    if (!file_exists('/dev/urandom')) {
        //We don't have a reliable source of random numbers
        define('Auth_OpenID_RAND_SOURCE', null);
    }

    $fs = new Auth_OpenID_FileStore(CACHE_DIR . '/openid');
    return new Auth_OpenID_Consumer($fs);
}

/**
 * Logs in the user if the OpenID is recognized.
 * Otherwise, sets an error message.
 *
 * @param string $url The OpenID URL
 */
function openid_login ($url) {
    global $db, $_SESSION, $LoginError, $LoginSuccessful;
    $url = $db->sql_escape($url);
    $sql = 'SELECT user_id FROM ' . TABLE_USERS_AUTH
          . " WHERE auth_type = 'OpenID' AND auth_identity LIKE '$url'";
    if ($user_id = $db->sql_query_express($sql)) {
        $sql = "UPDATE " . TABLE_SESSIONS . " SET user_id = '$user_id' WHERE session_id LIKE '$_SESSION[ID]'";
        if (!$db->sql_query($sql)) message_die(SQL_ERROR, "Can't update session table", '', __LINE__, __FILE__, $sql);
        $LoginSuccessful = true;
        setcookie("LastOpenID", $url, time() + 2592000);
        header("location: " . get_url());
    } else {
        $LoginError = "Read the source to get an invite.";
    }
}

///
/// Login procedural code
///

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'openid.login') {
    //Gets Auth_OpenID_Consumer instance
    $consumer = get_openid_consumer();

    //Completes the OpenID transaction
    $reply = $consumer->complete(get_server_url() . $_SERVER['REQUEST_URI']);
    if ($reply->status == Auth_OpenID_SUCCESS) {
        openid_login($reply->endpoint->claimed_id);
    } elseif ($reply->message) {
        //TODO: $reply->message could be rather long and won't fit in the UI
	//space. You can wish to add code to print $LoginError elsewhere if
	//too long.
	$LoginError = "[OpenID] $reply->message";
    } else {
        $LoginError = "[OpenID] $reply->status";
    }
} elseif (isset($_POST['LogIn'])) {
    //User have filled login form
    if ($_POST['openid']) {
        //Gets Auth_OpenID_Consumer instance
        $consumer = get_openid_consumer();

        //Starts the OpenID transaction and redirects user to provider url
        if ($request = $consumer->begin($_POST['openid'])) {
            $url = $request->redirectURL(get_server_url(), "$Config[SiteURL]/?action=openid.login", false);
            header("location: $url");
            $LoginError = '<a href="' . $url . '">Click here to continue login</a>';
        } else {
            $LoginError = 'Invalid OpenID URL.';
        }
    } else {
        //Login

	//Authentications way, the user/password in last.
	//OpenID is handled by a separate logic.
	$Login = $_POST['username'];
        $authentications = array();
	if ($useYubiCloud = array_key_exists('YubiCloud', $Config) ) {
	    $authentications[] = new YubiCloudAuthentication($_POST['password'], $Login);
	}
        if ($Login) {
	    $authentications[] = new UserPasswordAuthentication($Login, $_POST['password']);
	}

	$loginSuccessful = false;
	foreach ($authentications as $authentication) {
	    if ($authentication->isValid()) {
		$loginSuccessful = true;
		//Logs in user
		login($authentication->getUserID(), $Login);
	    } else {
		$loginError = $authentication->getError();
	    }
	    if (!$authentication->canTryNextAuthenticationMethod()) {
		break;
	    }
	}

	//Tests if the password wouldn't match an invite code
        //If so, redirects people using login page as invitation claim page
        if (!$LoginSuccessful) {
	    $code = $db->sql_escape($_POST['password']);
	    $sql = "SELECT * FROM " . TABLE_USERS_INVITES . " WHERE invite_code = '$code'";
	    if (!$result = $db->sql_query($sql)) {
		message_die(SQL_ERROR, "Can't get invites", '', __LINE__, __FILE__, $sql);
	    }
	    if ($row = $db->sql_fetchrow($result)) {
		$url = get_url('invite', $_POST['password']);
		header('location: ' . $url);
	    }
        }
    }
} elseif (isset($_POST['LogOut']) || $action == "user.logout") {
    Logout();
}
