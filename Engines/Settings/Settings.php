<?php

/**
 * Settings
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  Settings
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

namespace Zed\Engines\Settings;

/**
 * Settings
 *
 * This class maps the Settings format (preferences.xml)
 *
 * It allows to generate settings web forms and handle replies, from a
 * XML document.
 */
class Settings {

    ///
    /// Constants
    ///

    /**
     * The method to call in your objects, to save data.
     *
     * @const string
     */
    const SETTINGS_SAVE_METHOD = "save_to_database";

    ///
    /// Properties
    ///

    public string $filePath;

    /**
     * A collection of SettingsPage items
     *
     * @var SettingsPage[]
     */
    public array $pages = [];

    /**
     * The targets are underlying objects manipulated by the settings
     *
     * @var object[]
     */
    public array $targets = [];

    ///
    /// Constructor
    ///

    /**
     * Initializes a new instance of Settings class
     */
    function __construct (string $xmlFilePath) {
        //Opens .xml
        if (!file_exists($xmlFilePath)) {
            throw new SettingException("$xmlFilePath not found.");
        }
        $this->filePath = $xmlFilePath;

        //Parses it
        $this->parse();
    }

    ///
    /// XML deserialization
    ///

    /**
     * Parses XML file
     */
    function parse () {
        //Parses it
        $xml = simplexml_load_file($this->filePath);
        foreach ($xml->page as $page) {
            //Gets page
            $page = SettingsPage::fromXml($page);

            //Adds to sections array
            $this->pages[$page->id] = $page;
        }
    }
}
