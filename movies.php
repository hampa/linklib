<?php
require_once('linkontrol/template.php');
require_once('linkontrol/global.php');
require_once('linkontrol/access.php');
require_once('linkontrol/functions_linkontrol.php');

$linkontrol = new linkontrol();
$arr = $linkontrol->getMovies();
$id = $_SERVER['REMOTE_ADDR'];
if (isset($arr)) {
        foreach ($arr as $key => $val) {
                $movies_list .= '<tr>' .
		'<td>' . $val['name'] . '</td>' .
		'<td>' . $val['href'] . '</td>' .
		'<td>' . $val['username'] . '</td>' .
		'<td>' . 
		'<form>' . 
		"<input type=\"button\" value=\"Watch\" onClick=\"window.open('movie.php?id=$id&movieid=" . $val['movieid'] . "', " .
		"'movie', 'width=800,height=600,toolbar=yes,location=yes,directories=yes, status=yes,menubar=yes,scrollbar=yes,copyhistory=yes,resizable=yes,screenX=0')\")>" .
		'</form>' .
		'</td>' .
		'<td>' .
		'<form>' . 
		"<input type=\"button\" value=\"Open Remote\" onClick=\"window.open('remote.php?id=$id', " .
		"'remote', 'width=800,height=600,toolbar=yes,location=yes,directories=yes, status=yes,menubar=yes,scrollbar=yes,copyhistory=yes,resizable=yes,screenX=600')\")>" .
		'</form>' .
		'<td><a href="edit.php?movieid=' . $val['movieid'] . '">' . Edit . '</a></td>' .
		'<td><a href="?do=delete_movie&movieid=' . $val['movieid'] . '">' . Delete . '</a></td>' .
		"</tr>\n";
        }
}

eval('$content .= "' . fetchTemplate('movies') . '";');
eval('printOutput("' . fetchTemplate('shell') . '");');
?>
