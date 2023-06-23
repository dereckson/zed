<?php

namespace Zed\Tests\Engines\API\Format;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Zed\Engines\API\Format\ValueRepresentation;

class ValueRepresentationTest extends TestCase {

    public static function provideValuesAndRepresentations () : iterable {
        // Scalars
        yield [null, ValueRepresentation::Scalar];
        yield [true, ValueRepresentation::Scalar];
        yield [false, ValueRepresentation::Scalar];
        yield [0, ValueRepresentation::Scalar];
        yield [1, ValueRepresentation::Scalar];
        yield [1.1, ValueRepresentation::Scalar];
        yield ["", ValueRepresentation::Scalar];
        yield ["foo", ValueRepresentation::Scalar];

        // Arrays
        yield [[], ValueRepresentation::List];
        yield [[1, 2, 3], ValueRepresentation::List];
        yield [
            [
                "foo" => "bar",
            ],
            ValueRepresentation::Object
        ];

        // Objects
        yield [new \stdClass, ValueRepresentation::Object];
        yield [new \DateTimeImmutable, ValueRepresentation::Object];

    }

    #[DataProvider('provideValuesAndRepresentations')]
    public function testFrom ($value, $representation) {
        $this->assertEquals(
            $representation,
            ValueRepresentation::from($value)
        );
    }
}
