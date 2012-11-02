<?php
class linkontrol {
	const WEBPAGE = 0;
	const PERSON = 1;
	const LOCATION = 2;
	const VIDEO = 3;
	const PICTURE = 4;
	const TEXT = 5;
	const AUDIO = 6;
	const WEBCONTENT = 7;

	function runSqlMulti($SQL) {
		global $db;
		return $db->run($SQL);
	}

	function runSql($SQL) {
		$arr = $this->runSqlMulti($SQL);
		return $arr[0];
	}

	function isMovieLink($host) {
		return array_key_exists($host, array('youtube.com', 'vimeo.com', 'youtu.be'));
	}

	function urlToHost($url) {
		$host = "";
		if (preg_match('@^(?:http://)?([^/]+)(.*)@i', $url, $matches)) {
			$host = $matches[1];
			if (preg_match('/[^.]+\.[^.]+$/', $host, $matches)) {
				$x = explode(".", $matches[0]);
				$host = $x[0];
			}
		}
		return $host;
	}

	function fetchTouchIcon($url) {
		$filename = "";
		$host = $this->urlToHost($url);
		if ($host == "") {
			return "";
		}
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$output = curl_exec($ch);
		curl_close($ch);

		preg_match_all('/<link rel="apple-touch-icon.*">/', $output, $match);
		foreach ($match as $val) {
        		//echo("yo:" . $val[0] . "\n");
        		if (preg_match('#http://.*png#', $val[0], $url)) {
                		$ch = curl_init($url[0]);
				$filename = $host . ".png";
                		$fp = fopen("Icons/" . $filename, "w");
                		curl_setopt($ch, CURLOPT_FILE, $fp);
                		curl_setopt($ch, CURLOPT_HEADER, 0);
                		$output = curl_exec($ch);
                		curl_close($ch);
				return $filename;
			}
		}
		return $filename;
        }

	function getTimeFeeds($movieid, $sort = "asc") {
		$movieid = intval($movieid);
		$sql = "SELECT timefeed.*, linktype.name as linktypename " .
			"FROM linkontrol.timefeed, linkontrol.linktype " .
			"WHERE timefeed.linktypeid = linktype.linktypeid " .
			"AND movieid = $movieid " .
			"AND deleted = 0 ";
		if ($sort == "desc") {
			$sql .= "ORDER BY start DESC LIMIT 1000";
		}
		else {
			$sql .= "ORDER BY start ASC LIMIT 1000";
		}
		//print_r($sql);
		//die();
		return $this->runSqlMulti($sql);
	}

	function getTimeFeed($timefeedid) {
		$timefeedid = intval($timefeedid);
		return $this->runSql("SELECT timefeed.*, movie.userid FROM linkontrol.timefeed, linkontrol.movie " .
				"WHERE movie.movieid = timefeed.movieid " .
				"AND timefeedid = $timefeedid");
	}

	function deleteTimeFeed($timefeedid) {
		$timefeedid = intval($timefeedid);
		return $this->runSql("UPDATE linkontrol.timefeed SET deleted = 1 WHERE timefeedid = $timefeedid");
	}

	function addTimeFeed($movieid, $start, $end, $title, $img, $body, $href, $linktypeid) {
                $this->runSql("INSERT INTO linkontrol.timefeed(movieid, start, end, title, img, body, href, linktypeid) " .
				"VALUES ($movieid, $start, $end, '$title', '$img', '$body', '$href', $linktypeid)");
		return mysql_insert_id();
        }

	function updateTimeFeed($timefeedid, $start, $end, $title, $img, $body, $href, $linktypeid) {
                $this->runSql("UPDATE linkontrol.timefeed " . 
				"SET start = $start, end = $end, title = '$title', img = '$img', " .
				"body = '$body', href = '$href', linktypeid = $linktypeid " .
				"WHERE timefeedid = $timefeedid");
		return mysql_affected_rows();
	}

	function addMovie($userid, $name, $href) {
                $this->runSql("INSERT INTO linkontrol.movie(userid, name, href) " .
				"VALUES ($userid, '$name', '$href')");
		return mysql_insert_id();
        }

	function updateMovie($movieid, $name, $href) {
		$movieid = intval($movieid);
                $this->runSql("UPDATE linkontrol.movie SET name = '$name', href = '$href' WHERE movieid = $movieid");
		return mysql_affected_rows();
        }

	function deleteMovie($movieid) {
		$movieid = intval($movieid);
		return $this->runSql("UPDATE linkontrol.movie SET deleted = 1 WHERE movieid = $movieid");
	}

	function getMovies() {
		return $this->runSqlMulti("SELECT movie.*, users.username FROM linkontrol.movie, linkontrol.users " .
					"WHERE deleted = 0 " .
					"AND movie.userid = users.userid " .
					"ORDER BY movieid DESC LIMIT 100"); 
        }

	function getUserMovies($userid) {
		$userid = intval($userid);
		return $this->runSqlMulti("SELECT movie.*, users.username FROM linkontrol.movie, linkontrol.users " .
					"WHERE deleted = 0 " .
					"AND movie.userid = $userid " .
					"AND movie.userid = users.userid " .
					"ORDER BY movieid DESC LIMIT 100"); 
        }

	function movieToArray($val) {
		$img = "something.png";
		return array('movieid' => $val['movieid'], 'name' => $val['name'], 'img' => $img, 'url' => $val['href'], 'userid' => $val['userid']);
	}

	function getMovie($movieid) {
		$movieid = intval($movieid);
		return $this->runSql("SELECT * FROM linkontrol.movie WHERE movieid = $movieid LIMIT 1"); 
        }
	
	function createSession($movieid) {
		$movieid = intval($movieid);
		$sessionkey = substr(base_convert(md5($movieid + time()), 10, 36), 1, 5);
                $this->runSql("INSERT INTO linkontrol.session(movieid, sessionkey) " .
				"VALUES ($movieid, '$sessionkey')");
		return $sessionkey;
	}

	function reuseSession($sessionkey, $movieid) {
		$movieid = intval($movieid);
                $this->runSql("INSERT INTO linkontrol.session(movieid, sessionkey) " .
				"VALUES ($movieid, '$sessionkey') " . 
				"ON DUPLICATE KEY UPDATE movieid = $movieid, sessionkey = '$sessionkey'");
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
			$val['title'] . "</td><td>" . 
			$val['body'] . "</td><td>" . 
			$val['img'] . "</td><td>" . 
			$val['href'] . "</td><td>" . 
			"<a href=\"?do=delete_time_feed&timefeedid=" . $val['timefeedid'] . "&movieid=" . $val['movieid'] . "\">Delete</a></td><td>" .
			"<a href=\"?do=get_time_feed&timefeedid=" . $val['timefeedid'] . "&movieid=" . $val['movieid'] . "\">View</a></td><td>" .
			"</td></tr>\n";
	}

	function getLinkTypeSelectHtml($linktypeid) {
		$arr = $this->runSqlMulti("SELECT * FROM linkontrol.linktype");
		$html = "<SELECT name=linktypeid>\n";
		if (isset($arr)) {
			foreach ($arr as $key => $val) {
				if ($linktypeid == $val['linktypeid']) {
					$html .= '<option value="' . $val['linktypeid'] . '" SELECTED>' . $val['name'] . '</option>' . "\n";
				}
				else {
					$html .= '<option value=' . $val['linktypeid'] . '>' . $val['name'] . '</option>' . "\n";
				}
			}
		}
		$html .= "</SELECT>\n";
		return $html;
	}

	function timeFeedToHtmlForm($index, $val) {
		$html = '<form method=post action="?do=update_time_feed&movieid=' . $val['movieid'] . '">' . 
			'<input type=hidden name=feedid value=' . $val['timefeedid'] . '>' .
			'<tr>' .
			'<td>' . $index . '</td>' . 
			'<td><input name=start size=4 value="' . $val['start'] . '"></td>' . 
			'<td><input name=title size=35 value="' . $val['title'] . '"></td>' . 
			'<td><input name=body size=35 value="' . $val['body'] . '"></td>' . 
			'<td><input name=href size=35 value="' . $val['href'] . '"></td>' . 
			'<td><input name=img size=10 value="' . $val['img'] . '"></td>' . 
			'<td>';
			$html .= $this->getLinkTypeSelectHtml($val['linktypeid']);

			$html .=
			'<td>' .
			'<td><input type=submit value="Save">' .
			'</form>' .
			"<td><a href=\"?do=delete_time_feed&timefeedid=" . $val['timefeedid'] . "&movieid=" . $val['movieid'] . "\">Delete</a></td>" .
			"</tr>\n";
			return $html;
	}

	function timeFeedToPopcorn($val) {
		// if end: 0, it will not show
		return "popcorn.timefeed({\n" .
			"start: " . $val['start'] . ",\n" . 
			//"end:" . $val['end'] . ",\n" .
			"target: '#feeddiv',\n" .
			"body: '" . $val['title'] . "',\n" .
			"img: 'Icons/" . $val['img'] . "',\n" .
			"href: '" . $val['href'] . "'\n" .
		"});\n";
	}
	
	function timeFeedToLine($val) {
		$linktypeid = $val['linktypeid'];
		$timefeedid = $val['timefeedid'];
		$start = $val['start'];
		$img = "Icons/" . $val['img'];
		$href = $val['href'];
		$title = $val['title'];
		if ($val['body'] != "") {
			$body = $val['body'];
		}
		else {
			$body = $val['href'];
		}
		$length = 170.0;
		$percent = ($start / $length) * 100.0; 
		return "<a title='$title - $body' class='feed-event' style='left: " . $percent . "%;'></a>\n";
	}

	function timeFeedToJson($val) {
		$linktypeid = $val['linktypeid'];
		$timefeedid = $val['timefeedid'];
		$start = $val['start'];
		$img = "Icons/" . $val['img'];
		$href = $val['href'];
		$title = $val['title'];
		if ($val['body'] != "") {
			$body = $val['body'];
		}
		else {
			$body = $val['href'];
		}
		$length = 170.0;
		$percent = ($start / $length) * 100.0; 
		$arr = array('title' => $title, 'body' => $body, 'img' => $img, 'url' => $href, 'percent' => $percent, 'start' => $start);
		return json_encode($arr);
		//return '{"title":"' . $title . '";"body":"' . $body . '";"percent:"' . $percent. "},\n"; 
	}

	function timeFeedToArray($val) {
		$linktypeid = $val['linktypeid'];
		$timefeedid = $val['timefeedid'];
		$start = $val['start'];
		$img = $val['img'];
		$images = array();
		$images[0] = $val['img'];
		if ($val['img'] != "ll.png") {
			$images[1] = "ll.png"; 
		}
		else {
			$images[1] = "";
		}
		$href = $val['href'];
		$title = $val['title'];
		if ($val['body'] != "") {
			$body = $val['body'];
		}
		else {
			$body = $val['href'];
		}
		$length = 170.0;
		$percent = ($start / $length) * 100.0; 
                if (preg_match('@^(?:http://)?([^/]+)(.*)@i', $href, $matches)) {
                        $host = $matches[1];
                        $name = $matches[2];
                        $body = "<strong>" . $host . "</strong>" . $name;
                }
		return array('title' => $title, 'body' => $body, 'img' => $img, 'url' => $href, 'percent' => $percent, 'start' => $start, 'linktype' => $linktypeid, 'feedid' => $timefeedid, 'images' => $images, 'deleted' => 0);
	}

	function timeFeedToList($val) {
		$linktypeid = $val['linktypeid'];
		$timefeedid = $val['timefeedid'];
		$start = $val['start'];
		$img = "Icons/" . $val['img'];
		$href = $val['href'];
		$title = $val['title'];
		if ($val['body'] != "") {
			$body = $val['body'];
		}
		else {
			$body = $val['href'];
		}
		if (preg_match('@^(?:http://)?([^/]+)(.*)@i', $href, $matches)) {
                	$host = $matches[1]; 
        		$name = $matches[2]; 
			$body = "<strong>" . $host . "</strong>" . $name;
        	}

		$height = 80;
		$width = $height;
		if ($linktypeid == VIDEO) { // video
				/*
                                return "<li style='display: none' start='$start'>" .
					'<div data-role="collapsible" data-theme="a">' .
                                        "<h3>$title</h3>" .
                                        "<iframe width='288' height='200' src='$href' frameborder='0' allowfullscreen></iframe>" .
                                        "</div>" .
                                "</li>\n";
				*/
                                return "<li style='display: none; padding 0px 0px' start='$start'>" .
                                        "<iframe width='288' height='200' src='$href' frameborder='0' allowfullscreen></iframe>" .
					"</li>\n";
		}
		else if ($linktypeid == TEXT) { // Text 
			return "<li style='display: none' start='$start' data-icon='gear'><div id='fade'></div><img width='$width' height='$height' src='$img' /><h3>$title</h3><p>$body</p></li>\n";
		}
		else if ($linktypeid == WEBCONTENT) {
			return "<li style='display: none' start='$start' data-icon='gear'><div id='fade'></div><a href='#page_$timefeedid'><img width='$width' height='$height' src='$img' /><h3>$title</h3><p>$body</p></a></li>\n";
		}
		else {
			return "<li style='display: none' start='" . $val['start'] . "' data-icon='gear'>\n" .
			'<div id="fade"></div>' . "\n" .
			'<a href="' . $val['href'] . '">' . "\n" .
			'<img width="' . $height . '" height="' . $height . '" src="Icons/' . $val['img'] . '" />' . "\n" . 
			"<h3>" . $title . "</h3>\n" .
			"<p>" . $body . "</p>\n" .
			"</a>" .
			"</li>\n";
		}
	}

	function getNavigationMenu() {
		$html = <<<EOF
		<table>
		<tr>
		<td><a href="movies.php"> Movies </a></td>
		<td><a href="remote.php"> Remote </a></td>
		<td><a href="testapi.php"> Test API </a></td>
		<tr>
		</table>
EOF;
		return $html;

	}	

	function getLoginMenu() {
		return '<div><a href="login.php" class="sign-in" id="login-link">Log In</a> | <a href="register.php" class="sign-in" id="signup-link">Sign Up</a></div>' . "\n";
	}

	function getNavigationMenu2() {
		$html = <<<EOF
		<div id="small_menu">
		<ul>
		<li><a style="padding: 0pt 9px;" href="index.php"> Linkontrol </a></li>
		<li><a style="padding: 0pt 9px;" href="movies.php"> Movies </a></li>
		<li><a style="padding: 0pt 9px;" href="remote.php"> Remote </a></li>
</ul>
EOF;
		return $html;
	}
}
?>
