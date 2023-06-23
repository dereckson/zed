<?php

namespace Zed\Tests\Engines\API\Format;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use stdClass;
use Zed\Engines\API\Format\XmlDocument;

class XmlDocumentTest extends TestCase {

    ///
    /// Data providers
    ///

    public static function provideXmlDocuments () : iterable {
        $dir = dirname(__DIR__, 3) . "/data/XmlDocument";

        yield [[1, 2, 3, 4, 5], "$dir/sequence.xml"];

        $ship = new stdClass;
        $ship->name = "Demios";
        $ship->id = 400;
        $ship->manifest = [12, 50, 97];
        yield [$ship , "$dir/ship.xml"];
    }

    ///
    /// Tests
    ///

    public function testToXmlWithScalar () {
        $expected = '<?xml version="1.0" encoding="utf-8"?><foo>666</foo>';
        $actual = XmlDocument::toXml(666, "foo");

        $this->assertEquals($expected, str_replace("\n", "", $actual));
    }

    #[DataProvider("provideXmlDocuments")]
    public function testXml ($data, $expectedPath) {
        $expected = file_get_contents($expectedPath);

        $this->assertEquals($expected, XmlDocument::toXml($data));
    }

}
