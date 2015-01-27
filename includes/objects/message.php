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

/**
 * Message class
 *
 * This class maps the messages table.
 *
 * It also provides a static method to get perso's messages.
 */
class Message {

    public $id;
    public $date;
    public $from;
    public $to;
    public $text;
    public $flag;

    /**
     * Initializes a new instance
     *
     * @param int $id the primary key
     */
    function __construct ($id = null) {
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
        if (array_key_exists('date', $_POST)) $this->date = $_POST['date'];
        if (array_key_exists('from', $_POST)) $this->from = $_POST['from'];
        if (array_key_exists('to', $_POST)) $this->to = $_POST['to'];
        if (array_key_exists('text', $_POST)) $this->text = $_POST['text'];
        if (array_key_exists('flag', $_POST)) $this->flag = $_POST['flag'];
    }

    /**
     * Loads the object Message (ie fill the properties) from the database
     */
    function load_from_database () {
        global $db;
        $sql = "SELECT * FROM messages WHERE message_id = '" . $this->id . "'";
        if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query messages", '', __LINE__, __FILE__, $sql);
        if (!$row = $db->sql_fetchrow($result)) {
            $this->lastError = "Message unkwown: " . $this->id;
            return false;
        }
        $this->date = $row['message_date'];
        $this->from = $row['message_from'];
        $this->to = $row['message_to'];
        $this->text = $row['message_text'];
        $this->flag = $row['message_flag'];
        return true;
    }

    /**
     * Saves to database
     */
    function save_to_database () {
        global $db;

        $id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
        $date = $db->sql_escape($this->date);
        $from = $db->sql_escape($this->from);
        $to = $db->sql_escape($this->to);
        $text = $db->sql_escape($this->text);
        $flag = $db->sql_escape($this->flag);

        //Updates or inserts
        $sql = "REPLACE INTO messages (`message_id`, `message_date`, `message_from`, `message_to`, `message_text`, `message_flag`) VALUES ($id, '$date', '$from', '$to', '$text', '$flag')";
        if (!$db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
        }

        if (!$id) {
            //Gets new record id value
            $this->id = $db->sql_nextid();
        }
    }

    /**
     * Sends the message
     */
    function send () {
        $this->save_to_database();
        //TODO: triggers new message notifier
    }

    /**
     * Deletes the message
     */
    function delete () {
        //A message is deleted if its flag value is 2
        if ($this->flag != 2) {
            $this->flag = 2;
            $this->save_to_database();
        }
    }

    /**
     * Gets messages from the specified perso
     */
    static function get_messages ($perso_id, $mark_as_read = true, &$countNewMessages = 0) {
        global $db;
        $sql = "SELECT message_id FROM " . TABLE_MESSAGES . " WHERE message_to = " . $db->sql_escape($perso_id) . " AND message_flag < 2 ORDER BY message_id DESC";
        if (!$result = $db->sql_query($sql)) {
            message_die(SQL_ERROR, "Unable to get messages", '', __LINE__, __FILE__, $sql);
        }
        while ($row = $db->sql_fetchrow($result)) {
            $message = new Message($row[0]);
            $messages[] = $message;
            $ids[] = $message->id;
            if ($message->flag == 0) {
                //New message
                $countNewMessages++;
            }
        }
        if ($mark_as_read && count($ids)) {
            $ids = join($ids, ', ');
            $sql = "UPDATE " . TABLE_MESSAGES . " SET message_flag = '1' WHERE message_id IN ($ids)";
            $db->sql_query($sql);
        }
        return $messages;
    }
}

?>
