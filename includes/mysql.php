<?php

/*
 * MySQL layer and helper class 
 *
 * @package Zed
 * @subpackage Pluton
 * @copyright Copyright (c) 2010, Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 */

if (!defined('SQL_LAYER')) {
	define('SQL_LAYER', 'mysql');
    
    class sql_db {
        private $id;
        
        function __construct($host = 'localhost', $username = 'root', $password = '' , $database = '') {
            $this->id = mysql_connect($host, $username, $password);
            if ($database != '') {
                mysql_select_db($database, $this->id);
            }
        }
        
        function sql_query ($query) {
            return mysql_query($query, $this->id);
        }
        
        function sql_fetchrow ($result) {
            return mysql_fetch_array($result);
        }
        
        function sql_error () {
            $error['code'] = mysql_errno($this->id);
            $error['message'] = mysql_error($this->id);
            return $error;
        }
        
        function sql_numrows ($result) {
            return mysql_num_rows($result);
        }
        
        function sql_nextid () {
            return mysql_insert_id($this->id);
        }
        
        /*
         * Express query method, returns an immediate and unique result
         *
         * @param string $query the query to execute
         * @param string $error_message the error message
         * @param boolean $return_as_string return result as string, and not as an array
         *
         * @return mixed the row or the scalar result
         */
        function sql_query_express ($query = '', $error_message = "Impossible d'exécuter cette requête.", $return_as_string = true) {
            if (!$query) {
                return '';
            } elseif (!$result = $this->sql_query($query)) {
                message_die(SQL_ERROR, $error_message, '', __LINE__, __FILE__, $query);
            } else {
                $row = $this->sql_fetchrow($result);
                return $return_as_string ? $row[0] : $row;                
            }
        }
        
        /*
         * Escapes a SQL expression
         *
         * @param string expression The expression to escape
         * @return string The escaped expression
         */
        function sql_escape ($expression) {
            return mysql_real_escape_string($expression);
        }
        
        function set_charset ($encoding) {
            mysql_set_charset('utf8', $this->id);
        }
    }
}



$db = new sql_db($Config['sql']['host'], $Config['sql']['username'], $Config['sql']['password'], $Config['sql']['database']);

unset($Config['sql']);

if ($db->lastError) {
	die($db->lastError);
}

//Sets SQL connexion in UTF8. PHP 5.2.3+
$db->set_charset('utf8');
?>