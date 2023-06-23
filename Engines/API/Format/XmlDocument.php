<?php
declare(strict_types=1);

namespace Zed\Engines\API\Format;

use Keruald\OmniTools\Reflection\CodeClass;

use DOMDocument;
use RuntimeException;
use SimpleXMLElement;

class XmlDocument {

    ///
    /// Constants
    ///

    const IGNORED_KEYS = [
        "lastError",
    ];

    ///
    /// Properties
    ///

    private SimpleXMLElement $output;

    ///
    /// Constructors
    ///

    public function __construct (
        private readonly mixed  $data,
        private readonly string $rootNodeName = 'data',
        private readonly string $dataNodeName = 'item',
        private readonly string $unknownNodeName = 'unknownNode',
    ) {
    }

    public static function toXml(
        mixed $data,
        string $rootNodeName = 'data',
        string $dataNodeName = 'item',
        string $unknownNodeName = 'unknownNode'
    ) : string {
        if (!is_array($data) && !is_object($data)) {
            // Straightforward case
            $data = StringView::getScalarElement($data);
            return '<?xml version="1.0" encoding="utf-8"?>'
                . "\n<$rootNodeName>$data</$rootNodeName>\n";
        }

        $document = new XmlDocument(
            $data,
            $rootNodeName,
            $dataNodeName,
            $unknownNodeName,
        );

        return $document->build();
    }

    ///
    /// Document builder
    ///

    public function build () : string {
        $this->output = simplexml_load_string(
            "<?xml version='1.0' encoding='utf-8'?><$this->rootNodeName />"
        );

        $this->buildElement(
            $this->data,
            $this->output,
            $this->dataNodeName,
            true,
        );

        return $this->getXML();
    }

    private function buildElement (
        mixed $data,
        SimpleXMLElement $parent,
        string $nodeName = null,
        bool $isRootElement = false,
    ): void {
        if ($nodeName === null) {
            $nodeName = $this->unknownNodeName;
        }

        // If we're at top level, we want to add properties directly
        // to the root element, and not create <item></item> enclosure.
        $get_element = fn() => match ($isRootElement) {
            true => $parent,
            false => $parent->addChild($nodeName),
        };

        switch (ValueRepresentation::from($data)) {
            case ValueRepresentation::List:
                $element = $get_element();
                foreach ($data as $value) {
                    $key = match ($isRootElement) {
                        true => $nodeName,
                        false => $this->buildVectorKey($value),
                    };

                    $this->buildElement($value, $element, $key);
                }
                break;

            case ValueRepresentation::Object:
                $element = $get_element();
                foreach ($data as $key => $value) {
                    if (self::isIgnoredProperty($key, $value)) {
                        continue;
                    }

                    $this->buildElement($value, $element, $key);
                }
                break;

            case ValueRepresentation::Scalar:
                $value = StringView::getScalarElement($data);

                if ($isRootElement) {
                    $parent[0] = $value;
                } else {
                    $parent->addChild($nodeName, $value);
                }
                break;
        }
    }

    private static function isIgnoredProperty (
        string $key, mixed $value
    ) : bool {
        return $value === null || in_array($key, self::IGNORED_KEYS);
    }

    ///
    /// Document formatter
    ///

    public function getXML (): string {
        $dom = new DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($this->output->asXML());

        $xml = $dom->saveXML();
        return match ($xml) {
            false => throw new RuntimeException("Can't parse XML"),
            default => $xml,
        };
    }

    private function buildVectorKey (mixed $value) : string {
        // If $value is an object, we can derive the tag name
        // from the object's class name.
        if (is_object($value)) {
            try {
                $key = CodeClass::from($value)->getShortClassName();
                return strtolower($key);
            } catch (\ReflectionException $e) {
            }
        }

        return $this->unknownNodeName;
    }

}
