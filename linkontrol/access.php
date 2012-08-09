<?php
require_once("include/membersite_config.php");
$username = "";
if ($fgmembersite->CheckLogin()) {
	$username = $fgmembersite->Username();
}
$userid = $fgmembersite->UserId();
if (isset($_REQUEST['show_session'])) {
	echo("session\n");
	print_r($_SESSION);
}
?>
