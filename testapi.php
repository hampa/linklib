<?php
include_once("linkontrol/functions_linkontrol.php");
?>
<html>
<head></head>
<body>
<?php
if ($msg != '') {
	echo("<h2>$msg</h2>");
}
?>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=add_time_feed" >
<table>
<tr><td>movieid:</td>	<td><input name="movieid" value="1"></td></tr>
<tr><td>userid:</td>	<td><input name="userid" value="2101483"></td></tr>
<tr><td>start:</td>	<td><input name="start" value="10"></td></tr>
<tr><td>end:</td>	<td><input name="end" value="13"></td></tr>
<tr><td>title:</td>	<td><input name="title" value="Feed Title"></td></tr>
<tr><td>img:</td>	<td><input name="img" value="pp.png"></td></tr>
<tr><td>body:</td>	<td><input name="body" value="This is a feed title"></td></tr>
<tr><td>href:</td>	<td><input name="href" value="http://en.wikipedia.org/wiki/"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>
</form>
</body>
</html>
