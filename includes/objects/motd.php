<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * MOTD class.
 *
 * 0.1	2010-02-03 21:11	Import from Azhàr
 *
 */

class MOTD {

	public $id;
	public $perso_id;
	public $text;
	public $date;
	
	function __construct ($id = '') {
		if ($id) {
			$this->id = $id;
			return $this->load_from_database();
		} else {
			$this->date = time();
			return true;
		}
	}
	
	//Loads the object MOTD (ie fill the properties) from the $_POST array
	function load_from_form () {
		if (array_key_exists('perso_id', $_POST)) $this->user_id = $_POST['user_id'];
		if (array_key_exists('text', $_POST)) $this->text = $_POST['text'];
		if (array_key_exists('date', $_POST)) $this->date = $_POST['date'];
	}
	
	//Loads the object MOTD (ie fill the properties) from the database
	function load_from_database () {
		global $db;
		$id = $db->sql_escape($this->id);
		$sql = "SELECT * FROM " . TABLE_MOTD . " WHERE motd_id = '" . $id . "'";
		if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Unable to query azhar_motd", '', __LINE__, __FILE__, $sql);
		if (!$row = $db->sql_fetchrow($result)) {
			$this->lastError = "MOTD unkwown: " . $this->id;
			return false;
		}
		$this->perso_id = $row['perso_id'];
		$this->text = $row['motd_text'];
		$this->date = $row['motd_date'];
		return true;
	}
	
	//Saves the object to the database
	function save_to_database () {
		global $db;
		$id = $this->id ? "'" . $db->sql_escape($this->id) . "'" : 'NULL';
		$perso_id = $db->sql_escape($this->perso_id);
		$text = $db->sql_escape($this->text);
		$date = $db->sql_escape($this->date);

        $sql = "REPLACE INTO " . TABLE_MOTD . " (`motd_id`, `perso_id`, `motd_text`, `motd_date`) VALUES ($id, '$perso_id', '$text', '$date')";
		if (!$db->sql_query($sql)) {
			message_die(SQL_ERROR, "Unable to save", '', __LINE__, __FILE__, $sql);
		}
	}
}
	
?>

