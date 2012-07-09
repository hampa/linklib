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

	function getTimeFeeds($movieid) {
		$movieid = intval($movieid);
		return $this->runSqlMulti("SELECT * FROM linkontrol.timefeed WHERE movieid = $movieid AND deleted = 0 ORDER BY start ASC LIMIT 1000");
	}

	function getTimeFeed($timefeedid) {
		$timefeedid = intval($timefeedid);
		return $this->runSql("SELECT * FROM linkontrol.timefeed WHERE timefeedid = $timefeedid");
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

	function updateTimeFeed($timefeedid, $userid, $start, $end, $title, $img, $body, $href) {
                $this->runSql("UPDATE linkontrol.timefeed SET " . 
				"userid = $userid, start = $start, end = $end, title = '$title', img = '$img', body = '$body', href = '$href' " .
				"WHERE timefeedid = $timefeedid");
		return mysql_affected_rows();
	}

	function addMovie($userid, $name, $href) {
                $this->runSql("INSERT INTO linkontrol.movie(userid, name, href) " .
				"VALUES (userid, '$name', '$href')");
		return mysql_insert_id();
        }

	function getMovies() {
		return $this->runSqlMulti("SELECT * FROM linkontrol.movie ORDER BY movieid DESC LIMIT 100"); 
        }
	
	function createSession($movieid) {
		$movieid = intval($movieid);
		$sessionkey = substr(base_convert(md5($movieid + time()), 10, 36), 1, 5);
                $this->runSql("INSERT INTO linkontrol.session(movieid, sessionkey) " .
				"VALUES ($movieid, '$sessionkey')");
		return $sessionkey;
	}

	function getSession($sessionkey) {
		return $this->runSql("SELECT session.movieid, movie.name as moviename " .
			"FROM linkontrol.session, linkontrol.movie " .
			"WHERE session.movieid = movie.movieid " .
			"AND sessionkey = '$sessionkey' LIMIT 1"); 
	}
	
	function timeFeedToHtml($val) {
		return "<tr><td>" . $val['timefeedid'] . "</td><td>" . 
			$val['start'] . ',' . $val['end'] . "</td><td>" . 
			$val['body'] . "</td><td>" . 
			$val['img'] . "</td><td>" . 
			$val['href'] . "</td><td>" . 
			"<a href=\"?do=delete_time_feed&timefeedid=" . $val['timefeedid'] . "&movieid=" . $val['movieid'] . "\">Delete</a></td><td>" .
			"<a href=\"?do=get_time_feed&timefeedid=" . $val['timefeedid'] . "&movieid=" . $val['movieid'] . "\">View</a></td><td>" .
			"</td></tr>\n";
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
