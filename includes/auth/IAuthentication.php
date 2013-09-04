<?php

/**
 * Authentication method interface.
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
 * Authentication method interface.
 */
interface IAuthentication {
    /**
     * Determines if an user has been authenticated.
     *
     * @return boolean true if the user has successfully been authenticated; otherwise, false.
     */
    public function isValid ();

    /**
     * Gets the last authentication error
     *
     * @return string The last authentication error
     */
    public function getError();

    /**
     * Gets the user_id matching the key
     *
     * @return int the user ID
     */
    public function getUserID ();

    /**
     * Determines if the next authentication method could be tried if this one failed.
     *
     * This allow when a method has failed in such a way the user must be warned to warn it,
     * returning false.
     *
     * @return bool true if authentication can go on to the next method; otherwise, false
     */
    public function canTryNextAuthenticationMethod();
}
