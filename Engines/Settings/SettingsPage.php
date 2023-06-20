<?php

/**
 * Settings: a settings page class
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

use SimpleXMLElement;

/**
 * This class maps the page XML element, from our Settings XML schema
 *
 * <page id="account" title="Account">
 *     <setting ...>
 *          ...
 *     </setting>
 *     <setting ...>
 *          ...
 *     </setting>
 * <page>
 *
 * It provides method to print a form built from this page and to handle form.
 */
class SettingsPage {

    /**
     * The page ID
     *
     * This property maps the id attribute from the page XML tag.
     */
    public string $id;

    /**
     * The page's title
     *
     * This property maps the title attribute from the page XML tag.
     */
    public string $title;

    /**
     * The settings
     *
     * This property is an array of Setting items and maps the <setting> tags
     * @var Setting[]
     */
    public array $settings = [];

    /**
     * Initializes a new instance of SettingsPage class
     */
    function __construct(string $id) {
        $this->id = $id;
    }

    /**
     * Initializes a settings page from an SimpleXMLElement XML fragment
     */
    static function fromXml(SimpleXMLElement $xml) : SettingsPage {
        // Reads attributes
        $id = '';
        $title = '';
        foreach ($xml->attributes() as $key => $value) {
            switch ($key) {
                case 'title':
                    $title = (string)$value;
                    break;

                case 'id':
                    $id = (string)$value;
                    break;

                default:
                    message_die(GENERAL_ERROR, "Unknown attribute: $key = \"$value\"", "Settings error");
            }
        }

        // Ensure id attribute is defined
        if ($id === "") {
            message_die(GENERAL_ERROR, "Section without id. Please add id='' in <section> tag", "Story error");
        }

        //Initializes new SettingsPage instance
        $page = new SettingsPage($id);
        $page->title = $title;

        //Gets settings
        if ($xml->setting) {
            foreach ($xml->setting as $settingXml) {
                $setting = Setting::fromXml($settingXml);
                $page->settings[$setting->key] = $setting;
            }
        }

        return $page;
    }

    /**
     * Handles form reading $_POST array, set new settings values and saves.
     *
     * @param string[] $errors an array where the errors will be filled
     * @return boolean true if there isn't error ; otherwise, false.
     */
    function handleForm(array &$errors = []) : bool {
        $objects = [];
        $result = true;

        // Sets new settings values, and records objects to save
        foreach ($this->settings as $setting) {
            $value = $_POST[$setting->key] ?? "";

            if ($setting->field === "password" && $value === "") {
                // We don't erase passwords if not set
                continue;
            }

            // If the setting value is different of current one, we update it
            $currentValue = $setting->get();
            if ($setting->field === "checkbox" || $currentValue !== $value) {
                if (!$setting->set($value)) {
                    $errors[] = $setting->lastError ?? "An error have occurred in $setting->key field.";
                    $result = false;
                }

                if ($setting->object) {
                    $objects[] = $setting->object;
                }
            }
        }

        $this->saveObjects($objects);

        return $result;
    }

    /**
     * @param string[] $objects
     */
    private function saveObjects (array $objects) : void {
        $objects = array_unique($objects);

        foreach ($objects as $objectName) {
            $object = $this->getUnderlyingObject($objectName);
            if (method_exists($object, Settings::SETTINGS_SAVE_METHOD)) {
                call_user_func([$object, Settings::SETTINGS_SAVE_METHOD]);
            }
        }
    }

    private function getUnderlyingObject (string $object) : object {
        return $GLOBALS[$object];
    }

}
