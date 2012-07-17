<?php
include_once('linkontrol/functions_linkontrol.php');
$linkontrol = new linkontrol();
?>
<html>
<head>
<link href="css/editfeed.css" rel="stylesheet" />
<link href="css/edit.css" rel="stylesheet" />
<link href="css/nav.css" rel="stylesheet" />
<link href="css/apistyle.css" rel="stylesheet" />
</head>
<body>
<?php echo($linkontrol->getNavigationMenu()); ?>
<table> 
<div align="center">
</div>
<table align="center" border=1>
<form method=post action="?do=add_movie" >
<input type=hidden name=userid value="123">
<tr bgcolor="#AAAAAA">
<td><input name=name value="Enter Movie Title"></td>
<td><input name=href value="http://link.to.movie.com"></td>
<td colspan=3><input type=submit value="Add Movie"></td>
</form>
<?php
$arr = $linkontrol->getMovies();
if (isset($arr)) {
        foreach ($arr as $key => $val) {
                echo('<tr>' .
		'<td>' . $val['name'] . '</td>' .
		'<td>' . $val['href'] . '</td>' .
		'<td><a href="movie.php?movieid=' . $val['movieid'] . '">' . View . '</a></td>' .
		'<td><a href="edit.php?movieid=' . $val['movieid'] . '">' . Edit . '</a></td>' .
		'<td><a href="?do=delete_movie&movieid=' . $val['movieid'] . '">' . Delete . '</a></td>' .
		"</tr>\n");
        }
}
?>
</table>
</body>
</html>
