<?php
declare(strict_types=1);

namespace Zed\Tests\Engines\API\Format;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Zed\Engines\API\Format\StringView;

use stdClass;

class StringViewTest extends TestCase {

    ///
    /// Tests
    ///

    #[DataProvider('provideScalarViews')]
    public function testToString($data, $expected) : void {
        $this->assertEquals($expected, StringView::toString($data));

    }


    #[DataProvider('provideScalarElements')]
    public function testGetScalarElement($data, $expected) : void {
        $this->assertEquals($expected, StringView::getScalarElement($data));
    }

    ///
    /// Data providers
    ///

    public static function provideScalarViews () : iterable {
        // Lists
        yield [[], "[]"];
        yield [[1, 2, 3], "[1, 2, 3]"];
        yield [["foo", "bar"], "[foo, bar]"];
        yield [[1, 2, 3, "foo", "bar"], "[1, 2, 3, foo, bar]"];

        // Objects
        $shipA = new stdClass;
        $shipA->name = "Demios";
        $shipA->id = 400;
        yield [$shipA, "{name: Demios, id: 400}"];

        $shipB = new stdClass;
        $shipB->name = "Demios";
        $shipB->id = 400;
        $shipB->manifest = [12, 50, 97];
        yield [$shipB, "{name: Demios, id: 400, manifest: [12, 50, 97]}"];

        // List of objects
        yield [
            [$shipA, $shipB],
            "[{name: Demios, id: 400}, {name: Demios, id: 400, manifest: [12, 50, 97]}]",
        ];

        // Scalar elements should be rendered as is
        foreach (self::provideScalarElements() as $testParameters) {
            yield $testParameters;
        }
    }

    public static function provideScalarElements () : iterable {
        yield [true, "true"];
        yield [false, "false"];

        yield [null, ""];

        yield [42, "42"];
        yield [42.5, "42.5"];

        yield ["", ""];
        yield ["foo", "foo"];
    }

}
