<?php
class linkontrol {
	function runSqlMulti($SQL) {
		global $db;
		return $db->run($SQL);
	}

	function runSql($SQL) {
		$arr = $this->runSqlMulti($SQL);
		return $arr[0];
	}

	function getMod($modid) {
		$modid = intval($modid);
		return $this->runSql("SELECT data FROM minibash.mod WHERE modid = $modid");
	}

	function addTimeFeed($movieid, $userid, $start, $end, $title, $img, $body, $href) {
                $this->runSql("INSERT INTO linkontrol.timefeed(movieid, userid, start, end, title, img, body, href) " .
				"VALUES ($movieid, $userid, $start, $end, '$img', '$title', '$body', '$href')");
		return mysql_insert_id();
        }
}
?>
