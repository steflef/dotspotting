<?php

	#
	# $Id$
	#

	include("include/init.php");

	$recent_sheets = sheets_recently_created($GLOBALS['cfg']['user_id']);
	$GLOBALS['smarty']->assign_by_ref("recent_sheets", $recent_sheets['sheets']);

	$smarty->display('page_recent_sheets.txt');
	exit();

?>