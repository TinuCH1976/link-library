<?php
	require_once('../../../wp-load.php');
	require_once('link-library.php');
	
	$catID = $_GET['id'];
	$settingsID = $_GET['settings'];
	$pageID = $_GET['page'];
	
	$settingsname = 'AdminSettings' . $settingsID;
	
	echo LinkLibrary($settingsname, 'obsolete', true, false, false, false, false, '', false, false, false, false, '<br />', false, 
					'', '', '<li>', '</li>', '', '', false, '', '', false, '', '', '', 1, '', '', '', false, 'linklistcatname', false,
					0, '', '', '', false, true, false, $catID, '', false, 3, false, false, 1, '', '', '', 'ASC', 'ASC', 'name', false, 5, false,
					'', false, false, '', '', 'right', false, 900, 700, '', '', 'beforename', '', $pageID);						
?>
