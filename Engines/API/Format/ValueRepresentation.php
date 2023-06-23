<?php
declare(strict_types=1);

namespace Zed\Engines\API\Format;

enum ValueRepresentation {

    ///
    /// Possible values
    ///

    /**
     * Representation as XML tags with generic key
     */
    case List;

    /**
     * Representation as XML tags matching keys
     */
    case Object;

    /**
     * Direct representation as string
     */
    case Scalar;

    ///
    /// Helper methods
    ///

    public static function from (mixed $value) : self {
        if (is_object($value)) {
            return self::Object;
        }

        if (is_array($value)) {
            if (array_is_list($value)) {
                return self::List;
            }

            return self::Object;
        }

        return self::Scalar;
    }
}
