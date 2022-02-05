<?php

/**
 * YubiCloud authentication class.
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

require_once('Auth/Yubico.php');

/**
 * YubiCloudAuthentication class
 *
 * Authenticates a user through YubiCloud
 */
class YubiCloudAuthentication implements IAuthentication {
    /**
     * The key
     * @var string
     */
    private $key;

    /**
     * The username who should match the key
     * @var string
     */
    private $username;

    /**
     * The user_id
     * @var int
     */
    private $user_id;

    /**
     * Indicates if the error MUST be returned to the user
     * @var string
     */
    private $mustThrowError = false;

    /**
     * The last validation error
     * @var string
     */
    public $error;

    /**
     * Initializes a new instance of the key
     *
     * @param string $key The key
     */
    public function __construct ($key, $username = null) {
        $this->username = $username;
        $this->key = $key;
    }

    /**
     * Validates the specified key's characters to determine if it looks like an OTP
     *
     * @return boolean true if the input seems an OTP key; otherwise, false.
     */
    function looksValidOTP () {
        return preg_match("/^[cbdefghijklnrtuv]{32,48}$/", $this->key);
    }

    /**
     * Gets public identity
     *
     * @return string Public identity
     */
    function getPublicIdentity () {
        return substr($this->key, 0, 12);
    }

    /**
     * Validates an OTP key against the YubiCloud servers
     *
     * @return boolean true if the input is a valid OTP key; otherwise, false.
     */
    function isValid () {
        global $Config;

        //No need to lost time to query server if format is incorrect.
        if (!$this->looksValidOTP()) {
            $this->error = "Not the expected YubiKey OTP format.";
            return false;
        }

        //Query YubiCloud. We stop validation tests if that fails.
        $yubi = new Auth_Yubico(
            $Config['YubiCloud']['ClientID'],
            $Config['YubiCloud']['SecretKey']
        );
        $auth = $yubi->verify($this->key);
        if (@PEAR::isError($auth)) {
            $this->error = $auth->getMessage();
            return false;
        }

        //Note: We first query the YubiCloud server, then we check if we can use the key
        //      as the key is an OTP (*one time* password), this allow to invalidate it.
        //      If we wouldn't do that, an attacker can reuse this password for another site.
        if (!$this->computeUserID()) {
            $this->error = "Valid YubiKey OTP. But the key doesn't match any account.";
            $this->mustThrowError = true;
            return false;
        }

        //Finally, if someone puts also a login, we'll check this user ID match this username
        if ($this->username) {
            $user = User::get($this->user_id);
            if ($this->username != $user->name) {
            $this->error = "Valid YubiKey OTP but fix or remove your username.";
            $this->mustThrowError = true;
            return false;
            }
        }

        return true;
    }

    /**
     * Gets the user_id matching the username
     *
     * You first need to validate the username, calling the isValid method.
     */
    function computeUserID () {
        global $db;

        /**
         * Here a MySQL record for a valid OTP
         * +---------+-----------+---------------+-----------------+---------+
         * | auth_id | auth_type | auth_identity | auth_properties | user_id |
         * +---------+-----------+---------------+-----------------+---------+
         * |       2 | YubiKey   | cccccccccccc  | NULL            |    1234 |
         * +---------+-----------+---------------+-----------------+---------+
         */
        $authentication_identity = $this->getPublicIdentity();
        $sql = "SELECT user_id FROM " . TABLE_USERS_AUTH
             . " WHERE auth_type = 'YubiKey' AND auth_identity = '$authentication_identity'";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Can't query users authentication table.", '', __LINE__, __FILE__, $sql);
        }
        if ($row = $db->fetchRow($result)) {
            $this->user_id = $row['user_id'];
            return true;
        }
        return false;
    }

    /**
     * Gets the last authentication error
     *
     * @return string The last authentication error
     */
    function getError () {
        return $this->error;
    }

    /**
     * Gets the user_id matching the key
     *
     * You first need to query the authentication table, calling the computeUserID method.
     * This is automatically done by IsValid, as we need to validate key matches someone.
     *
     * @return int the user ID
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
