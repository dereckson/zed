<?php

/**
 * Sessions
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This file provides functions to manage sessions. It's not currently properly
 * documented, as it's a temporary old session file, which will be updated soon.
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
 * @todo Replaces this code by the unified Keruald session class.
 */

function session_update () {
    global $db, $IP, $Config;
    //Nettoyage de la session
    /* Initialisation */
    $time_online  =      5 * 60; // Temps après lequel l'utilisateur n'est plus considéré comme online
    $time_session = 2 * 60 * 60; // Durée de vie de la session

    $heureActuelle = time(); //Timestamp UNIX et non MySQL

    /* On fait le ménage */
    $sql = "UPDATE " . TABLE_SESSIONS . " SET online=0 WHERE HeureLimite < $heureActuelle";
    if (!$db->query($sql)) {
        message_die(SQL_ERROR, 'Impossible de mettre à jour les sessions (utilisateurs offline)', '', __LINE__, __FILE__, $sql);
    }

    $sql = "DELETE FROM " . TABLE_SESSIONS . " WHERE SessionLimite < $heureActuelle";
    if (!$db->query($sql)) {
        message_die(SQL_ERROR, "Impossible d'effacer les sessions expirées", '', __LINE__, __FILE__, $sql);
    }

    /* Création / mise à jour de la session utilisateur */
    if (!$_SESSION['ID']) {
        $_SESSION['ID'] = md5(generate_random_string("AAAA1234"));
    }

    $sql = "SELECT * FROM " . TABLE_SESSIONS . " WHERE session_id LIKE '$_SESSION[ID]'";
    if ( !($result = $db->query($sql)) ) {
        message_die(SQL_ERROR, "Problème critique avec les sessions.", '', __LINE__, __FILE__, $sql);
    }

    if ($result->numRows() === 0) {
        $sql = "INSERT INTO " . TABLE_SESSIONS . " (IP, session_id, `Where`, HeureLimite, SessionLimite) VALUES ('$IP', '$_SESSION[ID]', $Config[ResourceID], $heureActuelle + $time_online, $heureActuelle + $time_session)";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Impossible de créer une nouvelle session", '', __LINE__, __FILE__, $sql);
        }
    } else {
        $sql = "UPDATE " . TABLE_SESSIONS . " SET online=1, HeureLimite = $heureActuelle + $time_online, SessionLimite= $heureActuelle + $time_session WHERE session_id = '$_SESSION[ID]'";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Impossible de mettre à jour la session", '', __LINE__, __FILE__, $sql);
        }
    }
}

function nbc () {
//Renvoi du nombre d'usagers connectés
    global $db, $Config;
    $sql = "SELECT count(*) FROM " . TABLE_SESSIONS . " WHERE online=1 AND `Where` = $Config[ResourceID]";
    if ( !($result = $db->query($sql)) ) {
        message_die(SQL_ERROR, "Impossible d'obtenir le nombre d'utilisateurs connectés sur le site web", '', __LINE__, __FILE__, $sql);
    }
    $row = $db->fetchRow($result);
    return $row[0];
}

function get_info ($info) {
//Renvoie une variable de la session
    global $db;
    $sql = "SELECT $info FROM " . TABLE_SESSIONS . " WHERE session_id LIKE '$_SESSION[ID]'";
    if ( !($result = $db->query($sql)) ) {
        message_die(SQL_ERROR, "Impossible d'obtenir $info", '', __LINE__, __FILE__, $sql);
    }
    $row = $db->fetchRow($result);
    return $row[$info];
}

function get_logged_user () {
//Renvoie toutes les informations d'un utilisateur
    global $db;
    $sql = "SELECT * FROM " . TABLE_SESSIONS . " WHERE session_id LIKE '$_SESSION[ID]'";
    if ( !($result = $db->query($sql)) ) {
        message_die(SQL_ERROR, "Impossible d'obtenir les informations de l'utilisateur", '', __LINE__, __FILE__, $sql);
    }
    $row = $db->fetchRow($result);

    require_once('includes/objects/user.php');
    $user = User::get($row['user_id']);

    $user->session = $row;

    return $user;
}

function set_info ($info, $value) {
//Définit une variable session
    global $db;
    $value = ($value === null) ? 'NULL' : "'" . $db->escape($value) . "'";
    $sql = "UPDATE " . TABLE_SESSIONS . " SET $info = $value WHERE session_id LIKE '$_SESSION[ID]'";
    if (!$db->query($sql)) {
        message_die(SQL_ERROR, "Impossible de définir $info", '', __LINE__, __FILE__, $sql);
    }
}

/**
 * Destroys $_SESSION array values, help ID
 */
function clean_session () {
    foreach ($_SESSION as $key => $value) {
        if ($key != 'ID') {
            unset($_SESSION[$key]);
        }
    }
}


/**
 * Logs in user
 */

function login ($user_id, $username) {
    global $db;
    $sql = "UPDATE " . TABLE_SESSIONS . " SET user_id = '$user_id' WHERE session_id LIKE '$_SESSION[ID]'";
    if (!$db->query($sql)) {
        message_die(SQL_ERROR, "Impossible de procéder à la connexion", '', __LINE__, __FILE__, $sql);
    }

    //We send a cookie to print automatically the last username on the login
    //page during 30 days.
    if ($username) {
        setcookie("LastUsername", $username, time() + 2592000);
    }
}

/**
 * Logs out user
 */
function logout () {
    //Anonymous user in session table
    global $db;
    $sql = "UPDATE " . TABLE_SESSIONS . " SET user_id = '-1', perso_id = NULL WHERE session_id LIKE '$_SESSION[ID]'";
    if (!$db->query($sql)) {
        message_die(SQL_ERROR, "Impossible de procéder à la déconnexion", '', __LINE__, __FILE__, $sql);
    }
    clean_session();
}
