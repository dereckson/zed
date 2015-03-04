<?php

/**
 * MySQLi layer and helper class
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, 2014, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2014 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * SQL layer and helper class: MySQLi
 *
 * @package Keruald
 * @subpackage Keruald
 * @copyright Copyright (c) 2010, Sébastien Santoro aka Dereckson
 * @license Released under BSD license
 * @version 0.1
 */
class DatabaseMySQLi {
    /*
     * @var int the connection identifier
     */
    private $db;

    /**
     * Initializes a new instance of the database abstraction class, for MySQLi engine
     */
    function __construct($host = 'localhost', $username = '', $password = '', $database = '') {
        //Checks extension requirement
        if (!class_exists("mysqli")) {
            message_die(GENERAL_ERROR, "You've chosen to use a MySQLi database engine, but the MySQLi extension is missing.", "Setup issue");
        }

        //Connects to MySQL server
        $this->db = new mysqli($host, $username, $password) or $this->sql_die();

        //Selects database
        if ($database != '') {
            $this->db->select_db($database);
        }
    }

    /**
     * Outputs a can't connect to the SQL server message and exits.
     * It's called on connect failure
     */
    private function sql_die () {
        //You can custom here code when you can't connect to SQL server
        //e.g. in a demo or appliance context, include('start.html'); exit;
        die ("Can't connect to SQL server.");
    }

    /**
     * Sends a unique query to the database
     *
     * @return mixed if the query is successful, a mysqli_result instance ; otherwise, false
     */
    function sql_query ($query) {
        return $this->db->query($query);
    }

    /**
     * Fetches a row of result into an associative array
     *
     * @return array an associative array with columns names as keys and row values as values
     */
    function sql_fetchrow ($result) {
        return $result->fetch_array();
    }

    /**
     * Gets last SQL error information
     *
     * @return array an array with two keys, code and message, containing error information
     */
    function sql_error () {
        return [
            'code' => $this->db->errno,
            'message' => $this->db->error
        ];
    }

    /**
     * Gets the number of rows affected or returned by a query
     *
     * @return int the number of rows affected (delete/insert/update) or the number of rows in query result
     */
    function sql_numrows ($result) {
        return $result->num_rows;
    }

    /**
     * Gets the primary key value of the last query (works only in INSERT context)
     *
     * @return int  the primary key value
     */
    function sql_nextid () {
        return $this->db->insert_id;
    }

    /**
     * Express query method, returns an immediate and unique result
     *
     * @param string $query the query to execute
     * @param string $error_message the error message
     * @param boolean $return_as_string return result as string, and not as an array
     * @return mixed the row or the scalar result
     */
    function sql_query_express ($query = '', $error_message = "Impossible d'exécuter cette requête.", $return_as_string = true) {
        if ($query === '' || $query === false || $query === null) {
            //No query, no value
            return '';
        } elseif (!$result = $this->sql_query($query)) {
            //An error have occured
            message_die(SQL_ERROR, $error_message, '', '', '', $query);
        } else {
            //Fetches row
            $row = $this->sql_fetchrow($result);

            //If $return_as_string is true, returns first query item (scalar mode) ; otherwise, returns row
            return $return_as_string ? $row[0] : $row;
        }
    }

    /*
     * Escapes a SQL expression
     * @param string expression The expression to escape
     * @return string The escaped expression
     */
    function sql_escape ($expression) {
        return $this->db->real_escape_string($expression);
    }

    /**
     * Sets charset
     */
    function set_charset ($encoding) {
       $this->db->set_charset($encoding);
    }
    
    /**
     * Loads a database instance, connected and ready to process queries.
     */
    static function load () {
        global $Config;
        
        //Creates an instance of this database class with configuration values
        $db = new self(
            $Config['database']['host'],
            $Config['database']['username'],
            $Config['database']['password'],
            $Config['database']['database']
        );
        
        //Sets SQL connexion in UTF-8.
        $db->set_charset('utf8');
        
        return $db;
    }
}
