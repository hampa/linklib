<html>
<head>
</head>
<body>
<table> 
<div align="center">
<h3>Movie Search<h3>
</div>
<form method=GET action="movie_search.php?do=search_movie">
<table align="center">
	<tr>	
		<td>Search:</td>
		<td><input name="name" value="<?php echo($_GET['q']); ?>"></td>
		<td><input type=submit value="Search"></td>
	</tr>
</table>
</form>
<div align="center">
<p>The Remote Code is shown on your movie window</p>
</div>
</body>
</html>
