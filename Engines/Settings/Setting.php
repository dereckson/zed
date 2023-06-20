<?php

/**
 * Settings: an individual setting class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, 2023, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  Settings
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010, 2023 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

namespace Zed\Engines\Settings;

use SimpleXMLElement;

/**
 * Setting class
 *
 * This class maps the <setting> XML block, from our settings XML format
 */
class Setting {

    /**
     * @var string The setting unique identifier
     */
    public string $id;

    ///
    /// Rendering settings
    /// How the setting should be offered in presentation layer?
    ///

    /**
     * @var string The setting unique key for rendering layer
     *
     * This identified is used to populate `id` and `name` attributes of
     * the form control. They also allow to find language label to print
     * the name of the setting in HTML form.
     */
    public string $key = "";

    /**
     * @var string The kind of field to use (e.g. "text", "validationText")
     */
    public string $field = "";

    /**
     * @var string If set, a regexp to validate the setting format
     */
    public string $regExp = "";

    /**
     * A list of choices. This list will be offered as a dropdown menu values.
     *
     * @var string[]
     */
    public array $choices = [];

    ///
    /// Mapping setting <> object property
    /// What those settings control?
    ///

    /**
     * @var string The name of the object this property belongs to.
     *
     * This is mainly used for get/set variables.
     *
     * @todo Create a targets property in Settings,
     *       then use $settings->targets[$this->object]
     */
    public string $object = "";

    /**
     * @var string The name of the property in the object
     */
    private string $property = "";

    /**
     * @var string The name of the method to call in the object
     *             when the setting is set.
     *
     * When left blank, it uses $handler, or if blank too, $property.
     */
    private string $method = "";

    /**
     * @var string[] The custom PHP code to run to get or set the property.
     *
     * Array valid keys are "get" and "set". It is acceptable to only use one,
     * or to use both.
     *
     * This requires the use of `eval` and makes methods usage difficult to
     * track. As such, it is now recommended to add a method to your model
     * object, and write that method name in $method.
     *
     * @deprecated Create a method to your target object and use $this->method
     */
    private array $handler = [];

    ///
    /// Error management
    ///

    /**
     * @var string If set, contains an error message
     */
    public string $lastError = "";

    ///
    /// Constructor
    ///

    public function __construct (string $id) {
        $this->id = $id;
    }

    ///
    /// Get and set property value
    ///

    function get () : string {
        // Case 1 — Evaluates custom handler
        if (array_key_exists('get', $this->handler)) {
            return eval($this->handler['get']);
        }

        // Case 2 — Gets object property
        if ($this->hasKnownObjectProperty()) {
            return $this->getObjectProperty();
        }

        // Passwords can be left blank
        if ($this->field === "password") {
            //Okay not to have a value for password fields
            return "";
        }

        // At this point, there is a configuration error in <setting> block.
        throw new SettingException(<<<EOF
Setting $this->key haven't any get indication.

Please set <object> and <property> tags, and <method> as needed.
EOF
        );
    }

    function set(string $value) : bool {
        // Validates data
        if (!$this->hasValidFormat($value)) {
            $this->lastError = "Invalid format for $this->key setting";
            return false;
        }

        // Tries to set value

        // Case 1 — uses custom handler code [deprecated]
        if (array_key_exists('set', $this->handler)) {
            return eval($this->handler['set']);
        }

        if ($this->object !== "") {
            // Case 2 — calls a specific method
            if ($this->method !== "") {
                return $this->callSetMethod($value);
            }

            // Case 3 — sets directly the property
            if ($this->property !== "") {
                $this->setObjectProperty($value);
                return true;
            }
        }

        // At this point, there is a configuration error in <setting> block.
        throw new SettingException(<<<EOF
Setting $this->key haven't any set indication.

Please set <object>, <property>, and, if needed, <method>.
EOF
);
    }

    private function hasValidFormat(string $value) : bool {
        return $this->regExp === "" || preg_match("/^" . $this->regExp . '$/', $value);
    }

    private function hasKnownObjectProperty () : bool {
        return $this->object !== "" && $this->property !== "";
    }

    ///
    /// Helper methods to interact with underlying object
    ///

    /**
     * @todo Consume $this->object from the engine targets, not from the globals
     */
    private function getUnderlyingObject() : object {
        if ($this->object === "") {
            throw new SettingException(
                "Underlying object isn't specified. Set <object> tag."
            );
        }

        return $GLOBALS[$this->object];
    }

    private function getObjectProperty () : string {
        $object = $this->getUnderlyingObject();
        $property = $this->property;

        return $object->$property;
    }

    private function setObjectProperty (string $value) : void {
        $object = $this->getUnderlyingObject();
        $property = $this->property;

        $object->$property = $value;
    }

    private function callSetMethod (string $value) : bool {
        $object = $this->getUnderlyingObject();

        return call_user_func([$object, $this->method], $value);
    }

    ///
    /// XML deserialization
    ///

    /**
     * Initializes a new instance of Setting class from a XML element
     */
    static function fromXml(SimpleXMLElement $xml) : self {
        //Reads attributes
        $id = '';
        foreach ($xml->attributes() as $key => $value) {
            switch ($key) {
                case 'id':
                    $id = (string)$value;
                    break;

                default:
                    message_die(GENERAL_ERROR, "Unknown attribute: $key = \"$value\"", "Settings error");
            }
        }

        //id attribute is mandatory
        if (!$id) {
            message_die(GENERAL_ERROR, "Setting without id. Please add id='' in <setting> tag", "Settings error");
        }

        //Initializes new Setting instance
        $setting = new Setting($id);

        //Parses simple <tag>value</tag>
        $properties = ['key', 'field', 'object', 'property', 'method', 'regExp'];
        foreach ($properties as $property) {
            if ($xml->$property) {
                $setting->$property = (string)$xml->$property;
            }
        }

        //Parses <handler>
        $setting->handler = [];
        if ($xml->handler) {
            trigger_error("The setting $id still uses a handler. Move this code to the related object and switch here to method.", E_USER_DEPRECATED);
            if ($xml->handler->get) {
                $setting->handler['get'] = (string)$xml->handler->get;
            }
            if ($xml->handler->set) {
                $setting->handler['set'] = (string)$xml->handler->set;
            }
        }

        //Parses <choices>
        if ($xml->choices) {
            foreach ($xml->choices->choice as $choiceXml) {
                $setting->choices[(string)$choiceXml->key] = (string)$choiceXml->value;
            }
        }

        return $setting;
    }
}
