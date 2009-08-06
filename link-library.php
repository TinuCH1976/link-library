<?php
/*
Plugin Name: Link Library
Plugin URI: http://wordpress.org/extend/plugins/link-library/
Description: Functions to generate link library page with a list of link
categories with hyperlinks to the actual link lists. Other options are
the ability to display notes on top of descriptions, to only display
selected categories and to display names of links at the same time
as their related images.
Version: 2.3
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
				add_submenu_page('plugins.php', 'Link Library for Wordpress', 'Link Library', 9, basename(__FILE__), array('LL_Admin','config_page'));
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
				$settings_link = '<a href="plugins.php?page=link-library.php">' . __('Settings') . '</a>';
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

			if ( isset($_POST['submit1']) || isset($_POST['submit2']) || isset($_POST['submit3']) || isset($_POST['submit4']) || isset($_POST['submit5'])) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the Link Library for WordPress options.'));
				check_admin_referer('linklibrarypp-config');
				
				foreach (array('order', 'table_width', 'num_columns', 'categorylist', 'excludecategorylist', 'beforenote', 'afternote','position',
							   'beforeitem', 'afteritem', 'beforedesc', 'afterdesc', 'beforelink','afterlink', 'beforecatlist1',
							   'beforecatlist2', 'beforecatlist3','catnameoutput', 'linkaddfrequency', 'addbeforelink', 'addafterlink',
							   'defaultsinglecat', 'rsspreviewcount', 'rssfeedinlinecount','beforerss','afterrss') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = strtolower($_POST[$option_name]);
					}
				}
				
				foreach (array('linkheader', 'descheader', 'notesheader','linktarget', 'settingssetname', 'loadingicon') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = $_POST[$option_name];
					}
				}
				
				foreach (array('hide_if_empty', 'catanchor', 'showdescription', 'shownotes', 'showrating', 'showupdated', 'show_images', 
								'show_image_and_name', 'use_html_tags', 'show_rss', 'nofollow','showcolumnheaders','show_rss_icon', 'showcategorydescheaders',
								'showcategorydesclinks', 'showadmineditlinks', 'showonecatonly', 'rsspreview', 'rssfeedinline', 'rssfeedinlinecontent') as $option_name) {
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

					update_option($settingsname,$options);
				}	
				
			}
					
			$options1 = get_option('LinkLibraryPP1');
			$options2 = get_option('LinkLibraryPP2');
			$options3 = get_option('LinkLibraryPP3');
			$options4 = get_option('LinkLibraryPP4');
			$options5 = get_option('LinkLibraryPP5');
			?>		
			<div class="wrap">
				<h2>Link Library Configuration</h2>
				<form name="lladminform" action="" method="post" id="analytics-conf">
					<div>Link Library supports the creation of 5 different setting sets to display links on your pages.</div>
					<div>A single set of settings is created by default, with the user being able to create up to five sets.</div>
					<div>The first set cannot be deleted.</div>
					<div>To use a specific settings set on a page, use the syntax [link-library-cats settings=1] [link-library settings=1], changing 1 for the right set number.</div>
					<br />
					<ul>
						<li><?php if ($settings == 1) {echo '<img src="' . $llpluginpath . '/icons/next-16x16.png" />';} ?><a href="?page=link-library.php&amp;settings=1"> Settings Set #1<?php if ($options1 != "") echo ' - ' . $options1['settingssetname']; ?></a></li>
						<li><?php if ($settings == 2) {echo '<img src="' . $llpluginpath . '/icons/next-16x16.png" />';} if ($options2 != "") {echo '<a href="?page=link-library.php&amp;settings=2">';} else {echo '<a href="?page=link-library.php&amp;settings=2"><img src="' . $llpluginpath . '/icons/add-16x16.png" /></a>';} ?> Settings Set #2<?php if ($options2 != "") echo ' - ' . $options2['settingssetname']; ?><?php if ($options2 != "") {echo '</a> <a href="?page=link-library.php&amp;deletesettings=2"><img src="' . $llpluginpath . '/icons/delete-16x16.png" /></a>';}  ?></li>
						<li><?php if ($settings == 3) {echo '<img src="' . $llpluginpath . '/icons/next-16x16.png" />';} if ($options3 != "") {echo '<a href="?page=link-library.php&amp;settings=3">';} else {echo '<a href="?page=link-library.php&amp;settings=3"><img src="' . $llpluginpath . '/icons/add-16x16.png" /></a>';} ?> Settings Set #3<?php if ($options3 != "") echo ' - ' . $options3['settingssetname']; ?><?php if ($options3 != "") {echo '</a> <a href="?page=link-library.php&amp;deletesettings=3"><img src="' . $llpluginpath . '/icons/delete-16x16.png" /></a>';}  ?></li>
						<li><?php if ($settings == 4) {echo '<img src="' . $llpluginpath . '/icons/next-16x16.png" />';} if ($options4 != "") {echo '<a href="?page=link-library.php&amp;settings=4">';} else {echo '<a href="?page=link-library.php&amp;settings=4"><img src="' . $llpluginpath . '/icons/add-16x16.png" /></a>';} ?> Settings Set #4<?php if ($options4 != "") echo ' - ' . $options4['settingssetname']; ?><?php if ($options4 != "") {echo '</a> <a href="?page=link-library.php&amp;deletesettings=4"><img src="' . $llpluginpath . '/icons/delete-16x16.png" /></a>';}  ?></li>
						<li><?php if ($settings == 5) {echo '<img src="' . $llpluginpath . '/icons/next-16x16.png" />';} if ($options5 != "") {echo '<a href="?page=link-library.php&amp;settings=5">';} else {echo '<a href="?page=link-library.php&amp;settings=5"><img src="' . $llpluginpath . '/icons/add-16x16.png" /></a>';} ?> Settings Set #5<?php if ($options5 != "") echo ' - ' . $options5['settingssetname']; ?><?php if ($options5 != "") {echo '</a> <a href="?page=link-library.php&amp;deletesettings=5"><img src="' . $llpluginpath . '/icons/delete-16x16.png" /></a>';}  ?></li>
					</ul>
					<p style="border:0;" class="submit"><input type="submit" name="submit<?php echo $settings; ?>" value="Update Settings &raquo;" /></p>
					<table class="form-table" style="width:100%;">
					<?php
					if ( function_exists('wp_nonce_field') )
						wp_nonce_field('linklibrarypp-config');
					?>
					<tr><td><h3>Common Parameters</h3></td></tr>
					<tr>
						<th scope="row" valign="top">
							<label for="settingssetname">Settings Set Name</label>
						</th>
						<td>
							<input type="text" id="settingssetname" name="settingssetname" size="40" value="<?php echo $options['settingssetname']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="order">Results Order</label>
						</th>
						<td>
							<select name="order" id="order" style="width:350px;">
								<option value="name"<?php if ($options['order'] == 'name') { echo ' selected="selected"';} ?>>Order by Name</option>
								<option value="id"<?php if ($options['order'] == 'id') { echo ' selected="selected"';} ?>>Order by ID</option>
								<option value="catlist"<?php if ($options['order'] == 'catlist') { echo ' selected="selected"';} ?>>Order of categories based on included category list</option>
								<option value="order"<?php if ($options['order'] == 'order') { echo ' selected="selected"';} ?>>Order set by 'My Link Order' Wordpress Plugin</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="categorylist">Categories to be displayed (comma-separated numeric category IDs)</label>
						</th>
						<td>
							<input type="text" id="categorylist" name="categorylist" size="40" value="<?php echo $options['categorylist']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="excludecategorylist">Categories to be excluded (comma-separated numeric category IDs)</label>
						</th>
						<td>
							<input type="text" id="excludecategorylist" name="excludecategorylist" size="40" value="<?php echo $options['excludecategorylist']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="showonecatonly">Only show one category at a time (using AJAX queries)</label>
						</th>
						<td>
							<input type="checkbox" id="showonecatonly" name="showonecatonly" <?php if ($options['showonecatonly']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="defaultsinglecat">Default category to be shown when only showing one at a time (numeric ID)</label>
						</th>
						<td>
							<input type="text" id="defaultsinglecat" name="defaultsinglecat" size="4" value="<?php echo $options['defaultsinglecat']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							Icon to display when performing AJAX queries (relative to Tune Library plugin directory)
						</th>
						<td>
							<input type="text" id="loadingicon" name="loadingicon" size="40" value="<?php if ($options['loadingicon'] == '') {echo '/icons/Ajax-loader.gif';} else {echo strval($options['loadingicon']);} ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>					
					<tr>
						<th scope="row" valign="top">
							<label for="hide_if_empty">Hide Results if Empty</label>
						</th>
						<td>
							<input type="checkbox" id="hide_if_empty" name="hide_if_empty" <?php if ($options['hide_if_empty']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr><td><h3>Link Categories Settings</h3></td></tr>
					<tr>
						<th scope="row" valign="top">
							<label for="flatlist">Link Categories Display Format</label>
						</th>
						<td>
							<select name="flatlist" id="flatlist" style="width:200px;">
								<option value="false"<?php if ($options['flatlist'] == false) { echo ' selected="selected"';} ?>>Table</option>
								<option value="true"<?php if ($options['flatlist'] == true) { echo ' selected="selected"';} ?>>Unordered List</option>
							</select>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="showcategorydescheaders">Show Category Description (Use [ and ] instead of < and > for HTML codes)</></label>
						</th>
						<td>
							<input type="checkbox" id="showcategorydescheaders" name="showcategorydescheaders" <?php if ($options['showcategorydescheaders']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>			
					<tr>
						<th scope="row" valign="top">
							<label for="table_width">Width of Categories Table in Percents</label>
						</th>
						<td>
							<input type="text" id="table_width" name="table_width" size="10" value="<?php echo strval($options['table_width']); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="num_columns">Number of columns in Categories Table</label>
						</th>
						<td>
							<input type="text" id="num_columns" name="num_columns" size="10" value="<?php echo strval($options['num_columns']); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="divorheader">Use Div Class or Heading tag around Category Names</label>
						</th>
						<td>
							<select name="divorheader" id="divorheader" style="width:200px;">
								<option value="false"<?php if ($options['divorheader'] == false) { echo ' selected="selected"';} ?>>Div Class</option>
								<option value="true"<?php if ($options['divorheader'] == true) { echo ' selected="selected"';} ?>>Heading Tag</option>
							</select>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="catnameoutput">Div Class Name (e.g. linklistcatname) or Heading label (e.g h3)</label>
						</th>
						<td>
							<input type="text" id="catnameoutput" name="catnameoutput" size="30" value="<?php echo strval($options['catnameoutput']); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="catlistwrappers">Number of different sets of alternating div classes to be placed before and after each link category section</label>
						</th>
						<td>
							<select name="catlistwrappers" id="catlistwrappers" style="width:200px;">
								<option value="1"<?php if ($options['catlistwrappers'] == 1) { echo ' selected="selected"';} ?>>1</option>
								<option value="2"<?php if ($options['catlistwrappers'] == 2) { echo ' selected="selected"';} ?>>2</option>
								<option value="3"<?php if ($options['catlistwrappers'] == 3) { echo ' selected="selected"';} ?>>3</option>
							</select>
						</td>
						
					</tr>					
					<tr>
						<th scope="row" valign="top">
							<label for="beforecatlist1">First div class name</label>
						</th>
						<td>
							<input type="text" id="beforecatlist1" name="beforecatlist1" size="40" value="<?php echo $options['beforecatlist1']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>					
					<tr>
						<th scope="row" valign="top">
							<label for="beforecatlist2">Second div class name</label>
						</th>
						<td>
							<input type="text" id="beforecatlist2" name="beforecatlist2" size="40" value="<?php echo $options['beforecatlist2']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>		
					<tr>
						<th scope="row" valign="top">
							<label for="beforecatlist3">Third div class name</label>
						</th>
						<td>
							<input type="text" id="beforecatlist3" name="beforecatlist3" size="40" value="<?php echo $options['beforecatlist3']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>							
					<tr><td><h3>Link Element Settings</h3></td></tr>
					<tr>
						<th scope="row" valign="top">
							<label for="showcategorydesclinks">Show Category Description (Use [ and ] instead of < and > for HTML codes)</></label>
						</th>
						<td>
							<input type="checkbox" id="showcategorydesclinks" name="showcategorydesclinks" <?php if ($options['showcategorydesclinks']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>						
					<tr>
						<th scope="row" valign="top">
							<label for="catanchor">Embed HTML anchors (need to be active for Link Categories to work)</label>
						</th>
						<td>
							<input type="checkbox" id="catanchor" name="catanchor" <?php if ($options['catanchor']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="linktarget">Link Target (sets default link target window, does not override specific targets set in links)</label>
						</th>
						<td>
							<input type="text" id="linktarget" name="linktarget" size="40" value="<?php echo $options['linktarget']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="displayastable">Link Display Format</label>
						</th>
						<td>
							<select name="displayastable" id="displayastable" style="width:200px;">
								<option value="true"<?php if ($options['displayastable'] == true) { echo ' selected="selected"';} ?>>Table</option>
								<option value="false"<?php if ($options['displayastable'] == false) { echo ' selected="selected"';} ?>>Unordered List</option>
							</select>
						</td>
					</tr>				
					<tr>
						<th scope="row" valign="top">
							<label for="showcolumnheaders">Show Column Headers</label>
						</th>
						<td>
							<input type="checkbox" id="showcolumnheaders" name="showcolumnheaders" <?php if ($options['showcolumnheaders']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="linkheader">Link Column Header</label>
						</th>
						<td>
							<input type="text" id="linkheader" name="linkheader" size="40" value="<?php echo $options['linkheader']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="descheader">Description Column Header</label>
						</th>
						<td>
							<input type="text" id="descheader" name="descheader" size="40" value="<?php echo $options['descheader']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="notesheader">Notes Column Header</label>
						</th>
						<td>
							<input type="text" id="notesheader" name="notesheader" size="40" value="<?php echo $options['notesheader']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="helpline1">Map of user-customizable fields</label>
						</th>
						<td>
							<img src="<?php echo $llpluginpath; ?>/HelpLine1.jpg"/>
						</td>
					</tr>					
					<tr>
						<th scope="row" valign="top">
							<label for="beforeitem">1A - Output before complete link group (link, notes, desc, etc...)</label>
						</th>
						<td>
							<input type="text" id="beforeitem" name="beforeitem" size="40" value="<?php echo $options['beforeitem']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afteritem">1B - Output after complete link group (link, notes, desc, etc...)</label>
						</th>
						<td>
							<input type="text" id="afteritem" name="afteritem" size="40" value="<?php echo $options['afteritem']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="linkaddfrequency">Frequency of additional output before and after complete link group</label>
						</th>
						<td>
							<input type="text" id="linkaddfrequency" name="linkaddfrequency" size="10" value="<?php echo strval($options['linkaddfrequency']); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>				
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="helpline1">Map of user-customizable fields with additional output</label>
						</th>
						<td>
							<img src="<?php echo $llpluginpath; ?>/HelpLine2.jpg"/>
						</td>
					</tr>						
					<tr>
						<th scope="row" valign="top">
							<label for="addbeforelink">5A - Additional Output before complete link group</label>
						</th>
						<td>
							<input type="text" id="addbeforelink" name="addbeforelink" size="40" value="<?php echo $options['addbeforelink']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="addafterlink">5B - Additional Output after link group</label>
						</th>
						<td>
							<input type="text" id="addafterlink" name="addafterlink" size="40" value="<?php echo $options['addafterlink']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>						
					<tr>
						<th scope="row" valign="top">
							<label for="beforelink">2A - Output before Link</label>
						</th>
						<td>
							<input type="text" id="beforelink" name="beforelink" size="40" value="<?php echo $options['beforelink']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afterlink">2B - Output after Link</label>
						</th>
						<td>
							<input type="text" id="afterlink" name="afterlink" size="40" value="<?php echo $options['afterlink']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="showdescription">Show Link Descriptions</label>
						</th>
						<td>
							<input type="checkbox" id="showdescription" name="showdescription" <?php if ($options['showdescription']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="beforedesc">3A - Output before Link Description</label>
						</th>
						<td>
							<input type="text" id="beforedesc" name="beforedesc" size="40" value="<?php echo $options['beforedesc']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afterdesc">3B - Output after Link Description</label>
						</th>
						<td>
							<input type="text" id="afternote" name="afterdesc" size="40" value="<?php echo $options['afterdesc']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="shownotes">Show Link Notes</label>
						</th>
						<td>
							<input type="checkbox" id="shownotes" name="shownotes" <?php if ($options['shownotes']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="beforenote">4A - Output before Link Note</label>
						</th>
						<td>
							<input type="text" id="beforenote" name="beforenote" size="40" value="<?php echo $options['beforenote']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afternote">4B - Output after Link Note</label>
						</th>
						<td>
							<input type="text" id="afternote" name="afternote" size="40" value="<?php echo $options['afternote']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>									
					<tr>
						<th scope="row" valign="top">
							<label for="showrating">Show Link Rating</label>
						</th>
						<td>
							<input type="checkbox" id="showrating" name="showrating" <?php if ($options['showrating']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="showupdated">Show Link Updated Flag</label>
						</th>
						<td>
							<input type="checkbox" id="showupdated" name="showupdated" <?php if ($options['showupdated']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="show_images">Show Link Images</label>
						</th>
						<td>
							<input type="checkbox" id="show_images" name="show_images" <?php if ($options['show_images']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="show_image_and_name">Show Link Image and Name</label>
						</th>
						<td>
							<input type="checkbox" id="show_image_and_name" name="show_image_and_name" <?php if ($options['show_image_and_name']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="use_html_tags">Use HTML tags for formatting</label>
						</th>
						<td>
							<input type="checkbox" id="use_html_tags" name="use_html_tags" <?php if ($options['use_html_tags']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="beforerss">Output before RSS Section</label>
						</th>
						<td>
							<input type="text" id="beforerss" name="beforerss" size="40" value="<?php echo $options['beforerss']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afterrss">Output after RSS Section</label>
						</th>
						<td>
							<input type="text" id="afterrss" name="afterrss" size="40" value="<?php echo $options['afterrss']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>						
					<tr>
						<th scope="row" valign="top">
							<label for="show_rss">Show RSS Link using Text</label>
						</th>
						<td>
							<input type="checkbox" id="show_rss" name="show_rss" <?php if ($options['show_rss']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="show_rss_icon">Show RSS Link using Standard Icon</label>
						</th>
						<td>
							<input type="checkbox" id="show_rss_icon" name="show_rss_icon" <?php if ($options['show_rss_icon']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="rsspreview">Show RSS Preview Link</label>
						</th>
						<td>
							<input type="checkbox" id="rsspreview" name="rsspreview" <?php if ($options['rsspreview']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="rsspreviewcount">Number of articles shown in RSS Preview</label>
						</th>
						<td>
							<input type="text" id="rsspreviewcount" name="rsspreviewcount" size="2" value="<?php if ($options['rsspreviewcount'] == '') echo '3'; else echo strval($options['rsspreviewcount']); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>				
					</tr>					
					<tr>
						<th scope="row" valign="top">
							<label for="rssfeedinline">Show RSS Feed Headers in Link Library output</label>
						</th>
						<td>
							<input type="checkbox" id="rssfeedinline" name="rssfeedinline" <?php if ($options['rssfeedinline']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>			
					<tr>
						<th scope="row" valign="top">
							<label for="rssfeedinlinecontent">Show RSS Feed Content in Link Library output</label>
						</th>
						<td>
							<input type="checkbox" id="rssfeedinlinecontent" name="rssfeedinlinecontent" <?php if ($options['rssfeedinlinecontent']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="rssfeedinlinecount">Number of RSS articles shown in Link Library Output</label>
						</th>
						<td>
							<input type="text" id="rssfeedinlinecount" name="rssfeedinlinecount" size="2" value="<?php if ($options['rssfeedinlinecount'] == '') echo '1'; else echo strval($options['rssfeedinlinecount']); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>				
					</tr>					
					<tr>
						<th scope="row" valign="top">
							<label for="nofollow">Add nofollow tag to outgoing links</label>
						</th>
						<td>
							<input type="checkbox" id="nofollow" name="nofollow" <?php if ($options['nofollow']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="showadmineditlinks">Show edit links when logged in as editor or administrator</label>
						</th>
						<td>
							<input type="checkbox" id="showadmineditlinks" name="showadmineditlinks" <?php if ($options['showadmineditlinks'] || $options['showadmineditlinks'] == '') echo ' checked="checked" '; ?>/>
						</td>
					</tr>					

					
					</table>
					<p style="border:0;" class="submit"><input type="submit" name="submit<?php echo $settings; ?>" value="Update Settings &raquo;" /></p>
					
					<p><a href="?page=link-library.php&amp;reset=<?php echo $settings; ?>">Reset current Settings Set</a></p>
					
					<p><a href="?page=link-library.php&amp;resettable=<?php echo $settings; ?>">Reset current Setting Set for table layout</a></p>
					
					<p>Copy settings from: <?php if ($settings != 1) { echo '<a href="?page=link-library.php&amp;copy=' . $settings . '&source=1">Settings Set 1</a> ';} ?>
					<?php if ($settings != 2) { echo '<a href="?page=link-library.php&amp;copy=' . $settings . '&source=2">Settings Set 2</a> ';} ?>
					<?php if ($settings != 3) { echo '<a href="?page=link-library.php&amp;copy=' . $settings . '&source=3">Settings Set 3</a> ';} ?>
					<?php if ($settings != 4) { echo '<a href="?page=link-library.php&amp;copy=' . $settings . '&source=4">Settings Set 4</a> ';} ?>
					<?php if ($settings != 5) { echo '<a href="?page=link-library.php&amp;copy=' . $settings . '&source=5">Settings Set 5</a> ';} ?>
					</p>
				</form>
			</div>
			<?php

		} // end config_page()
	
	} // end class LL_Admin

} //endif


function PrivateLinkLibraryCategories($order = 'name', $hide_if_empty = 'obsolete', $table_width = 100, $num_columns = 1, $catanchor = true, 
							   $flatlist = false, $categorylist = '', $excludecategorylist = '', $showcategorydescheaders = false, 
							   $showonecatonly = false, $settings = '', $loadingicon = '/icons/Ajax-loader.gif') {
	
	$countcat = 0;

	$order = strtolower($order);
	
	// Guess the location
	$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
		
	$output = "<!-- Link Library Categories Output -->\n\n";
	
	$output .= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
		
	$output .= "function showLinkCat ( _incomingID, _settingsID) {\n";
	$output .= "var map = {id : _incomingID, settings : _settingsID}\n";
	$output .= "\tjQuery('#contentLoading').toggle();jQuery.get('" . WP_PLUGIN_URL . "/link-library/link-library-ajax.php', map, function(data){jQuery('#linklist').replaceWith(data);initTree();jQuery('#contentLoading').toggle();});\n";
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
				$cattext = "<a href='#' onClick=\"showLinkCat('" . $catname->term_id. "', '" . $settings . "');\" >";
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
	
	return $output;
}


function get_links_notes($category = '', $before = '', $after = '<br />',
                   $between = ' ', $show_images = true, $orderby = 'name',
                   $show_description = true, $show_rating = false,
                   $limit = -1, $show_updated = 1, $show_notes = false, $show_image_and_name = false, $use_html_tags = false, 
				   $show_rss = false, $beforenote = '<br />', $afternote = '', $nofollow = false, $echo = true,
				   $beforedesc = '', $afterdesc = '', $beforelink = '', $afterlink = '', $show_rss_icon = false,
				   $linkaddfrequency = 0, $addbeforelink = '', $addafterlink = '', $linktarget = '', $showadmineditlinks = true,
				   $rsspreview = false, $rsspreviewcount = 3, $rssfeedinline = false, $rssfeedinlinecontent = false, $rssfeedinlinecount = 1,
				   $beforerss = '', $afterrss = '') {
				   
	global $wpdb;
	
	// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

if ( !defined('WP_ADMIN_URL') )
    define( 'WP_ADMIN_URL', get_option('siteurl') . '/wp-admin');
	
if ( !defined('WP_CONTENT_DIR') )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

// Guess the location
$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';

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
		$feed->set_cache_location('/home/amcyel/public_html/yannickcorner/wp-content/cache/link-library');
		
		$feed->set_stupidly_fast(true);
		
		// We'll make sure that the right content type and character encoding gets set automatically.
		// This function will grab the proper character encoding, as well as set the content type to text/html.
		$feed->handle_content_type();
	}

	$order = 'ASC';
	if ( substr($orderby, 0, 1) == '_' ) {
		$order = 'DESC';
		$orderby = substr($orderby, 1);
	}

	if ( $category == -1 ) //get_bookmarks uses '' to signify all categories
		$category = '';
		
	$catidquery = "select term_id from " . $wpdb->prefix . "terms where name = '" . $wpdb->escape($category) . "'";
	
	$catids = $wpdb->get_results($catidquery);
	
	if ($catids)
	{
		foreach ( (array) $catids as $catid)
		{
			$catidnumber = $catid->term_id;
			$results = get_bookmarks("category=$catidnumber&orderby=$orderby&order=$order&show_updated=$show_updated&limit=$limit");
		}
	}

	if ( !$results )
	{
		$output = "<div>This category does not contain any links</div>";
		return $output;
	}
		
	$linkcount = 0;
		
	$output = '';
	
    foreach ( (array) $results as $row) {
	
		$linkcount = $linkcount + 1;
		
		if ($linkaddfrequency > 0)
			if (($linkcount - 1) % $linkaddfrequency == 0)
				$output .= $addbeforelink;
		
		if (!isset($row->recently_updated)) $row->recently_updated = false;
        $output .= $before;
		$output .= $beforelink;
        if ($show_updated && $row->recently_updated)
            $output .= get_option('links_recently_updated_prepend');
			
        $the_link = '#';
        if (!empty($row->link_url) )
            $the_link = wp_specialchars($row->link_url);

        $rel = $row->link_rel;
		if ('' != $rel and !$nofollow)
            $rel = ' rel="' . $rel . '"';
		else if ('' != $rel and $nofollow)
            $rel = ' rel="' . $rel . ' nofollow"';
		else if ('' == $rel and $nofollow)
			$rel = ' rel="nofollow"';
		
		if ($use_html_tags) {
			$descnotes = $row->link_notes;
		}
		else {
			$descnotes = wp_specialchars($row->link_notes, ENT_QUOTES);
		}
		$desc = wp_specialchars($row->link_description, ENT_QUOTES);
        $name = wp_specialchars($row->link_name, ENT_QUOTES);

        $title = $desc;

        if ($show_updated) {
           if (substr($row->link_updated_f,0,2) != '00') {
                $title .= ' ('.__('Last updated') . '  ' . date(get_option('links_updated_date_format'), $row->link_updated_f + (get_option('gmt_offset') * 3600)) .')';
            }
        }

        if ('' != $title)
            $title = ' title="' . $title . '"';

        $alt = ' alt="' . $name . '"';
            
        $target = $row->link_target;
        if ('' != $target)
            $target = ' target="' . $target . '"';
		else 
		{
			$target = $linktarget;
			if ('' != $target)
				$target = ' target="' . $target . '"';
		}

        $output .= '<a href="' . $the_link . '"' . $rel . $title . $target. '>';
		
        if ( $row->link_image != null && ($show_images || $show_image_and_name)) {
			if ( strpos($row->link_image, 'http') !== false )
				$output .= "<img src=\"$row->link_image\" $alt $title />";
			else // If it's a relative path
				$output .= "<img src=\"" . get_option('siteurl') . "$row->link_image\" $alt $title />";
				
			if ($show_image_and_name)
				$output .= $name;
		} else {
			$output .= $name;
		}
		
        $output .= '</a>';
		
		if (($showadmineditlinks || $showadmineditlinks == '') && current_user_can("manage_links")) {
			$output .= $between . '<a href="' . WP_ADMIN_URL . '/link.php?action=edit&link_id=' . $row->link_id .'">(Edit)</a>';
		}
		
		$output .= $afterlink;
		
        if ($show_updated && $row->recently_updated) {
            $output .= get_option('links_recently_updated_append');
        }

		if ($use_html_tags) {
			$desc = $row->link_description;
		}
		else {
			$desc = wp_specialchars($row->link_description, ENT_QUOTES);
		}
		
        if ($show_description && ($desc != ''))
            $output .= $between . $beforedesc . $desc . $afterdesc;

		if (!$show_notes || ($descnotes == '')) {
			$output .= $beforenote;
		}

		if ($show_notes && ($descnotes != '')) {
			$output .= $beforenote . $between . $descnotes . $afternote;
		}
		if ($show_rss || $show_rss_icon || $rsspreview)
			$output .= $beforerss . '<div class="rsselements">';
			
		if ($show_rss && ($row->link_rss != '')) {
		    $output .= $between . '<a class="rss" href="' . $row->link_rss . '">RSS</a>';
		}
		if ($show_rss_icon && ($row->link_rss != '')) {
		    $output .= $between . '<a class="rssicon" href="' . $row->link_rss . '"><img src="' . $llpluginpath . '/icons/feed-icon-14x14.png" /></a>';
		}	
		if ($rsspreview && $row->link_rss != '')
		{
			$output .= $between . '<a href="' . WP_PLUGIN_URL . '/link-library/rsspreview.php?keepThis=true&linkid=' . $row->link_id . '&previewcount=' . $rsspreviewcount . '&TB_iframe=true&height=500&width=700" title="Preview of RSS feed for ' . $name . '" class="thickbox"><img src="' . $llpluginpath . '/icons/preview-16x16.png" /></a>';
		}
		
		if ($show_rss || $show_rss_icon || $rsspreview)
			$output .= '</div>' . $afterrss;

		
		if ($rssfeedinline)
		{
			$feed->set_feed_url($row->link_rss);
			
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
		
				
        $output .= $after . "\n";
		
		if ($linkaddfrequency > 0)
			if ($linkcount % $linkaddfrequency == 0)
				$output .= $addafterlink;
			
    } // end while
	
	return $output;
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
								$rssfeedinlinecontent = false, $rssfeedinlinecount = 1, $beforerss = '', $afterrss = '') {

	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	
	$order = strtolower($order);

	// Handle link category sorting
	$direction = 'ASC';	
	if ('_' == substr($order,0,1)) {
		$direction = 'DESC';
		$order = substr($order,1);
	}

	if (!isset($direction)) $direction = '';
	
	$currentcategory = 1;
	
	if ($showonecatonly && $AJAXcatid != '')
		$categorylist = $AJAXcatid;
	else if ($showonecatonly && $AJAXcatid == '' && $defaultsinglecat != '')
		$categorylist = $defaultsinglecat;

	// Fetch the link category data as an array of hashes
	
	if ($order == "catlist")
		{
			$displaycategories = explode(",", $categorylist);
			
			$catnames = array();
			
			foreach ( $displaycategories as $displaycategory ) {
				$temp = get_categories("type=link&orderby=name&order=$direction&hierarchical=0&include=$displaycategory");
				$catnames = array_merge($catnames,$temp);	
			}
			$order = "name";
		}
	else
	{
		$catnames = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0&include=$categorylist&exclude=$excludecategorylist");
	
	}

    // Display each category
	if ($catnames) {
		$output .= "<div id='linklist' class='linklist'>\n";
				
		if ($showonecatonly)
		{
			$catnames = array($catnames[0]);		
		}
		
		foreach ( (array) $catnames as $catname) {
		
		
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
				if ($catanchor)
					$cattext = '<div id="' . $catname->category_nicename . '">';
				else
					$cattext = '';
				
				if ($divorheader == false)
				{
					$catlink = '<div class="' . $catnameoutput . '">' . $catname->name;
					
					if ($showcategorydesclinks)
					{
						$catlink .= "<span class='linklistcatnamedesc'>";
						$catname->category_description = str_replace("[", "<", $catname->category_description);
						$catname->category_description = str_replace("]", ">", $catname->category_description);
						$catlink .= $catname->category_description;				
						$catlink .= '</span>';
					}
					
					$catlink .= "</div>";
				}
				else if ($divorheader == true)
				{
					$catlink = '<'. $catnameoutput . '>' . $catname->name;
					
					if ($showcategorydesclinks)
					{
						$catlink .= "<span class='linklistcatnamedesc'>";
						$catname->category_description = str_replace("[", "<", $catname->category_description);
						$catname->category_description = str_replace("]", ">", $catname->category_description);
						$catlink .= $catname->category_description;				
						$catlink .= '</span>';
					}
					
					$catlink .= '</' . $catnameoutput . '>';
				}
				
				if ($catanchor)
					$catenddiv = '</div>';
				else
					$catenddiv = '';
					
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
				
				
				// Call get_links() with all the appropriate params
				$linklist = get_links_notes($catname->name,
					$beforeitem,$afteritem,"\n",
					$show_images,
					$order,
					$showdescription,
					$showrating,
					-1,
					$showupdated,
					$shownotes,
					$show_image_and_name,
					$use_html_tags,
					$show_rss,
					$beforenote,
					$afternote,
					$nofollow,
					1,
					$beforedesc,
					$afterdesc,
					$beforelink,
					$afterlink,
					$show_rss_icon,
					$linkaddfrequency,
					$addbeforelink,
					$addafterlink,
					$linktarget,
					$showadmineditlinks,
					$rsspreview,
					$rsspreviewcount,
					$rssfeedinline,
					$rssfeedinlinecontent,
					$rssfeedinlinecount,
					$beforerss,
					$afterrss);
					
				$output .= $linklist;
								
				// Close the last category
				if ($displayastable)
					$output .= "\t</table>\n";
				else
					$output .= "\t</ul>\n";
					
				if ($catlistwrappers != '')
					$output .= "</div>";
				
				$currentcategory = $currentcategory + 1;
		}
		$output .= "</div>\n";
		
	}
	else
	{
		$output .= "<div>No categories were found that match the parameters entered in the Link Library Settings Panel! Please notify the blog author.</div>";	
	}
	
	$output .= "\n<!-- End of Link Library Output -->\n\n";
	
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
		
		update_option('LinkLibraryPP1',$options);
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
								$rssfeedinlinecount = 1, $beforerss = '', $afterrss = '') {
								
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
								  $options['rssfeedinlinecontent'], $options['rssfeedinlinecount'], $options['beforerss'], $options['afterrss']);
	
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
								$beforerss, $afterrss);

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
								  $options['beforerss'], $options['afterrss']);
}

function link_library_header() {
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . WP_PLUGIN_URL . '/link-library/stylesheet.css"/>';	
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . WP_PLUGIN_URL . '/link-library/thickbox/thickbox.css"/>';
}

function link_library_init() {
	wp_enqueue_script('thickbox', get_bloginfo('wpurl') . '/wp-content/plugins/link-library/thickbox/thickbox.js');
}  

add_shortcode('link-library-cats', 'link_library_cats_func');

add_shortcode('link-library', 'link_library_func');

wp_enqueue_script('jquery');

add_action('wp_head', 'link_library_header');

// adds the menu item to the admin interface
add_action('admin_menu', array('LL_Admin','add_config_page'));

add_action('init', 'link_library_init');

?>
