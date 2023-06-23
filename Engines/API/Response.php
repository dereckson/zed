<?php
declare(strict_types=1);

namespace Zed\Engines\API;

use Keruald\Database\DatabaseEngine;
use Zed\Engines\API\Format\ResponseFormat;
use Zed\Engines\API\Format\StringView;
use Zed\Engines\API\Format\XmlDocument;
use Zed\Engines\Database\WithDatabase;

class Response {

    use WithDatabase;
    use Cerbere;

    ///
    /// Properties
    ///

    public mixed $reply = "null";

    private string $xmlRoot = "data";

    private string $xmlChildren = "item";

    private ResponseFormat $format = ResponseFormat::JSON;

    ///
    /// Constructors
    ///

    public function __construct (DatabaseEngine $db) {
        $this->setDatabase($db);
    }

    public static function withFormat (
        DatabaseEngine $db,
        string $format,
    ) : self {
        $response = new self($db);
        $response->format = ResponseFormat::from($format);

        return $response;
    }

    ///
    /// Output
    ///

    /**
     * Sets response content and prints it
     */
    public function output (
        mixed $reply = null,
        string $xmlRoot = "data",
        string $xmlChildren = "item",
    ) : void {
        $this->reply = $reply;
        $this->xmlRoot = $xmlRoot;
        $this->xmlChildren = $xmlChildren;

        $this->print();
    }

    public function print () : void {
        echo match ($this->format) {
            ResponseFormat::Preview => $this->getPreview(),

            ResponseFormat::PHP => serialize($this->reply),

            ResponseFormat::JSON => json_encode($this->reply),

            ResponseFormat::XML => XmlDocument::toXml(
                $this->reply,
                $this->xmlRoot,
                $this->xmlChildren
            ),

            ResponseFormat::String => StringView::toString($this->reply),
        };
    }

    private function getPreview () : string {
        return '<pre>' . print_r($this->reply, true) . '</pre>';
    }

    ///
    /// Error output
    ///

    /**
     * Prints a message in raw or API format, then exits.
     *
     * The error message will be formatted through api_output if the constant
     * FORMAT_ERROR is defined and true. Otherwise, it will be printed as is.
     */
    public function die (string $message) : never {
        if (self::OUTPUT_ERROR) {
            if (self::FORMAT_ERROR) {
                $this->output($message, "error");
            } else {
                echo $message;
            }
        }
        exit;
    }

}
