<?php
class db {
	var $db_link;

	function __construct() {
		$this->db_link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_NAME, $this->db_link);
	}

	function query($sql) {
		$start_time = microtime(true);
		$result = mysql_query($sql, $this->db_link);
		if (mysql_errno($this->db_link)) {
			if ($_COOKIE['developer']) {
				die('SQL: ' . $sql . '<br>' . mysql_error($this->db_link));
			}
			else {
#die('Error in SQL');
				die('SQL: ' . $sql . '<br>' . mysql_error($this->db_link));
			}
		}
		$exec_time = intval(1000 * (microtime(true) - $start_time));
		return $result;
	}

	function escape($str) {
		return mysql_real_escape_string($str);
	}

	function fetch($result) {
		if ($result !== true && $result !== false) {
			while ($row = mysql_fetch_assoc($result)) {
				$data[] = $row;
			}
		}
		return $data;
	}

	function run($sql) {
		return $this->fetch($this->query($sql));
	}

	function affectedRows() {
		return mysql_affected_rows($this->db_link);
	}

	function insertId() {
		return mysql_insert_id($this->db_link);
	}
}
?>
