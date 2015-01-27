<?php

/**
 * User/password authentication class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2013, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  Auth
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2013 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * UserPasswordAuthentication class
 *
 * Authenticates a user with an username and a password
 */

/**
 * Login/pass authentication
 */
class UserPasswordAuthentication implements IAuthentication {
    /**
     * The username
     * @var string
     */
    private $username;

    /**
     * The password in clear text
     * @var string
     */
    private $password;

    /**
     * The last authentication error
     * @var string
     */
    private $error;

    /**
     * The user_id matching the username
     * @var int
     */
    private $user_id;

    /**
     * Indicates if the error MUST be returned to the user
     * @var string
     */
    private $mustThrowError = false;

    /**
     * Initializes a new instance of the UserPasswordAuthentication class
     *
     * @param string $username The username
     * @param string $passwordThe password
     */
    public function __construct ($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Gets the hash of the password
     *
     * @param string $password The password in clear text
     * @return string The hashed password
     */
    function getPasswordHash ($password) {
        return md5($password); //TODO: replace this by a salted MD5 or better, by another algo.
    }

    /**
     * Determines if the login/pass is valid
     *
     * @return bool true if the login/pass is valid; otherwise, false.
     */
    function isValid () {
        global $db;
    
        $sql = "SELECT user_password, user_id FROM " . TABLE_USERS . " WHERE username = '$this->username'";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Can't query users table.", '', __LINE__, __FILE__, $sql);
        }
        if ($row = $db->sql_fetchrow($result)) {
            $this->user_id = $row['user_id'];
            if (!$row['user_password']) {
                $this->error  = "This account exists but haven't a password defined. Use OpenID or contact dereckson (at) espace-win.org to fix that.";
                $mustThrowError = true;
            } elseif ($row['user_password'] != $this->getPasswordHash($this->password)) {
                //PASS NOT OK
                $this->error  = "Incorrect password.";
            } else {
                return true;
            }
        } else {
            $this->error = "Login not found.";
            $mustThrowError = true;
        }

        return false;
    }

    /**
     * Gets the last authentication error
     *
     * @return string The last error
     */
    function getError () {
        return $this->error;
    }

    /**
     * Gets the user_id matching the username
     * You first need to validate the username, calling the isValid method.
     *
     * @return int The user ID
     */
    function getUserID () {
        return $this->user_id;
    }

    /**
     * Determines if the next authentication method could be tried if this one failed.
     *
     * @return bool true if authentication can go on to the next method; otherwise, false
     */
    function canTryNextAuthenticationMethod () {
        return !$this->mustThrowError;
    }

}
