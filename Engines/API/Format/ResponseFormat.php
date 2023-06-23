<?php
declare(strict_types=1);

namespace Zed\Engines\API\Format;

enum ResponseFormat : string {
    /**
     * Information about a variable in a way that's readable by humans
     */
    case Preview = "preview";

    /**
     * PHP serialization
     */
    case PHP = "php";

    /**
     * JSON payload
     */
    case JSON = "json";

    /**
     * XML payload
     */
    case XML = "xml";

    /**
     * A text representation.
     *
     * Useful only for scalar information.
     */
    case String = "string";
}
