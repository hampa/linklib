<?php
include_once('linkontrol/functions_linkontrol.php');
$linkontrol = new linkontrol();
?>
<html>
<head>
<!-- <link href="css/apistyle.css" rel="stylesheet" /> -->
<link href="css/nav.css" rel="stylesheet" />
<link href="css/apistyle.css" rel="stylesheet" />
</head>
<body>
<?php echo($linkontrol->getNavigationMenu()); ?>
<table> 
<div align="center">
</div>
<form method=GET action="remote.php">
<table align="center">
	<tr>	
		<td>Remote Code:</td>
		<td><input name="id" value="<?php echo($_GET['id']); ?>"></td>
		<td><input type=submit value="Connect"></td>
	</tr>
</table>
</form>
<div align="center">
<p>The Remote Code is shown on your movie window</p>
</div>
</body>
</html>
