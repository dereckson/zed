<?php

/**
 * Message class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2010-01-28 01:47    Autogenerated by Pluton Scaffolding
 *
 * @package     Zed
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

namespace Zed\Models\Messages;

use Keruald\Database\DatabaseEngine;
use Zed\Models\Base\Entity;
use Zed\Models\Objects\Perso;

/**
 * Message class
 *
 * This class maps the messages table.
 *
 * It also provides a static method to get perso's messages.
 */
class Message extends Entity {

    public $id;
    public $date;
    private $from;
    public $to;
    public $text;
    public $flag;

    public MessageSource $source;
    public ?Perso $perso;

    public string $lastError = "";

    /**
     * Initializes a new instance
     *
     * @param int $id the primary key
     */
    function __construct (DatabaseEngine $db, $id = null) {
        $this->setDatabase($db);

        if ($id) {
            $this->id = $id;
            $this->load_from_database();
        } else {
            $this->date = time();
            $this->flag = 0;        //unread
        }
    }

    /**
     * Loads the object Message (ie fill the properties) from the $_POST array
     */
    function load_from_form () {
        if (array_key_exists('date', $_POST)) {
            $this->date = $_POST['date'];
        }
        if (array_key_exists('from', $_POST)) {
            $this->from = $_POST['from'];
        }
        if (array_key_exists('to', $_POST)) {
            $this->to = $_POST['to'];
        }
        if (array_key_exists('text', $_POST)) {
            $this->text = $_POST['text'];
        }
        if (array_key_exists('flag', $_POST)) {
            $this->flag = $_POST['flag'];
        }
    }

    /**
     * Loads the object Message (ie fill the properties) from the database
     */
    function load_from_database (): bool {
        $db = $this->getDatabase();

        $sql = "SELECT * FROM messages WHERE message_id = '" . $this->id . "'";
        if (!($result = $db->query($sql))) {
            message_die(SQL_ERROR, "Unable to query messages", '', __LINE__, __FILE__, $sql);
        }
        if (!$row = $db->fetchRow($result)) {
            $this->lastError = "Message unknown: " . $this->id;
            return false;
        }
        $this->date = $row['message_date'];
        $this->from = (int)$row['message_from'];
        $this->to = $row['message_to'];
        $this->text = $row['message_text'];
        $this->flag = $row['message_flag'];

        $this->computeSource();

        return true;
    }

    /**
     * Saves to database
     */
    function save_to_database (): void {
        $db = $this->getDatabase();

        $id = $this->id ? "'" . $db->escape($this->id) . "'" : 'NULL';
        $date = $db->escape($this->date);
        $from = match ($this->source) {
            MessageSource::System => 0,
            MessageSource::Perso => $this->perso->id,
        };
        $to = $db->escape($this->to);
        $text = $db->escape($this->text);
        $flag = $db->escape($this->flag);

        //Updates or inserts
        $sql = "REPLACE INTO messages (`message_id`, `message_date`, `message_from`, `message_to`, `message_text`, `message_flag`) VALUES ($id, '$date', '$from', '$to', '$text', '$flag')";
        if (!$db->query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$id) {
            //Gets new record id value
            $this->id = $db->nextId();
        }
    }

    public function setAsSystem () {
        $this->source = MessageSource::System;
        $this->perso = null;
    }

    public function setFrom (Perso $perso) {
        $this->source = MessageSource::Perso;
        $this->perso = $perso;
    }

    private function computeSource () {
        $db = $this->getDatabase();

        $this->source = match ($this->from) {
            0 => MessageSource::System,
            default => MessageSource::Perso,
        };

        $this->perso = match ($this->from) {
            0 => null,
            default => Perso::get($db, $this->from),
        };
    }

    /**
     * Sends the message
     */
    function send (): void {
        $this->save_to_database();
        //TODO: triggers new message notifier
    }

    /**
     * Deletes the message
     */
    function delete (): void {
        //A message is deleted if its flag value is 2
        if ($this->flag != 2) {
            $this->flag = 2;
            $this->save_to_database();
        }
    }

    /**
     * Gets messages from the specified perso
     */
    static function get_messages (DatabaseEngine $db, Perso $perso, bool $mark_as_read = true, int &$countNewMessages = 0) {
        $ids = [];

        $sql = "SELECT message_id FROM " . TABLE_MESSAGES . " WHERE message_to = " . $db->escape($perso->id) . " AND message_flag < 2 ORDER BY message_id DESC";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Unable to get messages", '', __LINE__, __FILE__, $sql);
        }

        $messages = [];
        while ($row = $db->fetchRow($result)) {
            $message = new Message($db, $row["message_id"]);
            $messages[] = $message;
            $ids[] = $message->id;
            if ($message->flag == 0) {
                //New message
                $countNewMessages++;
            }
        }
        if ($mark_as_read && count($ids)) {
            $ids = join(', ', $ids);
            $sql = "UPDATE " . TABLE_MESSAGES . " SET message_flag = '1' WHERE message_id IN ($ids)";
            $db->query($sql);
        }
        return $messages;
    }

    public function isSelf (): bool {
        return match ($this->source) {
            MessageSource::System => false,
            MessageSource::Perso => $this->perso->id == $this->to,
        };
    }

}
