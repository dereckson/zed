<?php
declare(strict_types=1);

namespace Zed\Engines\API\Format;

use Keruald\OmniTools\Collections\HashMap;
use Keruald\OmniTools\Collections\Vector;

use LogicException;

class StringView {
    public static function toString (mixed $data) : string {
        switch (ValueRepresentation::from($data)) {
            case ValueRepresentation::List:
                $response = Vector::from($data)
                    ->map(fn($item) => self::toString($item))
                    ->implode(", ");
                return "[$response]";

            case ValueRepresentation::Object:
                $response = HashMap::from($data)
                    ->mapToVector(
                        fn($key, $value) => "$key: " . self::toString($value)
                    )
                    ->implode(", ");
                return '{' . $response . '}';

            case ValueRepresentation::Scalar:
                return self::getScalarElement($data);
        }

        throw new LogicException("Unreachable code");
    }

    public static function getScalarElement (mixed $data) : string {
        if (is_bool($data)) {
            return $data ? 'true' : 'false';
        }

        return (string)$data;
    }
}
