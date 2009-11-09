<?php
/*
Plugin Name: Link Library
Plugin URI: http://wordpress.org/extend/plugins/link-library/
Description: Functions to generate link library page with a list of link
categories with hyperlinks to the actual link lists. Other options are
the ability to display notes on top of descriptions, to only display
selected categories and to display names of links at the same time
as their related images.
Version: 2.5.7
Author: Yannick Lefebvre
Author URI: http://yannickcorner.nayanna.biz/

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright � 2009 Yannick Lefebvre

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

You can also view a copy of the HTML version of the GNU General Public
License at http://www.gnu.org/copyleft/gpl.html

I, Yannick Lefebvre, can be contacted via e-mail at ylefebvre@gmail.com
*/

define('LLDIR', dirname(__FILE__) . '/');                

if ( ! class_exists( 'LL_Admin' ) ) {

	class LL_Admin {

		function add_config_page() {
			global $wpdb;
			if ( function_exists('add_submenu_page') ) {
				add_options_page('Link Library for Wordpress', 'Link Library', 9, basename(__FILE__), array('LL_Admin','config_page'));
				add_filter( 'plugin_action_links', array( 'LL_Admin', 'filter_plugin_actions'), 10, 2 );
				add_filter( 'ozh_adminmenu_icon', array( 'LL_Admin', 'add_ozh_adminmenu_icon' ) );				
			}
		} // end add_LL_config_page()

		function add_ozh_adminmenu_icon( $hook ) {
			static $llicon;
			if (!$llicon) {
				$llicon = WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)). '/chart_curve.png';
			}
			if ($hook == 'link-library.php') return $llicon;
			return $hook;
		}

		function filter_plugin_actions( $links, $file ){
			//Static so we don't call plugin_basename on every plugin row.
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

			if ( $file == $this_plugin ){
				$settings_link = '<a href="options-general.php?page=link-library.php">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link ); // before other links
			}
			return $links;
		}
		
		function config_page() {
			global $dlextensions;
			if ( isset($_GET['reset'])) {
			
					$options['order'] = 'name';
					$options['hide_if_empty'] = true;
					$options['table_width'] = 100;
					$options['num_columns'] = 1;
					$options['catanchor'] = true;
					$options['flatlist'] = false;
					$options['categorylist'] = null;
					$options['excludecategorylist'] = null;
					$options['showdescription'] = false;
					$options['shownotes'] = false;
					$options['showrating'] = false;
					$options['showupdated'] = false;
					$options['show_images'] = false;
					$options['show_image_and_name'] = false;
					$options['use_html_tags'] = false;
					$options['show_rss'] = false;
					$options['beforenote'] = '<br />';
					$options['afternote'] = '';
					$options['nofollow'] = false;
					$options['beforeitem'] = '<li>';
					$options['afteritem'] = '</li>';
					$options['beforedesc'] = '';
					$options['afterdesc'] = '';
					$options['displayastable'] = false;
					$options['beforelink'] = '';
					$options['afterlink'] = '';
					$options['showcolumnheaders'] = false;
					$options['linkheader'] = '';
					$options['descheader'] = '';
					$options['notesheader'] = '';
					$options['catlistwrappers'] = 1;
					$options['beforecatlist1'] = '';
					$options['beforecatlist2'] = '';
					$options['beforecatlist3'] = '';
					$options['divorheader'] = false;
					$options['catnameoutput'] = 'linklistcatname';
					$options['show_rss_icon'] = false;
					$options['linkaddfrequency'] = 0;
					$options['addbeforelink'] = '';
					$options['addafterlink'] = '';
					$options['linktarget'] = '';
					$options['showcategorydescheaders'] = false;
					$options['showcategorydesclinks'] = false;
					$options['settingssetname'] = 'Default';
					$options['showadmineditlinks'] = true;
					$options['showonecatonly'] = false;
					$options['loadingicon'] = '/icons/Ajax-loader.gif';
					$options['defaultsinglecat'] = '';
					$options['rsspreview'] = false;
					$options['rsspreviewcount'] = 3;
					$options['rssfeedinline'] = false;
					$options['rssfeedinlinecontent'] = false;
					$options['rssfeedinlinecount'] = 1;
					$options['beforerss'] = '';
					$options['afterrss'] = '';
					$options['rsscachedir'] = ABSPATH . 'wp-content/cache/link-library';
					$options['direction'] = 'ASC';
					$options['linkdirection'] = 'ASC';
					$options['linkorder'] = 'name';
					$options['pagination'] = false;
					$options['linksperpage'] = 5;
					$options['hidecategorynames'] = false;
					
					$settings = $_GET['reset'];
					$settingsname = 'LinkLibraryPP' . $settings;
					update_option($settingsname, $options);					
			}
			if ( isset($_GET['resettable']) ) {
					$options['order'] = 'name';
					$options['hide_if_empty'] = true;
					$options['table_width'] = 100;
					$options['num_columns'] = 3;
					$options['catanchor'] = true;
					$options['flatlist'] = false;
					$options['categorylist'] = null;
					$options['excludecategorylist'] = null;
					$options['showdescription'] = true;
					$options['shownotes'] = true;
					$options['showrating'] = false;
					$options['showupdated'] = false;
					$options['show_images'] = false;
					$options['show_image_and_name'] = false;
					$options['use_html_tags'] = false;
					$options['show_rss'] = false;
					$options['beforenote'] = '<td>';
					$options['afternote'] = '</td>';
					$options['nofollow'] = false;
					$options['beforeitem'] = '<tr>';
					$options['afteritem'] = '</tr>';
					$options['beforedesc'] = '<td>';
					$options['afterdesc'] = '</td>';
					$options['displayastable'] = true;
					$options['beforelink'] = '<td>';
					$options['afterlink'] = '</td>';
					$options['showcolumnheaders'] = true;
					$options['linkheader'] = 'Application';
					$options['descheader'] = 'Description';
					$options['notesheader'] = 'Similar to';
					$options['catlistwrappers'] = 1;
					$options['beforecatlist1'] = '';
					$options['beforecatlist2'] = '';
					$options['beforecatlist3'] = '';
					$options['divorheader'] = false;
					$options['catnameoutput'] = 'linklistcatname';
					$options['show_rss_icon'] = false;
					$options['linkaddfrequency'] = 0;
					$options['addbeforelink'] = '';
					$options['addafterlink'] = '';
					$options['linktarget'] = '';
					$options['showcategorydescheaders'] = false;
					$options['showcategorydesclinks'] = false;
					$options['settingssetname'] = 'Default';
					$options['showadmineditlinks'] = true;
					$options['showonecatonly'] = false;
					$options['loadingicon'] = '';
					$options['loadingicon'] = '/icons/Ajax-loader.gif';
					$options['defaultsinglecat'] = '';
					$options['rsspreview'] = false;
					$options['rsspreviewcount'] = 3;
					$options['rssfeedinline'] = false;
					$options['rssfeedinlinecontent'] = false;
					$options['rssfeedinlinecount'] = 1;
					$options['beforerss'] = '';
					$options['afterrss'] = '';
					$options['rsscachedir'] = ABSPATH . 'wp-content/cache/link-library';
					$options['direction'] = 'ASC';
					$options['linkdirection'] = 'ASC';
					$options['linkorder'] = 'name';
					$options['pagination'] = false;
					$options['linksperpage'] = 5;
					$options['hidecategorynames'] = false;
					
					$settings = $_GET['resettable'];
					$settingsname = 'LinkLibraryPP' . $settings;
					update_option($settingsname, $options);		
			}
			if ( isset($_GET['settings'])) {
				$settings = $_GET['settings'];				
			}
			if ( isset($_GET['copy']))
			{
				$destination = $_GET['copy'];
				$source = $_GET['source'];
				
				$sourcesettingsname = 'LinkLibraryPP' . $source;
				$sourceoptions = get_option($sourcesettingsname);
				
				$destinationsettingsname = 'LinkLibraryPP' . $destination;
				update_option($destinationsettingsname, $sourceoptions);
				
				$settings = $destination;
			}
			if ( isset($_GET['deletesettings']) ) {
				$settings = $_GET['deletesettings'];				
				$settingsname = 'LinkLibraryPP' . $settings;
				$options = delete_option($settingsname);
				$settings = 1;
			}			
			if (isset($_POST['submitgen']))
			{
				if (!current_user_can('manage_options')) die(__('You cannot edit the Link Library for WordPress options.'));
				check_admin_referer('linklibrarypp-config');
				
				foreach (array('stylesheet') as $option_name) {
					if (isset($_POST[$option_name])) {
						$genoptions[$option_name] = $_POST[$option_name];
					}
				}
				
				update_option('LinkLibraryGeneral', $genoptions);
				
			}
			if ( isset($_POST['submit1']) || isset($_POST['submit2']) || isset($_POST['submit3']) || isset($_POST['submit4']) || isset($_POST['submit5'])) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the Link Library for WordPress options.'));
				check_admin_referer('linklibrarypp-config');
				
				foreach (array('order', 'table_width', 'num_columns', 'categorylist', 'excludecategorylist', 'beforenote', 'afternote','position',
							   'beforeitem', 'afteritem', 'beforedesc', 'afterdesc', 'beforelink','afterlink', 'beforecatlist1',
							   'beforecatlist2', 'beforecatlist3','catnameoutput', 'linkaddfrequency', 'addbeforelink', 'addafterlink',
							   'defaultsinglecat', 'rsspreviewcount', 'rssfeedinlinecount','beforerss','afterrss','linksperpage') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = strtolower($_POST[$option_name]);
					}
				}
				
				foreach (array('linkheader', 'descheader', 'notesheader','linktarget', 'settingssetname', 'loadingicon','rsscachedir',
								'direction', 'linkdirection', 'linkorder') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = $_POST[$option_name];
					}
				}
				
				foreach (array('hide_if_empty', 'catanchor', 'showdescription', 'shownotes', 'showrating', 'showupdated', 'show_images', 
								'show_image_and_name', 'use_html_tags', 'show_rss', 'nofollow','showcolumnheaders','show_rss_icon', 'showcategorydescheaders',
								'showcategorydesclinks', 'showadmineditlinks', 'showonecatonly', 'rsspreview', 'rssfeedinline', 'rssfeedinlinecontent',
								'pagination', 'hidecategorynames') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = true;
					} else {
						$options[$option_name] = false;
					}
				}

				if ($_POST['flatlist'] == 'true') {
					$options['flatlist'] = true;
				} 
				else if ($_POST['flatlist'] == 'false') {
					$options['flatlist'] = false;
				}
				
				if ($_POST['displayastable'] == 'true') {
					$options['displayastable'] = true;
				} 
				else if ($_POST['displayastable'] == 'false') {
					$options['displayastable'] = false;
				}
				
				if ($_POST['divorheader'] == 'true') {
					$options['divorheader'] = true;
				} 
				else if ($_POST['divorheader'] == 'false') {
					$options['divorheader'] = false;
				}
				
				foreach (array('catlistwrappers') as $option_name)
				{
					if (isset($_POST[$option_name])) {
						$options[$option_name] = (int)($_POST[$option_name]);
					}
				
				}
				
				if (isset($_POST['submit1']))
				{
					update_option('LinkLibraryPP1', $options);
					echo '<div id="message" class="updated fade"><p><strong>Settings Set 1 Updated!</strong>';
				}
				else if (isset($_POST['submit2']))
				{
					update_option('LinkLibraryPP2', $options);
					echo '<div id="message" class="updated fade"><p><strong>Settings Set 2 Updated!</strong>';
				}
				else if (isset($_POST['submit3']))
				{
					update_option('LinkLibraryPP3', $options);
					echo '<div id="message" class="updated fade"><p><strong>Settings Set 3 Updated!</strong>';
				}
				else if (isset($_POST['submit4']))
				{
					update_option('LinkLibraryPP4', $options);
					echo '<div id="message" class="updated fade"><p><strong>Settings Set 4 Updated!</strong>';
				}
				else if (isset($_POST['submit5']))
				{
					update_option('LinkLibraryPP5', $options);
					echo '<div id="message" class="updated fade"><p><strong>Settings Set 5 Updated!</strong>';
				}				
					
				$categoryids = explode(',', $options['categorylist']);
				
				foreach($categoryids as $categoryid)
				{
					$catnames = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0&include=$categoryid");
					if (!$catnames)
					{
						echo '<br /><br />Included Category ID ' . $categoryid . ' is invalid. Please check the ID in the Link Category editor.';
					}
				}
				
				$categoryids = explode(',', $options['excludecategorylist']);
				
				foreach($categoryids as $categoryid)
				{
					$catnames = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0&include=$categoryid");
					if (!$catnames)
					{
						echo '<br /><br />Excluded Category ID ' . $categoryid . ' is invalid. Please check the ID in the Link Category editor.';
					}
				}
				
				echo '</p></div>';
					
				
				
			}
			
			// Pre-2.6 compatibility
			if ( !defined('WP_CONTENT_URL') )
				define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
			if ( !defined('WP_CONTENT_DIR') )
				define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

			// Guess the location
			$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';

			if ($settings == '')
			{
				$options = get_option('LinkLibraryPP1');
				$settings = 1;
			}
			else
			{
				$settingsname = 'LinkLibraryPP' . $settings;
				$options = get_option($settingsname);
			}
				
			if ($options == "")
			{
				$options['order'] = 'name';
				$options['hide_if_empty'] = true;
				$options['table_width'] = 100;
				$options['num_columns'] = 1;
				$options['catanchor'] = true;
				$options['flatlist'] = false;
				$options['categorylist'] = null;
				$options['excludecategorylist'] = null;
				$options['showdescription'] = false;
				$options['shownotes'] = false;
				$options['showrating'] = false;
				$options['showupdated'] = false;
				$options['show_images'] = false;
				$options['show_image_and_name'] = false;
				$options['use_html_tags'] = false;
				$options['show_rss'] = false;
				$options['beforenote'] = '<br />';
				$options['afternote'] = '';
				$options['nofollow'] = false;
				$options['beforeitem'] = '<li>';
				$options['afteritem'] = '</li>';
				$options['beforedesc'] = '';
				$options['afterdesc'] = '';
				$options['displayastable'] = false;
				$options['beforelink'] = '';
				$options['afterlink'] = '';
				$options['showcolumnheaders'] = false;
				$options['linkheader'] = '';
				$options['descheader'] = '';
				$options['notesheader'] = '';
				$options['catlistwrappers'] = 1;
				$options['beforecatlist1'] = '';
				$options['beforecatlist2'] = '';
				$options['beforecatlist3'] = '';
				$options['divorheader'] = false;
				$options['catnameoutput'] = 'linklistcatname';
				$options['show_rss_icon'] = false;
				$options['linkaddfrequency'] = 0;
				$options['addbeforelink'] = '';
				$options['addafterlink'] = '';	
				$options['linktarget'] = '';
				$options['showcategorydescheaders'] = false;
				$options['showcategorydesclinks'] = false;
				$options['settingssetname'] = 'Default';
				$options['showadmineditlinks'] = true;
				$options['showonecatonly'] = false;
				$options['loadingicon'] = '/icons/Ajax-loader.gif';
				$options['defaultsinglecat'] = '';
				$options['rsspreview'] = false;
				$options['rsspreviewcount'] = 3;
				$options['rssfeedinline'] = false;
				$options['rssfeedinlinecontent'] = false;
				$options['rssfeedinlinecount'] = 1;
				$options['beforerss'] = '';
				$options['afterrss'] = '';
				$options['rsscachedir'] = ABSPATH . 'wp-content/cache/link-library';
				$options['direction'] = 'ASC';
				$options['linkdirection'] = 'ASC';
				$options['linkorder'] = 'name';
				$options['pagination'] = false;
				$options['linksperpage'] = 5;

				update_option($settingsname,$options);
			}	
			
			$genoptions = get_option('LinkLibraryGeneral');
				
			if ($genoptions == "")
			{
				$genoptions['stylesheet'] = 'stylesheet.css';
				update_option('LinkLibraryGeneral', $genoptions);
			}
				
			$options1 = get_option('LinkLibraryPP1');
			$options2 = get_option('LinkLibraryPP2');
			$options3 = get_option('LinkLibraryPP3');
			$options4 = get_option('LinkLibraryPP4');
			$options5 = get_option('LinkLibraryPP5');
			?>		
			<div class="wrap" id='lladmin' style='width:1000px'>
				<h2>Link Library Configuration</h2>
				Help: <a target='llinstructions' href='http://wordpress.org/extend/plugins/link-library/installation/'>Installation Instructions</a> | <a href='http://wordpress.org/extend/plugins/link-library/faq/' target='llfaq'>Frequently Asked Questions (FAQ)</a> | Help is also available as tooltips on fields | <a href='http://yannickcorner.nayanna.biz/contact-me'>Contact the Author</a><br /><br />
				
				
				<form name='lladmingenform' action="" method="post" id="ll-conf">
				<?php
				if ( function_exists('wp_nonce_field') )
						wp_nonce_field('linklibrarypp-config');
					?>
				<fieldset style='border:1px solid #CCC;padding:10px'>
				<legend tooltip='These apply to all Settings Sets' style='padding: 0 5px 0 5px;'><strong>General Settings <span style="border:0;padding-left: 15px;" class="submit"><input type="submit" name="submitgen" value="Update General Settings &raquo;" /></span></strong></legend>
				<table>
				<tr>
				<td style='width:200px'>Stylesheet File Name</td>
				<td><input type="text" id="stylesheet" name="stylesheet" size="40" value="<?php echo $genoptions['stylesheet']; ?>"/></td>
				</tr>
				</table>
				</fieldset>
				<br />
				</form><br />
				
				<form name="lladminform" action="" method="post" id="analytics-conf">
				<?php
					if ( function_exists('wp_nonce_field') )
						wp_nonce_field('linklibrarypp-config');
					?>
					<div>
					<table class='widefat' style='clear:none;width:100%;background: #DFDFDF url(/wp-admin/images/gray-grad.png) repeat-x scroll left top;'>
						<thead>
						<tr>
							<th style='width:10px'>
							</th>
							<th style='width:40px' tooltip='Link Library Supports the Creation of up to 5 configurations to display link lists on your site'>
								Set #
							</th>
							<th tooltip='Link Library Supports the Creation of up to 5 configurations to display link lists on your site'>
								Set Name
							</th>
							<th tooltip='Link Library Supports the Creation of up to 5 configurations to display link lists on your site'>
								Code to insert on a Wordpress page to see Link Library
							</th>
							<th>
								Add/Delete
							</th>
							<th>Copy Settings</th>
						</tr>
						</thead>
						<tr>
						<td style='background: #FFF'><?php if ($settings == 1) {echo '<img src="' . $llpluginpath . '/icons/next-16x16.png" />';} ?></td><td style='background: #FFF'><a href="?page=link-library.php&amp;settings=1">1</a></td><td style='background: #FFF'><?php if ($options1 != "") echo '<a href="?page=link-library.php&amp;settings=1">' . $options1['settingssetname']; ?></a></td><td style='background: #FFF'><?php if ($options1 != "") echo "[link-library-cats settings=1] [link-library-search] [link-library settings=1]"; ?></td><td style='background: #FFF;text-align:center'></td><td style='background: #FFF;text-align:center'><?php if ($settings != 1) { echo "<a href='?page=link-library.php&amp;copy=" . $settings . "&source=1' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to overwrite current settings by copying from Settings Set '%s'\n  'Cancel' to stop, 'OK' to copy."), 1 )) . "') ) { return true;}return false;\"><img src='" . $llpluginpath . "/icons/page_copy.png' /></a> ";} ?></td>
						</tr>
						<?php for ($i = 2; $i <= 5; $i++): ?>
						<tr>
						<td style='background: #FFF'><?php if ($settings == $i) {echo '<img src="' . $llpluginpath . '/icons/next-16x16.png" />';} ?></td><td style='background: #FFF'><?php if (${"options$i"} != "") {echo "<a href='?page=link-library.php&amp;settings=" . $i . "'>" . $i . "</a>";} else { echo $i;}?></td><td style='background: #FFF'><?php if (${"options$i"} != "") echo '<a href="?page=link-library.php&amp;settings=' . $i . '">' . ${"options$i"}['settingssetname'] . '</a>'; else echo 'Empty';?></td><td style='background: #FFF'><?php if (${"options$i"} != "") echo "[link-library-cats settings=" . $i . "] [link-library-search] [link-library settings=" . $i . "]"; ?></td><td style='background: #FFF;text-align:center'><?php if (${"options$i"} != "") {echo "<a href='?page=link-library.php&amp;deletesettings=" . $i . "' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to delete Settings Set '%s'\n  'Cancel' to stop, 'OK' to delete."), $i )) . "') ) { return true;}return false;\"><img title='Delete Settings Set' src='" . $llpluginpath . "/icons/delete-16x16.png' /></a>";} else echo '<a href="?page=link-library.php&amp;settings=' . $i . '"><img title="Create Settings Set" src="' . $llpluginpath . '/icons/add-16x16.png" /></a>'; ?></td><td style='background: #FFF;text-align:center'><?php if ($settings != $i && ${"options$i"} != "") { echo "<a href='?page=link-library.php&amp;copy=" . $settings . "&source=" . $i . "' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to overwrite current settings by copying from Settings Set '%s'\n  'Cancel' to stop, 'OK' to copy."), $i )) . "') ) { return true;}return false;\"><img src='" . $llpluginpath . "/icons/page_copy.png' /></a> ";} ?></td>
						</tr>
						<?php endfor; ?>
					</table><br />
					<div style='float:left'><span style="border:0;" class="submit"><input type="submit" name="submit<?php echo $settings; ?>" value="Update Settings &raquo;" /></span></div>
					<div style='float:right'>
					<span><a href='?page=link-library.php&amp;reset=<?php echo $settings; ?>' <?php echo "onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to reset Setting Set '%s'\n  'Cancel' to stop, 'OK' to reset."), $settings )) . "') ) { return true;}return false;\""; ?>>Reset current Settings Set</a></span>
					
					<span><a href='?page=link-library.php&amp;resettable=<?php echo $settings; ?>' <?php echo "onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to reset Setting Set '%s' for a table layout\n  'Cancel' to stop, 'OK' to reset."), $settings )) . "') ) { return true;}return false;\""; ?>>Reset current Setting Set for table layout</a></span>
					</div>

					</div>
					
					<div style='padding-top: 15px;clear:both'>
					<fieldset style='border:1px solid #CCC;padding:10px'>
					<legend style='padding: 0 5px 0 5px;'><strong>Common Parameters</strong></legend>
					<table>
					
					<tr>
						<td style='width: 300px;padding-right: 50px'>
							Current Settings Set Name
						</td>
						<td>
							<input type="text" id="settingssetname" name="settingssetname" size="40" value="<?php echo $options['settingssetname']; ?>"/>
						</td>
					</tr>
					<tr>
						<td tooltip="Leave Empty to see all categories<br /><br />Enter list of comma-separated<br />numeric category IDs<br /><br />For example: 2,4,56">
							Categories to be displayed (Empty=All)
						</td>
						<td tooltip="Leave Empty to see all categories<br /><br />Enter list of comma-separated<br />numeric category IDs<br /><br />For example: 2,4,56">
							<input type="text" id="categorylist" name="categorylist" size="40" value="<?php echo $options['categorylist']; ?>"/>
						</td>
					</tr>
					<tr>
						<td tooltip="Enter list of comma-separated<br />numeric category IDs that should not be shown<br /><br />For example: 5,34,43">
							Categories to be excluded
						</td>
						<td tooltip="Enter list of comma-separated<br />numeric category IDs that should not be shown<br /><br />For example: 5,34,43">
							<input type="text" id="excludecategorylist" name="excludecategorylist" size="40" value="<?php echo $options['excludecategorylist']; ?>"/>
						</td>
					</tr>
					<tr>
						<td tooltip="This functionality uses AJAX queries">
							Only show one category at a time
						</td>
						<td tooltip="This functionality uses AJAX queries">
							<input type="checkbox" id="showonecatonly" name="showonecatonly" <?php if ($options['showonecatonly']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<td>
							Default category to be shown when only showing one at a time (numeric ID)
						</td>
						<td>
							<input type="text" id="defaultsinglecat" name="defaultsinglecat" size="4" value="<?php echo $options['defaultsinglecat']; ?>"/>
						</td>
					</tr>
					<tr>
						<td tooltip="File path is relative to Link Library plugin directory">
							Icon to display when performing AJAX queries
						</td>
						<td tooltip="File path is relative to Link Library plugin directory">
							<input type="text" id="loadingicon" name="loadingicon" size="40" value="<?php if ($options['loadingicon'] == '') {echo '/icons/Ajax-loader.gif';} else {echo strval($options['loadingicon']);} ?>"/>
						</td>
					</tr>
					<tr>
						<td tooltip='Only show a limited number of links and add page navigation links'>
							Paginate Results
						</td>
						<td tooltip='Only show a limited number of links and add page navigation links'>
							<input type="checkbox" id="pagination" name="pagination" <?php if ($options['pagination']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					<tr>
						<td tooltip="Number of Links to be Displayed per Page in Pagination Mode">
							Links per Page
						</td>
						<td tooltip="Number of Links to be Displayed per Page in Pagination Mode">
							<input type="text" id="linksperpage" name="linksperpage" size="3" value="<?php echo $options['linksperpage']; ?>"/>
						</td>
					</tr>				
					<tr>
						<td>
							Hide Results if Empty
						</td>
						<td>
							<input type="checkbox" id="hide_if_empty" name="hide_if_empty" <?php if ($options['hide_if_empty']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					</table>
					</fieldset>
					</div>
					<div style='clear:both;padding-top:15px'>
					<fieldset style='border:1px solid #CCC;padding:10px;margin:5px 0 5px 0;'>
					<legend style='padding: 0 5px 0 5px;'><strong>Link Categories Settings</strong></legend>
					<table>
					<tr>
						<td>
							Results Order
						</td>
						<td>
							<select name="order" id="order" style="width:200px;">
								<option value="name"<?php if ($options['order'] == 'name') { echo ' selected="selected"';} ?>>Order by Name</option>
								<option value="id"<?php if ($options['order'] == 'id') { echo ' selected="selected"';} ?>>Order by ID</option>
								<option value="catlist"<?php if ($options['order'] == 'catlist') { echo ' selected="selected"';} ?>>Order of categories based on included category list</option>
								<option value="order"<?php if ($options['order'] == 'order') { echo ' selected="selected"';} ?>>Order set by 'My Link Order' Wordpress Plugin</option>
							</select>
						</td>
						<td style='width:100px'></td>
						<td style='width:200px'>
							Link Categories Display Format
						</td>
						<td>
							<select name="flatlist" id="flatlist" style="width:200px;">
								<option value="false"<?php if ($options['flatlist'] == false) { echo ' selected="selected"';} ?>>Table</option>
								<option value="true"<?php if ($options['flatlist'] == true) { echo ' selected="selected"';} ?>>Unordered List</option>
							</select>
						</td>
					</tr>					
					<tr>
						<td tooltip="This setting does not apply when selecting My Link Order for the order">
							Direction
						</td>
						<td tooltip="This setting does not apply when selecting My Link Order for the order">
							<select name="direction" id="direction" style="width:100px;">
								<option value="ASC"<?php if ($options['direction'] == 'ASC') { echo ' selected="selected"';} ?>>Ascending</option>
								<option value="DESC"<?php if ($options['direction'] == 'DESC') { echo ' selected="selected"';} ?>>Descending</option>
							</select>
						</td>
						<td></td>
						<td tooltip="Use [ and ] in the description to perform special actions using HTML such as inserting images instead of < and >">
							Show Category Description
						</td>
						<td tooltip="Use [ and ] in the description to perform special actions using HTML such as inserting images instead of < and >">
							<input type="checkbox" id="showcategorydescheaders" name="showcategorydescheaders" <?php if ($options['showcategorydescheaders']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>			
					<tr>
						<td>
							Width of Categories Table in Percents
						</td>
						<td>
							<input type="text" id="table_width" name="table_width" size="10" value="<?php echo strval($options['table_width']); ?>"/>
						</td>
						<td></td>
						<td tooltip='Determines the number of alternating div tags that will be placed before and after each link category.<br /><br />These div tags can be used to style of position link categories on the link page.'>
							Number of alternating div classes 
						</td>
						<td tooltip='Determines the number of alternating div tags that will be placed before and after each link category.<br /><br />These div tags can be used to style of position link categories on the link page.'>
							<select name="catlistwrappers" id="catlistwrappers" style="width:200px;">
								<option value="1"<?php if ($options['catlistwrappers'] == 1) { echo ' selected="selected"';} ?>>1</option>
								<option value="2"<?php if ($options['catlistwrappers'] == 2) { echo ' selected="selected"';} ?>>2</option>
								<option value="3"<?php if ($options['catlistwrappers'] == 3) { echo ' selected="selected"';} ?>>3</option>
							</select>
						</td>						
					</tr>
					<tr>
						<td>
							Number of columns in Categories Table
						</td>
						<td>
							<input type="text" id="num_columns" name="num_columns" size="10" value="<?php echo strval($options['num_columns']); ?>">
						</td>
						<td></td>
						<td>
							First div class name
						</td>
						<td>
							<input type="text" id="beforecatlist1" name="beforecatlist1" size="40" value="<?php echo $options['beforecatlist1']; ?>" />
						</td>					
					</tr>
					<tr>
						<td>
							Use Div Class or Heading tag around Category Names
						</td>
						<td>
							<select name="divorheader" id="divorheader" style="width:200px;">
								<option value="false"<?php if ($options['divorheader'] == false) { echo ' selected="selected"';} ?>>Div Class</option>
								<option value="true"<?php if ($options['divorheader'] == true) { echo ' selected="selected"';} ?>>Heading Tag</option>
							</select>
						</td>
						<td></td>
						<td>
							Second div class name
						</td>
						<td>
							<input type="text" id="beforecatlist2" name="beforecatlist2" size="40" value="<?php echo $options['beforecatlist2']; ?>" />
						</td>
					</tr>					
					<tr>
						<td tooltip="Example div class name: linklistcatname, Example Heading Label: h3">
							Div Class Name or Heading label
						</td>
						<td  tooltip="Example div class name: linklistcatname, Example Heading Label: h3">
							<input type="text" id="catnameoutput" name="catnameoutput" size="30" value="<?php echo strval($options['catnameoutput']); ?>"/>
						</td>
						<td></td>
						<td>
							Third div class name
						</td>
						<td>
							<input type="text" id="beforecatlist3" name="beforecatlist3" size="40" value="<?php echo $options['beforecatlist3']; ?>" />
						</td>
					</tr>
					</table>
					</fieldset>
					<fieldset style='border:1px solid #CCC;padding:10px;margin:15px 0 5px 0;'>
					<legend style='padding: 0 5px 0 5px;'><strong>Link Element Settings</strong></legend>
					<table>
					<tr>
						<td>
							Link Results Order
						</td>
						<td>
							<select name="linkorder" id="linkorder" style="width:250px;">
								<option value="name"<?php if ($options['linkorder'] == 'name') { echo ' selected="selected"';} ?>>Order by Name</option>
								<option value="id"<?php if ($options['linkorder'] == 'id') { echo ' selected="selected"';} ?>>Order by ID</option>
								<option value="order"<?php if ($options['linkorder'] == 'order') { echo ' selected="selected"';} ?>>Order set by 'My Link Order' Wordpress Plugin</option>
							</select>
						</td>
						<td style='width:100px'></td>
						<td tooltip="Use [ and ] in the description to perform special actions using HTML such as inserting images instead of < and >">
							Show Category Description
						</td>
						<td tooltip="Use [ and ] in the description to perform special actions using HTML such as inserting images instead of < and >">
							<input type="checkbox" id="showcategorydesclinks" name="showcategorydesclinks" <?php if ($options['showcategorydesclinks']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<td tooltip='Except for My Link Order mode'>
							Direction
						</td>
						<td tooltip='Except for My Link Order mode'>
							<select name="linkdirection" id="linkdirection" style="width:200px;">
								<option value="ASC"<?php if ($options['linkdirection'] == 'ASC') { echo ' selected="selected"';} ?>>Ascending</option>
								<option value="DESC"<?php if ($options['linkdirection'] == 'DESC') { echo ' selected="selected"';} ?>>Descending</option>
							</select>
						</td>
						<td></td>
						<td tooltip='Need to be active for Link Categories to work'>
							Embed HTML anchors
						</td>
						<td tooltip='Need to be active for Link Categories to work'>
							<input type="checkbox" id="catanchor" name="catanchor" <?php if ($options['catanchor']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					<tr>
						<td tooltip="Sets default link target window, does not override specific targets set in links">
							Link Target
						</td>
						<td tooltip="Sets default link target window, does not override specific targets set in links">
							<input type="text" id="linktarget" name="linktarget" size="40" value="<?php echo $options['linktarget']; ?>"/>
						</td>
						<td></td>
						<td>
							Link Display Format
						</td>
						<td>
							<select name="displayastable" id="displayastable" style="width:200px;">
								<option value="true"<?php if ($options['displayastable'] == true) { echo ' selected="selected"';} ?>>Table</option>
								<option value="false"<?php if ($options['displayastable'] == false) { echo ' selected="selected"';} ?>>Unordered List</option>
							</select>
						</td>
					</tr>				
					<tr>
						<td>
							Show Column Headers
						</td>
						<td>
							<input type="checkbox" id="showcolumnheaders" name="showcolumnheaders" <?php if ($options['showcolumnheaders']) echo ' checked="checked" '; ?>/>
						</td>
						<td></td>
						<td>
							Link Column Header
						</td>
						<td>
							<input type="text" id="linkheader" name="linkheader" size="40" value="<?php echo $options['linkheader']; ?>"/>
						</td>
					</tr>	
					<tr>
						<td>
							Description Column Header
						</td>
						<td>
							<input type="text" id="descheader" name="descheader" size="40" value="<?php echo $options['descheader']; ?>"/>
						</td>
						<td></td>
						<td>
							Notes Column Header
						</td>
						<td>
							<input type="text" id="notesheader" name="notesheader" size="40" value="<?php echo $options['notesheader']; ?>"/>
						</td>
					</tr>
					<tr>
						<td>
							Hide Category Names
						</td>
						<td>
							<input type="checkbox" id="hidecategorynames" name="hidecategorynames" <?php if ($options['hidecategorynames'] == true) echo ' checked="checked" '; ?>/>
						</td>
						<td></td>
						<td>
						</td>
						<td>
						</td>
					</tr>	
					</table>
					<br />
					<strong>Link Sub-Field Configuration Table</strong>
						<table class='widefat' style='margin:15px 5px 10px 5px;clear:none;width:400px;background: #DFDFDF url(/wp-admin/images/gray-grad.png) repeat-x scroll left top;'>
							<thead>
								<tr>
									<th></th>
									<th tooltip='This column allows for the output of text/code before a number of links determined by the Display field'>Intermittent Before Link</th>
									<th tooltip='This column allows for the output of text/code before each link'>Before Link</th>
									<th tooltip='This column allows for the output of text/code before and after each link name'>Link</th>
									<th tooltip='This column allows for the output of text/code before and after each link description'>Link Description</th>
									<th tooltip='This column allows for the output of text/code before and after each link notes'>Link Notes</th>
									<th tooltip='This column allows for the output of text/code before and after the RSS icons'>RSS Icons</th>
									<th tooltip='This column allows for the output of text/code after each link'>After Link Block</th>
									<th tooltip='This column allows for the output of text/code after a number of links determined in the first column'>Intermittent After Link</th>
								</tr>
							</thead>			
							<tr>
								<td style='background: #FFF'>
									Display
								</td>
								<td style='background: #FFF' tooltip='Frequency of additional output before and after complete link group'>
									<input type="text" id="linkaddfrequency" name="linkaddfrequency" size="10" value="<?php echo strval($options['linkaddfrequency']); ?>"/>
								</td>						
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF' tooltip='Check to display link descriptions'>
									<input type="checkbox" id="showdescription" name="showdescription" <?php if ($options['showdescription']) echo ' checked="checked" '; ?>/>
								</td>
								<td style='background: #FFF' tooltip='Check to display link notes'>
									<input type="checkbox" id="shownotes" name="shownotes" <?php if ($options['shownotes']) echo ' checked="checked" '; ?>/>
								</td>
								<td style='background: #FFF'>
									See below
								</td>
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF'>
								</td>
							</tr>					
							<tr>
								<td style='background: #FFF'>
									Before
								</td>
								<td style='background: #FFF' tooltip='Output before complete link group (link, notes, desc, etc...)'>
									<input type="text" id="addbeforelink" name="addbeforelink" size="12" value="<?php echo $options['addbeforelink']; ?>"/>
								</td>						
								<td style='background: #FFF' tooltip='Output before complete link group (link, notes, desc, etc...)'>
									<input type="text" id="beforeitem" name="beforeitem" size="12" value="<?php echo $options['beforeitem']; ?>"/>
								</td>
								<td style='background: #FFF' tooltip='Code/Text to be displayed before each link'>
									<input type="text" id="beforelink" name="beforelink" size="12" value="<?php echo $options['beforelink']; ?>"/>
								</td>
								<td style='background: #FFF' tooltip='Code/Text to be displayed before each description'>
									<input type="text" id="beforedesc" name="beforedesc" size="12" value="<?php echo $options['beforedesc']; ?>"/>
								</td>
								<td style='background: #FFF' tooltip='Code/Text to be displayed before each note'>
									<input type="text" id="beforenote" name="beforenote" size="12" value="<?php echo $options['beforenote']; ?>"/>
								</td>
								<td style='background: #FFF' tooltip='Code/Text to be displayed before RSS Icons'>
									<input type="text" id="beforerss" name="beforerss" size="12" value="<?php echo $options['beforerss']; ?>"/>
								</td>
								<td style='background: #FFF'>
								</td>						
								<td style='background: #FFF'>
								</td>						
							</tr>
							<tr>
								<td style='background: #FFF'>
									After
								</td>
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF' tooltip='Output before complete link group (link, notes, desc, etc...)'>
								</td>
								<td style='background: #FFF' tooltip='Code/Text to be displayed after each link'>
									<input type="text" id="afterlink" name="afterlink" size="12" value="<?php echo $options['afterlink']; ?>"/>
								</td>
								<td style='background: #FFF' tooltip='Code/Text to be displayed after each description'>
									<input type="text" id="afterdesc" name="afterdesc" size="12" value="<?php echo $options['afterdesc']; ?>"/>
								</td>
								<td style='background: #FFF' tooltip='Code/Text to be displayed after each note'>
									<input type="text" id="afternote" name="afternote" size="12" value="<?php echo $options['afternote']; ?>"/>
								</td>
								<td  style='background: #FFF' tooltip='Code/Text to be displayed after RSS Icons'>
									<input type="text" id="afterrss" name="afterrss" size="12" value="<?php echo $options['afterrss']; ?>"/>
								</td>
								<td style='background: #FFF' tooltip='Output after complete link group (link, notes, desc, etc...)'>
									<input type="text" id="afteritem" name="afteritem" size="12" value="<?php echo $options['afteritem']; ?>"/>
								</td>	
								<td style='background: #FFF'>
									<input type="text" id="addafterlink" name="addafterlink" size="12" value="<?php echo $options['addafterlink']; ?>"/>
								</td>
							</tr>
					</table>
					<br />
					<table>
					<tr>
						<td style='width=150px'>
							Show Link Rating
						</td>
						<td style='width=75px;padding:0px 20px 0px 20px'>
							<input type="checkbox" id="showrating" name="showrating" <?php if ($options['showrating']) echo ' checked="checked" '; ?>/>
						</td>
						<td style='width:100px'></td>
						<td>
							Show Link Updated Flag
						</td>
						<td style='width=75px;padding:0px 20px 0px 20px'>
							<input type="checkbox" id="showupdated" name="showupdated" <?php if ($options['showupdated']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
						<td>
							Show Link Images
						</td>
						<td style='width=75px;padding:0px 20px 0px 20px'>
							<input type="checkbox" id="show_images" name="show_images" <?php if ($options['show_images']) echo ' checked="checked" '; ?>/>
						</td>
						<td></td>
						<td>
							Show Link Image and Name
						</td>
						<td style='width=75px;padding:0px 20px 0px 20px'>
							<input type="checkbox" id="show_image_and_name" name="show_image_and_name" <?php if ($options['show_image_and_name']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>					
						<td>
							Use HTML tags for formatting
						</td>
						<td style='width=75px;padding:0px 20px 0px 20px'>
							<input type="checkbox" id="use_html_tags" name="use_html_tags" <?php if ($options['use_html_tags']) echo ' checked="checked" '; ?>/>
						</td>
						<td></td>
						<td>
							Add nofollow tag to outgoing links
						</td>
						
						<td style='width=75px;padding:0px 20px 0px 20px'>
							<input type="checkbox" id="nofollow" name="nofollow" <?php if ($options['nofollow']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					<tr>					
						<td>
							Show edit links when logged in as editor or administrator
						</td>
						<td style='width=75px;padding:0px 20px 0px 20px'>
							<input type="checkbox" id="showadmineditlinks" name="showadmineditlinks" <?php if ($options['showadmineditlinks']) echo ' checked="checked" '; ?>/>
						</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					</table>
					<fieldset style='border:1px solid #CCC;padding:15px;margin:15px;'>
					<legend style='padding: 0 5px 0 5px;'><strong>RSS Field Configuration</strong></legend>
					<table>
					<tr>
						<td>
							Show RSS Link using Text
						</td>
						<td style='width=75px;padding-right:20px'>
							<input type="checkbox" id="show_rss" name="show_rss" <?php if ($options['show_rss']) echo ' checked="checked" '; ?>/>
						</td>
						<td>
							Show RSS Link using Standard Icon
						</td>
						<td style='width=75px;padding-right:20px'>
							<input type="checkbox" id="show_rss_icon" name="show_rss_icon" <?php if ($options['show_rss_icon']) echo ' checked="checked" '; ?>/>
						</td>
						<td></td><td style='width=75px;padding-right:20px'></td>
					</tr>					
					<tr>
						<td colspan='1' tooltip='Used for RSS Preview and RSS Inline Articles options below. Must have write access to directory.'>
							RSS Cache Directory
						</td>
						<td colspan='5' tooltip='Used for RSS Preview and RSS Inline Articles options below. Must have write access to directory.'>
							<input type="text" id="rsscachedir" name="rsscachedir" size="80" value="<?php if ($options['rsscachedir'] == '') echo ABSPATH . 'wp-content/cache/link-library'; else echo $options['rsscachedir']; ?>"/>
						</td>					
					</tr>
					<tr>
						<td>
							Show RSS Preview Link
						</td>
						<td>
							<input type="checkbox" id="rsspreview" name="rsspreview" <?php if ($options['rsspreview']) echo ' checked="checked" '; ?>/>
						</td>
						<td>
							Number of articles shown in RSS Preview
						</td>
						<td>
							<input type="text" id="rsspreviewcount" name="rsspreviewcount" size="2" value="<?php if ($options['rsspreviewcount'] == '') echo '3'; else echo strval($options['rsspreviewcount']); ?>"/>
						</td>				
						<td>
							Show RSS Feed Headers in Link Library output
						</td>
						<td>
							<input type="checkbox" id="rssfeedinline" name="rssfeedinline" <?php if ($options['rssfeedinline']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>					
					<tr>
						<td>
							Show RSS Feed Content in Link Library output
						</td>
						<td>
							<input type="checkbox" id="rssfeedinlinecontent" name="rssfeedinlinecontent" <?php if ($options['rssfeedinlinecontent']) echo ' checked="checked" '; ?>/>
						</td>
						<td>
							Number of RSS articles shown in Link Library Output
						</td>
						<td>
							<input type="text" id="rssfeedinlinecount" name="rssfeedinlinecount" size="2" value="<?php if ($options['rssfeedinlinecount'] == '') echo '1'; else echo strval($options['rssfeedinlinecount']); ?>"/>
						</td>				
						<td></td><td></td>						
					</tr>				
					</table>
					</div>

					<p style="border:0;" class="submit"><input type="submit" name="submit<?php echo $settings; ?>" value="Update Settings &raquo;" /></p>
					
					
				</form>
			</div>
			
			<script type="text/javascript">
// Create the tooltips only on document load
jQuery(document).ready(function()
	{
	// Notice the use of the each() method to acquire access to each elements attributes
	jQuery('#lladmin td[tooltip]').each(function()
		{
		jQuery(this).qtip({
			content: jQuery(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
			style: {
				width: 300,
				name: 'cream', // Give it a crea mstyle to make it stand out
			},
			position: {
				corner: {
					target: 'bottomLeft',
					tooltip: 'topLeft'
				}
			}
		});
	});
	
		jQuery('#lladmin th[tooltip]').each(function()
		{
		jQuery(this).qtip({
			content: jQuery(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
			style: {
				width: 300,
				name: 'cream', // Give it a crea mstyle to make it stand out
			},
			position: {
				corner: {
					target: 'bottomLeft',
					tooltip: 'topLeft'
				}
			}
		});
	});
	
			jQuery('#lladmin legend[tooltip]').each(function()
		{
		jQuery(this).qtip({
			content: jQuery(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
			style: {
				width: 300,
				name: 'cream', // Give it a crea mstyle to make it stand out
			},
			position: {
				corner: {
					target: 'bottomLeft',
					tooltip: 'topLeft'
				}
			}
		});
	});

});
</script>

			<?php

		} // end config_page()
	
	} // end class LL_Admin

} //endif


function PrivateLinkLibraryCategories($order = 'name', $hide_if_empty = 'obsolete', $table_width = 100, $num_columns = 1, $catanchor = true, 
							   $flatlist = false, $categorylist = '', $excludecategorylist = '', $showcategorydescheaders = false, 
							   $showonecatonly = false, $settings = '', $loadingicon = '/icons/Ajax-loader.gif') {
							   
	$output = '';
							   
	if (!isset($_GET['searchll']))
	{
		$countcat = 0;

		$order = strtolower($order);
		
		// Guess the location
		$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
			
		$output .= "<!-- Link Library Categories Output -->\n\n";
		
		$output .= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
			
		$output .= "function showLinkCat ( _incomingID, _settingsID) {\n";
		$output .= "var map = {id : _incomingID, settings : _settingsID}\n";
		$output .= "\tjQuery('#contentLoading').toggle();jQuery.get('" . WP_PLUGIN_URL . "/link-library/link-library-ajax.php', map, function(data){jQuery('#linklist" . $settings. "').replaceWith(data);jQuery('#contentLoading').toggle();initTree();});\n";
		$output .= "}\n";
			
		$output .= "</SCRIPT>\n\n";
		
		// Handle link category sorting
		$direction = 'ASC';
		if (substr($order,0,1) == '_') {
			$direction = 'DESC';
			$order = substr($order,1);
		}

		if (!isset($direction)) $direction = '';
		// Fetch the link category data as an array of hashesa

		if ($order == "catlist")
			{
				$displaycategories = explode(",",$categorylist);
				
				$catnames = array();
				
				foreach ( $displaycategories as $displaycategory ) {			
					$temp = get_categories("type=link&orderby=name&order=$direction&hierarchical=0&include=$displaycategory");
					$catnames = array_merge($catnames,$temp);							
				}
				
			}
		else
		{
			$catnames = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0&include=$categorylist&exclude=$excludecategorylist");		
		}

		// Display each category

		if ($catnames) {
			
			$output .=  "<div id=\"linktable\" class=\"linktable\">";
			
			if (!$flatlist)
				$output .= "<table width=\"" . $table_width . "%\">\n";
			else
				$output .= "<ul>\n";
				
			foreach ( (array) $catnames as $catname) {
				// Handle each category.
				// First, fix the sort_order info
				//$orderby = $cat['sort_order'];
				//$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;
				
				// Display the category name
				$countcat += 1;
				if (!$flatlist and (($countcat % $num_columns == 1) or ($num_columns == 1) )) $output .= "<tr>\n";
								
				if (!$flatlist)
					$catfront = '	<td>';
				else
					$catfront = '	<li>';
					
				if ($showonecatonly)
					$cattext = "<a href='#' onClick=\"showLinkCat('" . $catname->term_id. "', '" . $settings . "');return false;\" >";
				else if ($catanchor)
					$cattext = '<a href="#' . $catname->category_nicename . '">';
				else
					$cattext = '';
		
				$catitem =  $catname->name;
				
				if ($showcategorydescheaders)
				{
					$catname->category_description = str_replace("[", "<", $catname->category_description);
					$catname->category_description = str_replace("]", ">", $catname->category_description);
					$catitem .= $catname->category_description;				
				}
				
				if ($catanchor)
					$catitem .= "</a>";
				
				$output .= ($catfront . $cattext . $catitem );
						
				if (!$flatlist)
					$catterminator = "	</td>\n";
				else
					$catterminator = "	</li>\n";
					
				$output .= ($catterminator);
		
					
				if (!$flatlist and ($countcat % $num_columns == 0)) $output .= "</tr>\n";
			}
			
			if (!$flatlist and ($countcat % $num_columns == 3)) $output .= "</tr>\n";
			if (!$flatlist && $catnames)
				$output .= "</table>\n</div>\n";
			else if ($catnames)
				$output .= "</ul>\n</div>\n";
			
			if ($showonecatonly)
			{
				if ($loadingicon == '') $loadingicon = '/icons/Ajax-loader.gif';
				$output .= "<span class='contentLoading' id='contentLoading' style='display: none;'><img src='" . WP_PLUGIN_URL . "/link-library" . $loadingicon . "' alt='Loading data, please wait...'></span>\n";
			}
		}
		else
		{
			$output .= "<div>No categories were found that match the parameters entered in the Link Library Settings Panel! Please notify the blog author.</div>";	
		}
		
		$output .= "\n<!-- End of Link Library Categories Output -->\n\n";
	}
	return $output;
}

function highlightWords($text, $words)
{
        /*** loop of the array of words ***/
        foreach ($words as $word)
        {
                /*** quote the text for regex ***/
                $word = preg_quote($word);
                /*** highlight the words ***/
                $text = preg_replace("/($word)/i", '<span class="highlight_word">\1</span>', $text);
        }
        /*** return the text ***/
        return $text;
}


function PrivateLinkLibrary($order = 'name', $hide_if_empty = 'obsolete', $catanchor = true,
                                $showdescription = false, $shownotes = false, $showrating = false,
                                $showupdated = false, $categorylist = '', $show_images = false, 
                                $show_image_and_name = false, $use_html_tags = false, 
                                $show_rss = false, $beforenote = '<br />', $nofollow = false, $excludecategorylist = '',
								$afternote = '', $beforeitem = '<li>', $afteritem = '</li>', $beforedesc = '', $afterdesc = '',
								$displayastable = false, $beforelink = '', $afterlink = '', $showcolumnheaders = false, 
								$linkheader = '', $descheader = '', $notesheader = '', $catlistwrappers = 1, $beforecatlist1 = '', 
								$beforecatlist2 = '', $beforecatlist3 = '', $divorheader = false, $catnameoutput = 'linklistcatname',
								$show_rss_icon = false, $linkaddfrequency = 0, $addbeforelink = '', $addafterlink = '', $linktarget = '',
								$showcategorydesclinks = false, $showadmineditlinks = true, $showonecatonly = false, $AJAXcatid = '',
								$defaultsinglecat = '', $rsspreview = false, $rsspreviewcount = 3, $rssfeedinline = false,
								$rssfeedinlinecontent = false, $rssfeedinlinecount = 1, $beforerss = '', $afterrss = '',
								$rsscachedir = '', $direction = 'ASC', $linkdirection = 'ASC', $linkorder = 'name',
								$pagination = false, $linksperpage = 5, $hidecategorynames = false, $settings = '') {
								
	global $wpdb;

	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );	
	if ( !defined('WP_ADMIN_URL') )
		define( 'WP_ADMIN_URL', get_option('siteurl') . '/wp-admin');
	
	// Guess the location
	$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
	
	$currentcategory = 1;
	
	if ($showonecatonly && $AJAXcatid != '')
		$categorylist = $AJAXcatid;
	else if ($showonecatonly && $AJAXcatid == '' && $defaultsinglecat != '')
		$categorylist = $defaultsinglecat;
	
	$linkquery = "SELECT *, IF (DATE_ADD(l.link_updated, INTERVAL " . get_option('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated FROM " . $wpdb->prefix . "links l, " . $wpdb->prefix . "terms t, " . $wpdb->prefix . "term_relationships tr, ";
	$linkquery .= $wpdb->prefix. "term_taxonomy tt WHERE l.link_id = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id ";
	$linkquery .= "AND tt.taxonomy = 'link_category' AND tt.term_id = t.term_id";
	
	if ($categorylist != "")
		$linkquery .= " AND t.term_id in (" . $categorylist. ")";
		
	if ($excludecategorylist != "")
		$linkquery .= " AND t.term_id not in (" . $excludecategorylist . ")";
		
	if ($_GET['searchll'] != "")
	{
		$searchterms = explode(" ", $_GET['searchll']);
		
		if ($searchterms)
		{
			$mode = "search";
			$termnb = 1;
			
			foreach($searchterms as $searchterm)
			{
				if ($termnb == 1)
				{
					$linkquery .= " AND (link_name like '%" . $searchterm . "%' ";
					$termnb++;
				}
				else
				{
					$linkquery .= " OR link_name like '%" . $searchterm . "%' ";
				}
				
				if ($hidecategorynames == false)
					$linkquery .= " OR name like '%" . $searchterm . "%' ";
				if ($shownotes)
					$linkquery .= " OR link_notes like '%" . $searchterm . "%' ";
				if ($showdescription)
					$linkquery .= " OR link_description like '%" . $searchterm . "%' ";
			}
			
			$linkquery .= ")";			
		}
	}
	else
		$mode = "normal";
	
	if ($order == "name")
		$linkquery .= " ORDER by name " . $direction;
	elseif ($order == "id")
		$linkquery .= " ORDER by t.term_id " . $direction;
	elseif ($order == "order")
		$linkquery .= " ORDER by t.term_order " . $direction;
	elseif ($order == "catlist")
		$linkquery .= " ORDER by FIELD(t.term_id," . $categorylist . ") ";
		
	if ($linkorder == "name")
		$linkquery .= ", link_name " . $linkdirection;
	elseif ($linkorder == "id")
		$linkquery .= ", link_id " . $linkdirection;
	elseif ($linkorder == "order")
		$linkquery .= ", link_order ". $linkdirection;
		
	if ($pagination && $mode != 'search')
	{
		$quantity = $linksperpage + 1;
		
		if (isset($_GET['page']))
		{
			$pagenumber = $_GET['page'];
			$startingitem = ($pagenumber - 1) * $linksperpage;
			$linkquery .= " LIMIT " . $startingitem . ", " . $quantity;
		}
		else
		{
			$pagenumber = 1;
			$linkquery .= " LIMIT 0, " . $quantity;
		}
	}
		
	//echo $linkquery;
		
	$linkitems = $wpdb->get_results($linkquery);
	
	if ($pagination)
	{
		if (count($linkitems) > $linksperpage)
		{
			array_pop($linkitems);
			$nextpage = true;
		}
		else
			$nextpage = false;		
	}

    // Display links
	if ($linkitems) {
		$output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
		
		if ($mode == "search")
		{
			$output .= "<div class='resulttitle'>Search Results for '" . $_GET['searchll'] . "'</div>";
		}
		
		$currentcategoryid = -1;
		
		foreach ( (array) $linkitems as $linkitem) {	
	
			if ($currentcategoryid != $linkitem->term_id)
			{
				if ($currentcategoryid != -1 && $showonecatonly)
				{
					break;
				}
				if ($currentcategoryid != -1)
				{
					// Close the last category
					if ($displayastable)
						$output .= "\t</table>\n";
					else
						$output .= "\t</ul>\n";
						
					if ($catlistwrappers != '')
						$output .= "</div>";
					
					$currentcategory = $currentcategory + 1;				
				}
				
				$currentcategoryid = $linkitem->term_id;
				$linkcount = 0;
				
				if ($catlistwrappers == 1)
					$output .= "<div class=\"" . $beforecatlist1 . "\">";
				else if ($catlistwrappers == 2)
				{
					$remainder = $currentcategory % $catlistwrappers;
					switch ($remainder) {

						case 0:
							$output .= "<div class=\"" . $beforecatlist2 . "\">";						
							break;
							
						case 1:
							$output .= "<div class=\"" . $beforecatlist1 . "\">";
							break;				
					}
				}
				else if ($catlistwrappers == 3)
				{
					$remainder = $currentcategory % $catlistwrappers;
					switch ($remainder) {

						case 0:
							$output .= "<div class=\"" . $beforecatlist3 . "\">";						
							break;
							
						case 2:
							$output .= "<div class=\"" . $beforecatlist2 . "\">";
							break;
							
						case 1:
							$output .= "<div class=\"" . $beforecatlist1 . "\">";
							break;				
					}				
				}

				// Display the category name
				if ($hidecategorynames == false || $hidecategorynames == "")
				{
					if ($catanchor)
						$cattext = '<div id="' . $linkitem->slug . '">';
					else
						$cattext = '';
					
					if ($divorheader == false)
					{
						if ($mode == "search")
							$linkitem->name = highlightWords($linkitem->name, $searchterms);
							
						$catlink = '<div class="' . $catnameoutput . '">' . $linkitem->name;
						
						if ($showcategorydesclinks)
						{
							$catlink .= "<span class='linklistcatnamedesc'>";
							$linkitem->description = str_replace("[", "<", $linkitem->description);
							$linkitem->description = str_replace("]", ">", $linkitem->description);
							$catlink .= $linkitem->description;				
							$catlink .= '</span>';
						}
						
						$catlink .= "</div>";
					}
					else if ($divorheader == true)
					{
						if ($mode == "search")
							$linkitem->name = highlightWords($linkitem->name, $searchterms);
							
						$catlink = '<'. $catnameoutput . '>' . $linkitem->name;
						
						if ($showcategorydesclinks)
						{
							$catlink .= "<span class='linklistcatnamedesc'>";
							$linkitem->description = str_replace("[", "<", $linkitem->description);
							$linkitem->description = str_replace("]", ">", $linkitem->description);
							$catlink .= $linkitem->description;				
							$catlink .= '</span>';
						}
						
						$catlink .= '</' . $catnameoutput . '>';
					}
									
					if ($catanchor)
						$catenddiv = '</div>';
					else
						$catenddiv = '';
				}
					
				if ($displayastable == true)
				{
					$catstartlist = "\n\t<table class='linklisttable'>\n";
					if ($showcolumnheaders == true)
						$catstartlist .= "<div class='linklisttableheaders'><tr><th><div class='linklistcolumnheader'>".$linkheader."</div></th><th><div class='linklistcolumnheader'>".$descheader."</div></th><th><div class='linklistcolumnheader'>".$notesheader."</div></th></tr></div>\n";
					else
						$catstartlist .= '';
				}
				else
					$catstartlist = "\n\t<ul>\n";
					
				
				$output .= $cattext . $catlink . $catenddiv . $catstartlist; 
			}
			
			if ($mode == "search")
			{
				$linkitem->link_name = highlightWords($linkitem->link_name, $searchterms);
				
				if ($shownotes)
					$linkitem->link_notes = highlightWords($linkitem->link_notes, $searchterms);
				if ($showdescription)
					$linkitem->link_description = highlightWords($linkitem->link_description, $searchterms);				
			}
										
			$between = "\n";
			
			$feed = NULL;
			
			if ($rssfeedinline)
			{	
				if( !class_exists('SimplePie'))
				{
					require_once( 'simplepie.inc' );
				}
			
				$feed = new SimplePie();
				
				// We'll enable the discovering and caching of favicons.
				$feed->set_favicon_handler('./handler_image.php');
				
				$feed->set_item_limit($rssfeedinlinecount);
				
				$feed->enable_cache(true);
				if ($rsscachedir == '')
					$rsscachedir = ABSPATH . 'wp-content/cache/link-library';
				$feed->set_cache_location($rsscachedir);
				
				$feed->set_stupidly_fast(true);
				
				// We'll make sure that the right content type and character encoding gets set automatically.
				// This function will grab the proper character encoding, as well as set the content type to text/html.
				$feed->handle_content_type();
			}
							
			$linkcount = $linkcount + 1;
				
			if ($linkaddfrequency > 0)
				if (($linkcount - 1) % $linkaddfrequency == 0)
					$output .= $addbeforelink;
			
			if (!isset($linkitem->recently_updated)) $linkitem->recently_updated = false; 
			$output .= $beforeitem;
			$output .= $beforelink;
			if ($showupdated && $linkitem->recently_updated)
				$output .= get_option('links_recently_updated_prepend'); 
				
			$the_link = '#';
			if (!empty($linkitem->link_url) )
				$the_link = wp_specialchars($linkitem->link_url);

			$rel = $linkitem->link_rel;
			if ('' != $rel and !$nofollow)
				$rel = ' rel="' . $rel . '"';
			else if ('' != $rel and $nofollow)
				$rel = ' rel="' . $rel . ' nofollow"';
			else if ('' == $rel and $nofollow)
				$rel = ' rel="nofollow"';
			
			if ($use_html_tags) {
				$descnotes = $linkitem->link_notes;
			}
			else {
				$descnotes = wp_specialchars($linkitem->link_notes, ENT_QUOTES);
			}
			$desc = wp_specialchars($linkitem->link_description, ENT_QUOTES);
			$cleanname = wp_specialchars($linkitem->link_name, ENT_QUOTES);
			
			if ($mode == "search")
			{
				$descnotes = highlightWords($linkitem->link_notes, $searchterms);
				$desc = highlightWords($linkitem->link_description, $searchterms);
				$name = highlightWords($linkitem->link_name, $searchterms);
			}
			else
				$name = $cleanname;
				

			$title = wp_specialchars($linkitem->link_description, ENT_QUOTES);;

			if ($showupdated) {
			   if (substr($linkitem->link_updated_f,0,2) != '00') {
					$title .= ' ('.__('Last updated') . '  ' . date(get_option('links_updated_date_format'), $linkitem->link_updated_f + (get_option('gmt_offset') * 3600)) .')';
				}
			}

			if ('' != $title)
				$title = ' title="' . $title . '"';

			$alt = ' alt="' . $cleanname . '"';
				
			$target = $linkitem->link_target;
			if ('' != $target)
				$target = ' target="' . $target . '"';
			else 
			{
				$target = $linktarget;
				if ('' != $target)
					$target = ' target="' . $target . '"';
			}

			$output .= '<a href="' . $the_link . '"' . $rel . $title . $target. '>';
			
			if ( $linkitem->link_image != null && ($show_images || $show_image_and_name)) {
				if ( strpos($linkitem->link_image, 'http') !== false )
					$output .= "<img src=\"$linkitem->link_image\" $alt $title />";
				else // If it's a relative path
					$output .= "<img src=\"" . get_option('siteurl') . "$linkitem->link_image\" $alt $title />";
					
				if ($show_image_and_name)
					$output .= $name;
			} else {
				$output .= $name;
			}
			
			$output .= '</a>';
			
			if (($showadmineditlinks) && current_user_can("manage_links")) {
				$output .= $between . '<a href="' . WP_ADMIN_URL . '/link.php?action=edit&link_id=' . $linkitem->link_id .'">(Edit)</a>';
			}
			
			$output .= $afterlink;
			
			if ($showupdated && $linkitem->recently_updated) {
				$output .= get_option('links_recently_updated_append');
			}

			if ($use_html_tags || $mode == "search") {
				$desc = $linkitem->link_description;
			}
			else {
				$desc = wp_specialchars($linkitem->link_description, ENT_QUOTES);
			}
			
			if ($showdescription)
				$output .= $between . $beforedesc . $desc . $afterdesc;

			if ($shownotes) {
				$output .= $between . $beforenote . $descnotes . $afternote;
			}
			if ($show_rss || $show_rss_icon || $rsspreview)
				$output .= $beforerss . '<div class="rsselements">';
				
			if ($show_rss && ($linkitem->link_rss != '')) {
				$output .= $between . '<a class="rss" href="' . $linkitem->link_rss . '">RSS</a>';
			}
			if ($show_rss_icon && ($linkitem->link_rss != '')) {
				$output .= $between . '<a class="rssicon" href="' . $linkitem->link_rss . '"><img src="' . $llpluginpath . '/icons/feed-icon-14x14.png" /></a>';
			}	
			if ($rsspreview && $linkitem->link_rss != '')
			{
				$output .= $between . '<a href="' . WP_PLUGIN_URL . '/link-library/rsspreview.php?keepThis=true&linkid=' . $linkitem->link_id . '&previewcount=' . $rsspreviewcount . '&TB_iframe=true&height=500&width=700" title="Preview of RSS feed for ' . $cleanname . '" class="thickbox"><img src="' . $llpluginpath . '/icons/preview-16x16.png" /></a>';
			}
			
			if ($show_rss || $show_rss_icon || $rsspreview)
				$output .= '</div>' . $afterrss;

			
			if ($rssfeedinline && $linkitem->link_rss)
			{
				$feed->set_feed_url($linkitem->link_rss);
				
				$feed->init();				
					
					if ($feed->data && $feed->get_item_quantity() > 0)
					{
						$output .= '<div id="ll_rss_results">';
						
						$items = $feed->get_items(0, $rssfeedinlinecount);
						foreach($items as $item)
						{
							$output .= '<div class="chunk" style="padding:0 5px 5px;">';
							$output .= '<div class="rsstitle"><a target="feedwindow" href="' . $item->get_permalink() . '">' . $item->get_title() . '</a> - ' . $item->get_date("j M Y") . '</div>';
							if ($rssfeedinlinecontent) $output .= '<div class="rsscontent">' . $item->get_content() . '</div>';
							$output .= '</div>';
							$output .= '<br />';
						}
						
						$output .= '</div>';
					}
						
					
			}
			
					
			$output .= $afteritem . "\n";
			
			if ($linkaddfrequency > 0)
				if ($linkcount % $linkaddfrequency == 0)
					$output .= $addafterlink;
				
		} // end while
		
		// Close the last category
		if ($displayastable)
			$output .= "\t</table>\n";
		else
			$output .= "\t</ul>\n";
			
		if ($catlistwrappers != '')
			$output .= "</div>";
			
		if ($pagination && $mode != "search")
		{
			$previouspagenumber = $pagenumber - 1;
			$nextpagenumber = $pagenumber + 1;
			
			if ($pagenumber > 1)
				$output .= "<div class='previouspage'><a href='?page=" . $previouspagenumber . "'>&laquo; Page " . $previouspagenumber . "</a></div>";
			
			if ($nextpage)
				$output .= "<div class='nextpage'><a href='?page=" . $nextpagenumber . "'>Page " . $nextpagenumber . " &raquo;</a></div>";
			
		}
		
		$currentcategory = $currentcategory + 1;
		
		$output .= "</div>\n";
		
	}
	else
	{
		$output .= "<div>No categories were found that match the parameters entered in the Link Library Settings Panel! Please notify the blog author.</div>";	
	}
	
	$output .= "\n<!-- End of Link Library Output -->\n\n";
	
	return $output;
}

function PrivateLinkLibrarySearchForm() {

	$output = "<form method='get' id='llsearch'>\n";
	$output .= "<div>\n";
	$output .= "<input type='text' onfocus=\"this.value=''\" value='Search...' name='searchll' id='searchll' />\n";
	$output .= "<input type='submit' value='Search' />\n";
	$output .= "</div>\n";
	$output .= "</form>\n\n";
	
	return $output;
}

function PrivateLinkLibraryAddLinkForm($selectedcategorylist = '', $excludedcategorylist = '') {

	$output = "<form method='post' id='lladdlink'>\n";
	$output .= "<div class='lladdlink'>\n";
	$output .= "<div>Add new link</div>\n";
	$output .= "<table>\n";
	$output .= "<tr><td class='lladdlinkheader'>Link Name</td><td><input type='text' name='linkname' id='linkname' /></td></tr><br />\n";
	$output .= "<tr><td>Link Address</td><td><input type='text' name='linkaddress' id='linkaddress' /></td></tr><br />\n";
	$output .= "<tr><td>Link RSS</td><td><input type='text' name='linkrss' id='linkrss' /></td></tr><br />\n";
	
	$linkcats = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0&include=$selectedcategorylist&exclude=$excludedcategorylist");
	
	if ($linkcats)
	{
		$output .= "<tr><td>Link Category</td><td><SELECT name='linkcategory' id='linkcategory'>";
		foreach ($linkcats as $linkcat)
		{
			$output .= "<OPTION VALUE='" . $linkcat->category_nicename . "'>" . $linkcat->category_nicename;
		}
		
		$output .= "</SELECT></td></tr>";
	}
	
	$output .= "<tr><td>Link Description</td><td><input type='text' name='linkdesc' id='linkdesc' /></td></tr><br />\n";
	$output .= "<tr><td>Link Notes</td><td><input type='text' name='linknotes' id='linknotes' /></td></tr><br />\n";
	$output .= "</table>\n";
	$output .= "</div>\n";
	$output .= "</form>\n\n";

	return $output;
}

$version = "2.2";

$options  = get_option('LinkLibraryPP',"");

if ($options == "") {
	$newoptions = get_option('LinkLibraryPP1', "");
	
	if ($newoptions == "")
	{
		$options['order'] = 'name';
		$options['hide_if_empty'] = true;
		$options['table_width'] = 100;
		$options['num_columns'] = 1;
		$options['catanchor'] = true;
		$options['flatlist'] = false;
		$options['categorylist'] = null;
		$options['excludecategorylist'] = null;
		$options['showdescription'] = false;
		$options['shownotes'] = false;
		$options['showrating'] = false;
		$options['showupdated'] = false;
		$options['show_images'] = false;
		$options['show_image_and_name'] = false;
		$options['use_html_tags'] = false;
		$options['show_rss'] = false;
		$options['beforenote'] = '<br />';
		$options['afternote'] = '';
		$options['nofollow'] = false;
		$options['beforeitem'] = '<li>';
		$options['afteritem'] = '</li>';
		$options['beforedesc'] = '';
		$options['afterdesc'] = '';
		$options['displayastable'] = false;
		$options['beforelink'] = '';
		$options['afterlink'] = '';
		$options['showcolumnheaders'] = false;
		$options['linkheader'] = '';
		$options['descheader'] = '';
		$options['notesheader'] = '';
		$options['catlistwrappers'] = 1;
		$options['beforecatlist1'] = '';
		$options['beforecatlist2'] = '';
		$options['beforecatlist3'] = '';
		$options['divorheader'] = false;
		$options['catnameoutput'] = 'linklistcatname';
		$options['show_rss_icon'] = false;
		$options['linkaddfrequency'] = 0;
		$options['addbeforelink'] = '';
		$options['addafterlink'] = '';	
		$options['linktarget'] = '';
		$options['showcategorydescheaders'] = false;
		$options['showcategorydesclinks'] = false;
		$options['settingssetname'] = 'Default';
		$options['showadmineditlinks'] = true;
		$options['showonecatonly'] = false;
		$options['loadingicon'] = '/icons/Ajax-loader.gif';
		$options['defaultsinglecat'] = '';
		$options['rsspreview'] = false;
		$options['rsspreviewcount'] = 3;
		$options['rssfeedinline'] = false;
		$options['rssfeedinlinecontent'] = false;
		$options['rssfeedinlinecount'] = 1;
		$options['beforerss'] = '';
		$options['aftertss'] = '';
		$options['rsscachedir'] = ABSPATH . 'wp-content/cache/link-library';
		$options['direction'] = 'ASC';
		$options['linkdirection'] = 'ASC';
		$options['linkorder'] = 'name';
		$options['pagination'] = false;
		$options['linksperpage'] = 5;
		$options['hidecategorynames'] = false;
		
		update_option('LinkLibraryPP1',$options);
		
		$genoptions['stylesheet'] = 'stylesheet.css';
		
		update_option('LinkLibraryGeneral', $genoptions);
	}
} 
else
{
	/* Upgrading Options to Link Library 2.0 Format from 1.x */
	$options['settingssetname'] = "Imported Set 1";
	update_option('LinkLibraryPP1', $options);
	
	delete_option('LinkLibraryPP');
}

/*
 * function LinkLibraryCategories()
 *
 * added by Yannick Lefebvre
 *
 * Output a list of all links categories, listed by category, using the
 * settings in $wpdb->linkcategories and output it as table
 *
 * Parameters:
 *   order (default 'name')  - Sort link categories by 'name' or 'id' or 'category-list'. When set to 'AdminSettings', will use parameters set in Admin Settings Panel.
 *   hide_if_empty (default true)  - Supress listing empty link categories
 *   table_witdh (default 100) - Width of table, percentage
 *   num_columns (default 1) - Number of columns in table
 *   catanchor (default true) - Determines if links to generated anchors should be created
 *   flatlist (default false) - When set to true, displays an unordered list instead of a table
 *   categorylist (default null) - Specifies a comma-separate list of the only categories that should be displayed
 *   excludecategorylist (default null) - Specifies a comma-separate list of the categories that should not be displayed
 *   showcategorydescheaders (default null) - Show category descriptions in category list
 *   showonecatonly (default false) - Enable AJAX mode showing only one category at a time
 *   settings (default NULL) - Settings Set ID, only used when showonecatonly is true
 *   loadingicon (default NULL) - Path to icon to display when only show one category at a time
 */

function LinkLibraryCategories($order = 'name', $hide_if_empty = 'obsolete', $table_width = 100, $num_columns = 1, $catanchor = true, 
							   $flatlist = false, $categorylist = '', $excludecategorylist = '', $showcategorydescheaders = false,
							   $showonecatonly = false, $settings = '', $loadingicon = '/icons/Ajax-loader.gif') {
	
	if ($order == 'AdminSettings1' || $order == 'AdminSettings2' || $order == 'AdminSettings3' || $order == 'AdminSettings4' || $order == 'AdminSettings5')
	{
		if ($order == 'AdminSettings1')
			$options = get_option('LinkLibraryPP1');
		else if ($order == 'AdminSettings2')
			$options = get_option('LinkLibraryPP2');
		else if ($order == 'AdminSettings3')
			$options = get_option('LinkLibraryPP3');			
		else if ($order == 'AdminSettings4')
			$options = get_option('LinkLibraryPP4');			
		else if ($order == 'AdminSettings5')
			$options = get_option('LinkLibraryPP5');

		return PrivateLinkLibraryCategories($options['order'], true, $options['table_width'], $options['num_columns'], $options['catanchor'], $options['flatlist'],
								 $options['categorylist'], $options['excludecategorylist'], $options['showcategorydescheaders'], $options['showonecatonly'], '',
								 $options['loadingicon']);   
	}
	else
		return PrivateLinkLibraryCategories($order, true, $table_width, $num_columns, $catanchor, $flatlist, $categorylist, $excludecategorylist, $showcategorydescheaders,
		$showonecatonly, $settings, $loadingicon);   
	
}

/*
 * function LinkLibrary()
 *
 * added by Yannick Lefebvre
 *
 * Output a list of all links, listed by category, using the
 * settings in $wpdb->linkcategories and output it as a nested
 * HTML unordered list. Can also insert anchors for categories
 *
 * Parameters:
 *   order (default 'name')  - Sort link categories by 'name' or 'id'. When set to 'AdminSettings', will use parameters set in Admin Settings Panel.
 *   hide_if_empty (default true)  - Supress listing empty link categories
 *   catanchor (default true) - Adds name anchors to categorie links to be able to link directly to categories\
 *   showdescription (default false) - Displays link descriptions. Added for 2.1 since link categories no longer have this setting
 *   shownotes (default false) - Shows notes in addition to description for links (useful since notes field is larger than description)
 *   showrating (default false) - Displays link ratings. Added for 2.1 since link categories no longer have this setting
 *   showupdated (default false) - Displays link updated date. Added for 2.1 since link categories no longer have this setting
 *   categorylist (default null) - Only show links inside of selected categories. Enter category numbers in a string separated by commas
 *   showimages (default false) - Displays link images. Added for 2.1 since link categories no longer have this setting
 *   show_image_and_name (default false) - Show both image and name instead of only one or the other
 *   use_html_tags (default false) - Use HTML tags for formatting instead of just displaying them
 *   show_rss (default false) - Display RSS URI if available in link description
 *   beforenote (default <br />) - Code to print out between the description and notes
 *   nofollow (default false) - Adds nofollow tag to outgoing links
 *   excludecategorylist (default null) - Specifies a comma-separate list of the categories that should not be displayed
 *   afternote (default null) - Code / Text to be displayed after note
 *   beforeitem (default null) - Code / Text to be displayed before item
 *   afteritem (default null) - Code / Text to be displayed after item
 *   beforedesc (default null) - Code / Text to be displayed before description
 *   afterdesc (default null) - Code / Text to be displayed after description
 *   displayastable (default false) - Display lists of links as a table (when true) or as an unordered list (when false)
 *   beforelink (default null) - Code / Text to be displayed before link
 *   afterlink (default null) - Code / Text to be displayed after link
 *   showcolumnheaders (default false) - Show column headers if rendering in table mode
 *   linkheader (default null) - Text to be shown in link column when displaying as table
 *   descheader (default null) - Text to be shown in desc column when displaying as table
 *   notesheader (default null) - Text to be shown in notes column when displaying as table
 *   catlistwrappers (default 1) - Number of different sets of alternating elements to be placed before and after each link category section
 *   beforecatlist1 (default null) - First element to be placed before a link category section
 *   beforecatlist2 (default null) - Second element to be placed before a link category section
 *   beforecatlist3 (default null) - Third element to be placed before a link category section
 *   divorheader (default false) - Output div before and after cat name if false, output heading tag if true
 *   catnameoutput (default linklistcatname) - Name of div class or heading to output
 *   showrssicon (default false) - Output RSS URI if available and assign to standard RSS icon
 *   linkaddfrequency (default 0) - Frequency at which extra before and after output should be placed around links
 *   addbeforelink (default null) - Addition output to be placed before link
 *   addafterlink (default null) - Addition output to be placed after link
 *   linktarget (default null) - Specifies the link target window
 *   showcategorydescheaders (default false) - Display link category description when printing category list
 *   showcategorydesclinks (default false) - Display link category description when printing links
 *   showadmineditlinks (default false) - Display edit links in output if logged in as administrator
 *   showonecatonly (default false) - Only show one category at a time
 *   AJAXcatid (default null) - Category ID for AJAX sub-queries
 *   defaultsinglecat (default null) - ID of first category to be shown in single category mode
 *   rsspreview (default false) - Add preview links after RSS feed addresses
 *   rssfeedpreviewcount(default 3) - Number of RSS feed items to show in preview
 *   rssfeedinline (default false) - Shows latest feed items inline with link list
 *   rssfeedinlinecontent (default false) - Shows latest feed items contents inline with link list
 *   rssfeedinlinecount (default 1) - Number of RSS feed items to show inline
 *   beforerss (default null) - String to output before RSS block
 *   afterrss (default null) - String to output after RSS block
 *   rsscachedir (default null) - Path for SimplePie library to store RSS cache information
 *   direction (default ASC) - Sort direction for Link Categories
 *   linkdirection (default ASC) - Sort direction for Links within each category
 *   linkorder (default 'name') - Sort order for Links within each category
 *   pagination (default false) - Limit number of links displayed per page
 *   linksperpage (default 5) - Number of links to be shown per page in Pagination Mode
 *   hidecategorynames (default false) - Show category names in Link Library list
 *   settings (default NULL) - Setting Set ID
 */

function LinkLibrary($order = 'name', $hide_if_empty = 'obsolete', $catanchor = true,
                                $showdescription = false, $shownotes = false, $showrating = false,
                                $showupdated = false, $categorylist = '', $show_images = false, 
                                $show_image_and_name = false, $use_html_tags = false, 
                                $show_rss = false, $beforenote = '<br />', $nofollow = false, $excludecategorylist = '',
								$afternote = '', $beforeitem = '<li>', $afteritem = '</li>', $beforedesc = '', $afterdesc = '',
								$displayastable = false, $beforelink = '', $afterlink = '', $showcolumnheaders = false, 
								$linkheader = '', $descheader = '', $notesheader = '', $catlistwrappers = 1, $beforecatlist1 = '', 
								$beforecatlist2 = '', $beforecatlist3 = '', $divorheader = false, $catnameoutput = 'linklistcatname',
								$show_rss_icon = false, $linkaddfrequency = 0, $addbeforelink = '', $addafterlink = '', $linktarget = '',
								$showcategorydesclinks = false, $showadmineditlinks = true, $showonecatonly = false, $AJAXcatid = '',
								$defaultsinglecat = '', $rsspreview = false, $rsspreviewcount = 3, $rssfeedinline = false, $rssfeedinlinecontent = false,
								$rssfeedinlinecount = 1, $beforerss = '', $afterrss = '', $rsscachedir = '', $direction = 'ASC', 
								$linkdirection = 'ASC', $linkorder = 'name', $pagination = false, $linksperpage = 5, $hidecategorynames = false,
								$settings = '') {
								
	if ($order == 'AdminSettings1' || $order == 'AdminSettings2' || $order == 'AdminSettings3' || $order == 'AdminSettings4' || $order == 'AdminSettings5')
	{
		if ($order == 'AdminSettings1')
		{
			$options = get_option('LinkLibraryPP1');
			$settings = 1;
		}
		else if ($order == 'AdminSettings2')
		{
			$options = get_option('LinkLibraryPP2');
			$settings = 2;
		}
		else if ($order == 'AdminSettings3')
		{
			$options = get_option('LinkLibraryPP3');			
			$settings = 3;
		}
		else if ($order == 'AdminSettings4')
		{
			$options = get_option('LinkLibraryPP4');			
			$settings = 4;
		}
		else if ($order == 'AdminSettings5')
		{
			$options = get_option('LinkLibraryPP5');			
			$settings = 5;
		}

		return PrivateLinkLibrary($options['order'], TRUE, $options['catanchor'], $options['showdescription'], $options['shownotes'],
								  $options['showrating'], $options['showupdated'], $options['categorylist'], $options['show_images'],
								  $options['show_image_and_name'], $options['use_html_tags'], $options['show_rss'], $options['beforenote'],
								  $options['nofollow'], $options['excludecategorylist'], $options['afternote'], $options['beforeitem'],
								  $options['afteritem'], $options['beforedesc'], $options['afterdesc'], $options['displayastable'],
								  $options['beforelink'], $options['afterlink'], $options['showcolumnheaders'], $options['linkheader'],
								  $options['descheader'], $options['notesheader'], $options['catlistwrappers'], $options['beforecatlist1'], 
								  $options['beforecatlist2'], $options['beforecatlist3'], $options['divorheader'], $options['catnameoutput'],
								  $options['show_rss_icon'], $options['linkaddfrequency'], $options['addbeforelink'], $options['addafterlink'],
								  $options['linktarget'], $options['showcategorydesclinks'], $options['showadmineditlinks'], $options['showonecatonly'],
								  $AJAXcatid, $options['defaultsinglecat'], $options['rsspreview'], $options['rsspreviewcount'], $options['rssfeedinline'],
								  $options['rssfeedinlinecontent'], $options['rssfeedinlinecount'], $options['beforerss'], $options['afterrss'],
								  $options['rsscachedir'], $options['direction'], $options['linkdirection'], $options['linkorder'],
								  $options['pagination'], $options['linksperpage'], $options['hidecategorynames'], $settings);
	
	}
	else
		return PrivateLinkLibrary($order, $hide_if_empty, $catanchor, $showdescription, $shownotes, $showrating,
                                $showupdated, $categorylist, $show_images, $show_image_and_name, $use_html_tags, 
                                $show_rss, $beforenote, $nofollow, $excludecategorylist, $afternote, $beforeitem, $afteritem,
								$beforedesc, $afterdesc, $displayastable, $beforelink, $afterlink, $showcolumnheaders, 
								$linkheader, $descheader, $notesheader, $catlistwrappers, $beforecatlist1, 
								$beforecatlist2, $beforecatlist3, $divorheader, $catnameoutput, $show_rss_icon,
								$linkaddfrequency, $addbeforelink, $addafterlink, $linktarget, $showcategorydesclinks, $showadmineditlinks,
								$showonecatonly, '', $defaultsinglecat, $rsspreview, $rsspreviewcount, $rssfeedinline, $rssfeedinlinecontent, $rssfeedinlinecount,
								$beforerss, $afterrss, $rsscachedir, $direction, $linkdirection, $linkorder,
								$pagination, $linksperpage, $hidecategorynames, $settings);

}



function link_library_cats_func($atts) {
	extract(shortcode_atts(array(
	    'categorylistoverride' => '',
		'excludecategoryoverride' => '',
		'settings' => ''
	), $atts));
	
	if ($settings == '')
	{
		$settings = 1;
		$options = get_option('LinkLibraryPP1');
	}
	else
	{
		$settingsname = 'LinkLibraryPP' . $settings;
		$options = get_option($settingsname);
	}
	
	if ($categorylistoverride != '')
		$selectedcategorylist = $categorylistoverride;
	else
		$selectedcategorylist = $options['categorylist'];
		
	if ($excludecategoryoverride != '')
		$excludedcategorylist = $excludecategoryoverride;
	else
		$excludedcategorylist = $options['excludecategorylist'];

	return PrivateLinkLibraryCategories($options['order'], true, $options['table_width'], $options['num_columns'], $options['catanchor'], $options['flatlist'],
								 $selectedcategorylist, $excludedcategorylist, $options['showcategorydescheaders'], $options['showonecatonly'], $settings,
								 $options['loadingicon']);
}

function link_library_search_func($atts) {
	extract(shortcode_atts(array(
	), $atts));
	
	return PrivateLinkLibrarySearchForm();
}

function link_library_addlink_func($atts) {
	extract(shortcode_atts(array(
		'settings' => '',
		'categorylistoverride' => '',
		'excludecategoryoverride' => ''
	), $atts));
	
	if ($settings == '')
		$options = get_option('LinkLibraryPP1');
	else
	{
		$settingsname = 'LinkLibraryPP' . $settings;
		$options = get_option($settingsname);
	}
	
	if ($categorylistoverride != '')
		$selectedcategorylist = $categorylistoverride;
	else
		$selectedcategorylist = $options['categorylist'];
		
	if ($excludecategoryoverride != '')
		$excludedcategorylist = $excludecategoryoverride;
	else
		$excludedcategorylist = $options['excludecategorylist'];
	
	return PrivateLinkLibraryAddLinkForm($selectedcategorylist, $excludedcategorylist);	
}


function link_library_func($atts) {
	extract(shortcode_atts(array(
	    'categorylistoverride' => '',
		'excludecategoryoverride' => '',
		'notesoverride' => '',
		'descoverride' => '',
		'rssoverride' => '',
		'tableoverride' => '',
		'settings' => ''
	), $atts));

	if ($settings == '')
		$options = get_option('LinkLibraryPP1');
	else
	{
		$settingsname = 'LinkLibraryPP' . $settings;
		$options = get_option($settingsname);
	}
	
	if ($notesoverride != '')
		$selectedshownotes = $notesoverride;
	else
		$selectedshownotes = $options['shownotes'];
	
	if ($descoverride != '')
		$selectedshowdescription = $descoverride;
	else
		$selectedshowdescription = $options['showdescription'];

	if ($rssoverride != '')
		$selectedshowrss = $rssoverride;
	else
		$selectedshowrss = $options['show_rss'];					
		
	if ($categorylistoverride != '')
		$selectedcategorylist = $categorylistoverride;
	else
		$selectedcategorylist = $options['categorylist'];
		
	if ($excludecategoryoverride != '')
		$excludedcategorylist = $excludecategoryoverride;
	else
		$excludedcategorylist = $options['excludecategorylist'];	
		
	if ($tableoverride != '')
		$overridedisplayastable = $tableoverride;
	else
		$overridedisplayastable = $options['displayastable'];
		
	return PrivateLinkLibrary($options['order'], TRUE, $options['catanchor'], $selectedshowdescription, $selectedshownotes,
								  $options['showrating'], $options['showupdated'], $selectedcategorylist, $options['show_images'],
								  $options['show_image_and_name'], $options['use_html_tags'], $options['show_rss'], $options['beforenote'],
								  $options['nofollow'], $excludedcategorylist, $options['afternote'], $options['beforeitem'],
								  $options['afteritem'], $options['beforedesc'], $options['afterdesc'], $overridedisplayastable,
								  $options['beforelink'], $options['afterlink'], $options['showcolumnheaders'], $options['linkheader'],
								  $options['descheader'], $options['notesheader'], 	$options['catlistwrappers'], $options['beforecatlist1'], 
								  $options['beforecatlist2'], $options['beforecatlist3'], $options['divorheader'], $options['catnameoutput'],
								  $options['show_rss_icon'], $options['linkaddfrequency'], $options['addbeforelink'], $options['addafterlink'],
								  $options['linktarget'], $options['showcategorydesclinks'], $options['showadmineditlinks'],
								  $options['showonecatonly'], '', $options['defaultsinglecat'], $options['rsspreview'], $options['rsspreviewcount'], 
								  $options['rssfeedinline'], $options['rssfeedinlinecontent'], $options['rssfeedinlinecount'],
								  $options['beforerss'], $options['afterrss'], $options['rsscachedir'], $options['direction'],
								  $options['linkdirection'], $options['linkorder'], $options['pagination'], $options['linksperpage'],
								  $options['hidecategorynames'], $settings);
}

function link_library_header() {
	$genoptions = get_option('LinkLibraryGeneral');
	
	if ($genoptions == "")
		$genoptions['stylesheet'] = 'stylesheet.css';
		
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . WP_PLUGIN_URL . '/link-library/' . $genoptions['stylesheet'] . '"/>';	
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . WP_PLUGIN_URL . '/link-library/thickbox/thickbox.css"/>';
}

function link_library_init() {
	wp_enqueue_script('thickbox', get_bloginfo('wpurl') . '/wp-content/plugins/link-library/thickbox/thickbox.js');
	wp_enqueue_script('qtip', get_bloginfo('wpurl') . '/wp-content/plugins/link-library/jquery-qtip/jquery.qtip-1.0.0-rc3.min.js');
}  

add_shortcode('link-library-cats', 'link_library_cats_func');

add_shortcode('link-library-search', 'link_library_search_func');

add_shortcode('link-library-addlink', 'link_library_addlink_func');

add_shortcode('link-library', 'link_library_func');

wp_enqueue_script('jquery');

add_action('wp_head', 'link_library_header');

// adds the menu item to the admin interface
add_action('admin_menu', array('LL_Admin','add_config_page'));

add_action('init', 'link_library_init');

?>
