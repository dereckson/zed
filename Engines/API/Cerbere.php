<?php

namespace Zed\Engines\API;

use Keruald\Database\DatabaseEngine;
use Keruald\OmniTools\HTTP\Requests\Request;
use Keruald\OmniTools\Identifiers\UUID;

use Zed\Engines\Database\Tables;

use InvalidArgumentException;

trait Cerbere {

    ///
    /// Constants
    ///

    /**
     * Determines if localhost calls could be passed.
     *
     * If true, any call from localhost is valid.
     * Otherwise, normal security rules are applied.
     */
    public const ALLOW_LOCALHOST = false;

    /**
     * Determines if error should be printed.
     *
     * If true, the error will be printed according the FORMAT_ERROR setting.
     * Otherwise, a blank page will be served.
     */
    public const OUTPUT_ERROR = true;

    /**
     * Determines if the error must be formatted.
     *
     * If true, any error will be sent as API output.
     * Otherwise, it will be printed as is.
     *
     * Ignored if OUTPUT_ERROR is set to false.
     */
    public const FORMAT_ERROR = false;

    ///
    /// Traits requirements
    ///

    public abstract function die (string $message) : never;

    public abstract function getDatabase () : DatabaseEngine;

    ///
    /// API security methods
    ///

    /**
     * Checks if credentials are okay and exits if not
     *
     * If the credentials aren't valid, it will print an error message if
     * OUTPUT_ERROR is defined and true.
     *
     * This error message will be formatted through the api_output function if
     * FORMAT_ERROR is defined and true ; otherwise, it will be print as is.
     *
     * To help debug, you can also define ALLOW_LOCALHOST. If this constant is
     * rue, any call from localhost will be accepted, without checking the key.
     *
     * @see die
     */
    public function guard () : void {
        //If ALLOW_LOCALHOST is true, we allow 127.0.0.1 queries
        //If you use one of your local IP in your webserver vhost like 10.0.0.3
        //it could be easier to create yourself a test key
        if (self::ALLOW_LOCALHOST && Request::isFromLocalHost()) {
            return;
        }

        $key = $_REQUEST['key'] ?? "";
        $this->validateApiKey($key);     // never return when key is invalid
        $this->increaseApiKeyHit($key);
    }

    private function validateApiKey($key): void {
        //No key, no authentication
        if ($key === "") {
            $this->die('You must add credentials to your request.');
        }

        if (!UUID::isUUID($key)) {
            $this->die("The key format is invalid.");
        }

        //Authenticates user
        $db = $this->getDatabase();
        $sql = "SELECT key_active FROM " . Tables::API_KEYS .
            " WHERE key_guid like '$key'";

        $result = $db->query($sql);
        if (!$result) {
            $this->die("Can't find API key in database.");
        }

        $row = $db->fetchRow($result);
        if (!$row) {
            $this->die("Key doesn't exist.");
        }

        if (!$row['key_active']) {
            $this->die("Key disabled.");
        }
    }

    public function increaseApiKeyHit (string $key) : void {
        if (!UUID::isUUID($key)) {
            throw new InvalidArgumentException("The key must be an UUID.");
        }

        $db = $this->getDatabase();

        $sql = "UPDATE " . Tables::API_KEYS . " SET key_hits = key_hits + 1" .
            " WHERE key_guid like '$key'";
        if (!$db->query($sql)) {
            $this->die("Can't record API call to database.");
        }
    }

}
