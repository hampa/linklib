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

	function getTimeFeed($movieid) {
		$movieid = intval($movieid);
		return $this->runSqlMulti("SELECT * FROM linkontrol.timefeed WHERE movieid = $movieid AND deleted = 0 ORDER BY start ASC LIMIT 1000");
	}

	function deleteTimeFeed($timefeedid) {
		$timefeedid = intval($timefeedid);
		return $this->runSql("UPDATE linkontrol.timefeed SET deleted = 1 WHERE timefeedid = $timefeedid");
	}

	function addTimeFeed($movieid, $userid, $start, $end, $title, $img, $body, $href) {
                $this->runSql("INSERT INTO linkontrol.timefeed(movieid, userid, start, end, title, img, body, href) " .
				"VALUES ($movieid, $userid, $start, $end, '$title', '$img', '$body', '$href')");
		return mysql_insert_id();
        }

	function timeFeedToHtml($val) {
		return "<tr><td>" . $val['timefeedid'] . "</td><td>" . 
			$val['start'] . ',' . $val['end'] . "</td><td>" . 
			$val['body'] . "</td><td>" . 
			$val['img'] . "</td><td>" . 
			$val['href'] . "</td><td>" . 
			"<a href=\"?do=delete_time_feed&timefeedid=" . $val['timefeedid'] . "&movieid=" . $val['movieid'] . "\">Delete</a></td></tr>\n";
	}

	function timeFeedToJson($val) {
		return "popcorn.timefeed({\n" .
			"start: " . $val['start'] . ",\n" . 
			"end:" . $val['end'] . ",\n" .
			"target: '#feeddiv',\n" .
			"body: '" . $val['body'] . "',\n" .
			"img: 'Icons/" . $val['img'] . "',\n" .
			"href: '" . $val['href'] . "',\n" .
		"});\n";
	}
}
?>
