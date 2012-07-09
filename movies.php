<html>
<head>
</head>
<body>
<table> 
<div align="center">
<h3>Movies<h3>
</div>
<table align="center" border=1>
<?php
include_once('linkontrol/functions_linkontrol.php');
$linkontrol = new linkontrol();
$arr = $linkontrol->getMovies();
if (isset($arr)) {
        foreach ($arr as $key => $val) {
                echo('<tr><td><a href="movie.php?movieid=' . $val['movieid'] . '">' . $val['name'] . "($val[movieid])</a></td></tr>\n");
        }
}
?>
</table>
</body>
</html>
