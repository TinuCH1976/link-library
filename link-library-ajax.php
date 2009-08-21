<?php
	require_once('../../../wp-load.php');
	require_once('link-library.php');
	global $wpdb;
	
	$options  = get_option('LinkLibraryPP');
	
	$catID = $_GET['id'];
	$settingsID = $_GET['settings'];
	
	$settingsname = 'AdminSettings' . $settingsID;
	
	echo LinkLibrary($settingsname, 'obsolete', true, false, false, false, false, '', false, false, false, false, '<br />', false, 
					'', '', '<li>', '</li>', '', '', false, '', '', false, '', '', '', 1, '', '', '', false, 'linklistcatname', false,
					0, '', '', '', false, true, false, $catID, '', false, 3, false, false, 1, '', '', "");		
?>
