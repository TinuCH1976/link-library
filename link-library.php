<?php
/*
Plugin Name: Link Library
Plugin URI: http://wordpress.org/extend/plugins/link-library/
Description: Display links on pages with a variety of options
Version: 3.2.6
Author: Yannick Lefebvre
Author URI: http://yannickcorner.nayanna.biz/

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright © 2009 Yannick Lefebvre

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

require_once(ABSPATH . 'wp-admin/includes/bookmark.php');

define('LLDIR', dirname(__FILE__) . '/');  

global $rss_settings;

$rss_settings = "";

function http_get_file($url) {
    $url_stuff = parse_url($url);
    $port = isset($url_stuff['port']) ? $url_stuff['port']:80;
    $fp = fsockopen($url_stuff['host'], $port);
    $query = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n";
    $query .= 'Host: ' . $url_stuff['host'];
    $query .= "\n\n";
    fwrite($fp, $query);
    
    while ($line = fread($fp, 1024)) {
      $buffer .= $line;
    }
    
    preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts);
    return substr($buffer, - $parts[1]);
 }
              

if ( ! class_exists( 'LL_Admin' ) ) {

	class LL_Admin {

		function add_config_page() {
			global $wpdb;
			if ( function_exists('add_submenu_page') ) {
				add_options_page('Link Library for Wordpress', 'Link Library', 9, basename(__FILE__), array('LL_Admin','config_page'));
				add_filter( 'plugin_action_links', array( 'LL_Admin', 'filter_plugin_actions'), 10, 2 );
			}
		} // end add_LL_config_page()

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
					$options['showinvisible'] = false;
					$options['showdate'] = false;
					$options['beforedate'] = '';
					$options['afterdate'] = '';
					$options['catdescpos'] = 'right';
					$options['catlistdescpos'] = 'right';	
					$options['showuserlinks'] = false;
					$options['addnewlinkmsg'] = "Add new link";
					$options['linknamelabel'] = "Link name";
					$options['linkaddrlabel'] = "Link address";
					$options['linkrsslabel'] = "Link RSS";
					$options['linkcatlabel'] = "Link Category";
					$options['linkdesclabel'] = "Link Description";
					$options['linknoteslabel'] = "Link Notes";
					$options['addlinkbtnlabel'] = "Add Link";
					$options['newlinkmsg'] = "New link submitted";
					$options['moderatemsg'] = "it will appear in the list once moderated. Thank you.";
					$options['rsspreviewwidth'] = 900;
					$options['rsspreviewheight'] = 700;
					$options['beforeimage'] = '';
					$options['afterimage'] = '';
					$options['imagepos'] = 'beforename';
					$options['imageclass'] = '';
					$options['emailnewlink'] = false;
					$options['showaddlinkrss'] = false;
					$options['showaddlinkdesc'] = false;
					$options['showaddlinkcat'] = false;
					$options['showaddlinknotes'] = false;
					$options['usethumbshotsforimages'] = false;
					$options['addlinkreqlogin'] = false;
					$options['showcatlinkcount'] = false;
					$options['publishrssfeed'] = false;
					$options['numberofrssitems'] = 10;
					$options['rssfeedtitle'] = 'Link Library-Generated RSS Feed';
					$options['rssfeeddescription'] = 'Description of Link Library-Generated Feed';
					$options['showonecatmode'] = 'AJAX';
										
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
					$options['showinvisible'] = false;
					$options['showdate'] = false;
					$options['beforedate'] = '';
					$options['afterdate'] = '';
					$options['catdescpos'] = 'right';
					$options['catlistdescpos'] = 'right';
					$options['showuserlinks'] = false;	
					$options['addnewlinkmsg'] = "Add new link";
					$options['linknamelabel'] = "Link name";
					$options['linkaddrlabel'] = "Link address";
					$options['linkrsslabel'] = "Link RSS";
					$options['linkcatlabel'] = "Link Category";
					$options['linkdesclabel'] = "Link Description";
					$options['linknoteslabel'] = "Link Notes";
					$options['addlinkbtnlabel'] = "Add Link";
					$options['newlinkmsg'] = "New link submitted";
					$options['moderatemsg'] = "it will appear in the list once moderated. Thank you.";		
					$options['rsspreviewwidth'] = 900;
					$options['rsspreviewheight'] = 700;
					$options['beforeimage'] = '';
					$options['afterimage'] = '';
					$options['imagepos'] = 'beforename';
					$options['imageclass'] = '';
					$options['emailnewlink'] = false;
					$options['showaddlinkrss'] = false;
					$options['showaddlinkdesc'] = false;
					$options['showaddlinkcat'] = false;
					$options['showaddlinknotes'] = false;
					$options['usethumbshotsforimages'] = false;
					$options['addlinkreqlogin'] = false;
					$options['showcatlinkcount'] = false;
					$options['publishrssfeed'] = false;
					$options['numberofrssitems'] = 10;
					$options['rssfeedtitle'] = 'Link Library-Generated RSS Feed';
					$options['rssfeeddescription'] = 'Description of Link Library-Generated Feed';
					$options['showonecatmode'] = 'AJAX';

					$settings = $_GET['resettable'];
					$settingsname = 'LinkLibraryPP' . $settings;
					update_option($settingsname, $options);		
			}
			if ( isset($_GET['genthumbs']) || isset($_GET['genfavicons'])) {
				global $wpdb;
				
				if (isset($_GET['genthumbs']))
					$filepath = "link-library-images";
				elseif (isset($_GET['genfavicons']))
					$filepath = "link-library-favicons";
				
				if (!file_exists(ABSPATH . 'wp-content/plugins/' . $filepath))
				{
					echo "<div id='message' class='updated fade'><p><strong>Please create a folder called " . $filepath . " under your Wordpress plugins directory with write permissions to use this functionality.</strong></p></div>";				
				}
				else
				{
					if (isset($_GET['genthumbs']))
						$settings = $_GET['genthumbs'];
					elseif (isset($_GET['genfavicons']))
						$settings = $_GET['genfavicons'];
						
					$settingsname = 'LinkLibraryPP' . $settings;
					$options = get_option($settingsname);
					
					$linkquery = "SELECT distinct * ";
					$linkquery .= "FROM " . $wpdb->prefix . "terms t ";
					$linkquery .= "LEFT JOIN " . $wpdb->prefix . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
					$linkquery .= "LEFT JOIN " . $wpdb->prefix . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
					$linkquery .= "LEFT JOIN " . $wpdb->prefix . "links l ON (tr.object_id = l.link_id) ";
					$linkquery .= "WHERE tt.taxonomy = 'link_category' ";
					
					if ($options['categorylist'] != "")
						$linkquery .= " AND t.term_id in (" . $options['categorylist'] . ")";
					
					$linkitems = $wpdb->get_results($linkquery);
					
					if ($linkitems)
					{
						$filescreated = 0;
						$totallinks = count($linkitems);
						foreach($linkitems as $linkitem)
						{
							if ($linkitem->link_url != "" && $linkitem->link_name != "")
							{
								if (isset($_GET['genthumbs']))
									$genthumburl = "http://open.thumbshots.org/image.aspx?url=" . wp_specialchars($linkitem->link_url);
								elseif (isset($_GET['genfavicons']))
								{
									$strippedurl = str_replace("http://", "", wp_specialchars($linkitem->link_url));
									$genthumburl = "http://www.getfavicon.org/?url=" . $strippedurl . "/favicon.png";
								}
								
								$linkname = htmlspecialchars_decode($linkitem->link_name, ENT_QUOTES);
								$linkname = str_replace(" ", "", $linkname);
								$linkname = str_replace(".", "", $linkname);
								$linkname = str_replace("/", "-", $linkname);
									
								$imagedata = file_get_contents($genthumburl);
								$status = file_put_contents(ABSPATH . "/wp-content/plugins/" . $filepath. "/" . $linkname . ".jpg", $imagedata);
								
								if ($status)
									$filescreated++;
								
								$newimagedata = array("link_id" => $linkitem->link_id, "link_image" => "/wp-content/plugins/" . $filepath . "/" . $linkname . ".jpg");
								wp_update_link($newimagedata);
							}
						}
						
						if (isset($_GET['genthumbs']))
							echo "<div id='message' class='updated fade'><p><strong>Thumbnails successfully generated!</strong></p></div>";
						elseif (isset($_GET['genfavicons']))
							echo "<div id='message' class='updated fade'><p><strong>Favicons successfully generated!</strong></p></div>";
					}
					
				}
			}
			if ( isset($_GET['settings'])) {
				$settings = $_GET['settings'];				
			}
			else
			{
				$settings = 1;
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
				
				foreach (array('stylesheet', 'numberstylesets', 'includescriptcss') as $option_name) {
					if (isset($_POST[$option_name])) {
						$genoptions[$option_name] = $_POST[$option_name];
					}
				}
				
				foreach (array('debugmode') as $option_name) {
					if (isset($_POST[$option_name])) {
						$genoptions[$option_name] = true;
					} else {
						$genoptions[$option_name] = false;
					}
				}
				
				update_option('LinkLibraryGeneral', $genoptions);
				
			}
			if ( isset($_POST['submit'])) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the Link Library for WordPress options.'));
				check_admin_referer('linklibrarypp-config');
				
				$settingsetid = $_POST['settingsetid'];
				$settings = $_POST['settingsetid'];
				
				foreach (array('order', 'table_width', 'num_columns', 'categorylist', 'excludecategorylist', 'beforenote', 'afternote','position',
							   'beforeitem', 'afteritem', 'beforedesc', 'afterdesc', 'beforelink','afterlink', 'beforecatlist1',
							   'beforecatlist2', 'beforecatlist3','catnameoutput', 'linkaddfrequency', 'addbeforelink', 'addafterlink',
							   'defaultsinglecat', 'rsspreviewcount', 'rssfeedinlinecount','beforerss','afterrss','linksperpage', 'catdescpos',
							   'beforedate', 'afterdate', 'catlistdescpos', 'rsspreviewwidth', 'rsspreviewheight', 'beforeimage', 'afterimage', 'numberofrssitems') 
							   as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = strtolower($_POST[$option_name]);
					}
				}
				
				foreach (array('linkheader', 'descheader', 'notesheader','linktarget', 'settingssetname', 'loadingicon','rsscachedir',
								'direction', 'linkdirection', 'linkorder', 'addnewlinkmsg', 'linknamelabel', 'linkaddrlabel', 'linkrsslabel',
								'linkcatlabel', 'linkdesclabel', 'linknoteslabel', 'addlinkbtnlabel', 'newlinkmsg', 'moderatemsg', 'imagepos',
								'imageclass', 'rssfeedtitle', 'rssfeeddescription', 'showonecatmode') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = $_POST[$option_name];
					}
				}
				
				foreach (array('hide_if_empty', 'catanchor', 'showdescription', 'shownotes', 'showrating', 'showupdated', 'show_images', 
								'show_image_and_name', 'use_html_tags', 'show_rss', 'nofollow','showcolumnheaders','show_rss_icon', 'showcategorydescheaders',
								'showcategorydesclinks', 'showadmineditlinks', 'showonecatonly', 'rsspreview', 'rssfeedinline', 'rssfeedinlinecontent',
								'pagination', 'hidecategorynames', 'showinvisible', 'showdate', 'showuserlinks', 'emailnewlink', 'usethumbshotsforimages',
								'addlinkreqlogin', 'showcatlinkcount', 'publishrssfeed') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = true;
					} else {
						$options[$option_name] = false;
					}
				}
				
				foreach(array('flatlist', 'displayastable', 'divorheader','showaddlinkrss', 'showaddlinkdesc', 'showaddlinkcat', 'showaddlinknotes') as $option_name) {
					if ($_POST[$option_name] == 'true')
						$options[$option_name] = true;
					elseif ($_POST[$option_name] == 'false')
						$options[$option_name] = false;				
				}
				
				foreach (array('catlistwrappers') as $option_name)
				{
					if (isset($_POST[$option_name])) {
						$options[$option_name] = (int)($_POST[$option_name]);
					}				
				}
				
				$settingsname = 'LinkLibraryPP' . $settingsetid;
				
				update_option($settingsname, $options);
				echo "<div id='message' class='updated fade'><p><strong>Settings Set " . $settingsetid . " Updated!</strong></p></div>";
				
				global $wpdb;
					
				if ($options['categorylist'] != '')
				{
					$categoryids = explode(',', $options['categorylist']);
					
					foreach($categoryids as $categoryid)
					{
						$linkcatquery = "SELECT distinct t.name, t.term_id, t.slug as category_nicename, tt.description as category_description ";
						$linkcatquery .= "FROM " . $wpdb->prefix . "terms t, " . $wpdb->prefix. "term_taxonomy tt ";
						
						if ($hide_if_empty)
							$linkcatquery .= ", " . $wpdb->prefix . "term_relationships tr, " . $wpdb->prefix . "links l ";
						
						$linkcatquery .= "WHERE t.term_id = tt.term_id AND tt.taxonomy = 'link_category'";
										
						$linkcatquery .= " AND t.term_id = " . $categoryid;
																		
						$catnames = $wpdb->get_results($linkcatquery);

						if (!$catnames)
						{
							echo '<br /><br />Included Category ID ' . $categoryid . ' is invalid. Please check the ID in the Link Category editor.';
						}
					}
				}
				
				if ($options['excludecategorylist'] != '')
				{			
					$categoryids = explode(',', $options['excludecategorylist']);
					
					foreach($categoryids as $categoryid)
					{
						$linkcatquery = "SELECT distinct t.name, t.term_id, t.slug as category_nicename, tt.description as category_description ";
						$linkcatquery .= "FROM " . $wpdb->prefix . "terms t, " . $wpdb->prefix. "term_taxonomy tt ";
						
						if ($hide_if_empty)
							$linkcatquery .= ", " . $wpdb->prefix . "term_relationships tr, " . $wpdb->prefix . "links l ";
						
						$linkcatquery .= "WHERE t.term_id = tt.term_id AND tt.taxonomy = 'link_category'";
										
						$linkcatquery .= " AND t.term_id = " . $categoryid;
							
						$catnames = $wpdb->get_results($linkcatquery);
						
						if (!$catnames)
						{
							echo '<br /><br />Excluded Category ID ' . $categoryid . ' is invalid. Please check the ID in the Link Category editor.';
						}
					}
				}
				echo '</p></div>';
			}
			
			// Pre-2.6 compatibility
			if ( !defined('WP_CONTENT_URL') )
				define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
			if ( !defined('WP_CONTENT_DIR') )
				define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
			if ( !defined('WP_ADMIN_URL') )
				define( 'WP_ADMIN_URL', get_option('siteurl') . '/wp-admin');

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
				$options['showinvisible'] = false;
				$options['showdate'] = false;
				$options['beforedate'] = '';
				$options['afterdate'] = '';
				$options['catdescpos'] = 'right';	
				$options['catlistdescpos'] = 'right';
				$options['showuserlinks'] = false;
				$options['addnewlinkmsg'] = "Add new link";
				$options['linknamelabel'] = "Link name";
				$options['linkaddrlabel'] = "Link address";
				$options['linkrsslabel'] = "Link RSS";
				$options['linkcatlabel'] = "Link Category";
				$options['linkdesclabel'] = "Link Description";
				$options['linknoteslabel'] = "Link Notes";
				$options['addlinkbtnlabel'] = "Add Link";
				$options['newlinkmsg'] = "New link submitted";
				$options['moderatemsg'] = "it will appear in the list once moderated. Thank you.";
				$options['rsspreviewwidth'] = 900;
				$options['rsspreviewheight'] = 700;
				$options['beforeimage'] = '';
				$options['afterimage'] = '';
				$options['imagepos'] = 'beforename';
				$options['imageclass'] = '';
				$options['emailnewlink'] = false;
				$options['addlinkreqlogin'] = false;
				$options['showcatlinkcount'] = false;
				$options['publishrssfeed'] = false;
				$options['numberofrssitems'] = 10;
				$options['rssfeedtitle'] = 'Link Library-Generated RSS Feed';
				$options['rssfeeddescription'] = 'Description of Link Library-Generated Feed';
				$options['showonecatmode'] = 'AJAX';

				update_option($settingsname,$options);
			}	
			
			$genoptions = get_option('LinkLibraryGeneral');
				
			if ($genoptions == "")
			{
				$genoptions['stylesheet'] = 'stylesheet.css';
				$genoptions['numberstylesets'] = 5;
				$genoptions['includescriptcss'] = '';
				$genoptions['debugmode'] = false;
				update_option('LinkLibraryGeneral', $genoptions);
			}
			
			if (isset($_POST['approvelinks']))
			{
				global $wpdb;
				
				$section = 'moderate';
				
				foreach ($_POST['links'] as $approved_link)
				{
					$linkdescquery = "SELECT link_description ";
					$linkdescquery .= "FROM " . $wpdb->prefix . "links l ";
					$linkdescquery .= "WHERE link_id = " . $approved_link;
					
					$linkdesc = $wpdb->get_var($linkdescquery); 
					
					$modpos = strpos($linkdesc, "LinkLibrary:AwaitingModeration:RemoveTextToApprove");
					
					if ($modpos)
					{
						$startpos = $modpos + 51;
						$newlinkdesc = substr($linkdesc, $startpos);
						
						$id = array("id" => $linkdescquery);
						$newdesc = array ("link_description", $newlinkdesc);
						
						$tablename = $wpdb->prefix . "links";
						$wpdb->update( $tablename, array( 'link_description' => $newlinkdesc ), array( 'link_id' => $approved_link ));

					}
				}
				
				echo "<div id='message' class='updated fade'><p><strong>Link(s) Approved</strong></p></div>";			
			}
			
			if (isset($_POST['deletelinks']))
			{
				global $wpdb;
				
				$section = 'moderate';
				
				foreach ($_POST['links'] as $approved_link)
				{
					$wpdb->query("DELETE FROM " . $wpdb->prefix . "links WHERE link_id = " . $approved_link);
				}
				
				echo "<div id='message' class='updated fade'><p><strong>Link(s) Deleted</strong></p></div>";			
			}
			
			if ($_GET['section'] == 'moderate')
			{
				$section = 'moderate';
			}
			
			if ($section == 'moderate') {	
			?>
			<SCRIPT LANGUAGE="JavaScript">
				function checkAll(field) {
					for (i = 0; i < field.length; i++)
					field[i].checked = true;
				}

				function uncheckAll(field) {
					for (i = 0; i < field.length; i++)
					field[i].checked = false;
				}
			</script>

			<div class="wrap" id='llmoderate' style='width:1000px'>
				<h2>Link Library - Link Moderation</h2>
				<a href="<?php echo WP_ADMIN_URL ?>/options-general.php?page=link-library.php">Configuration Page</a> | <a href="http://yannickcorner.nayanna.biz/wordpress-plugins/link-library/" target="linklibrary"><img src="<?php echo $llpluginpath; ?>/icons/btn_donate_LG.gif" /></a> | <a target='llinstructions' href='http://wordpress.org/extend/plugins/link-library/installation/'>Installation Instructions</a> | <a href='http://wordpress.org/extend/plugins/link-library/faq/' target='llfaq'>FAQ</a> | Help also in tooltips | <a href='http://yannickcorner.nayanna.biz/contact-me'>Contact the Author</a><br /><br />
				
				<form name='llmoderateform' action="<?php echo WP_ADMIN_URL ?>/options-general.php?page=link-library.php&section=moderate" method="post" id="ll-mod">
				<?php
				if ( function_exists('wp_nonce_field') )
						wp_nonce_field('linklibrarypp-config');
					?>
				<table class='widefat' style='clear:none;width:100%;background: #DFDFDF url(/wp-admin/images/gray-grad.png) repeat-x scroll left top;'>
					<tr>
						<th style='width: 30px'></th>
						<th style='width: 200px'>Link Name</th>
						<th style='width: 300px'>Link URL</th>
						<th>Link Description</th>
					</tr>
				<?php global $wpdb;
				
					$linkquery = "SELECT distinct * ";
					$linkquery .= "FROM " . $wpdb->prefix . "links l ";
					$linkquery .= "WHERE l.link_description like '%LinkLibrary:AwaitingModeration:RemoveTextToApprove%' ";
					$linkquery .= " ORDER by link_name ASC";
					
					$linkitems = $wpdb->get_results($linkquery);
					

					if ($linkitems) {
						foreach($linkitems as $linkitem) {
						
						$modpos = strpos($linkitem->link_description, "LinkLibrary:AwaitingModeration:RemoveTextToApprove");
					
						if ($modpos)
						{
							$startpos = $modpos + 51;
							$newlinkdesc = substr($linkitem->link_description, $startpos);
						}
				?>
						<tr style='background: #FFF'>
							<td><input type="checkbox" name="links[]" value="<?php echo $linkitem->link_id; ?>" /></td>
							<td><?php echo "<a title='Edit Link: " . $linkitem->link_name . "' href='http://yannickcorner.nayanna.biz/wp-admin/link.php?action=edit&link_id=" . $linkitem->link_id. "'>" . $linkitem->link_name . "</a>"; ?></td>
							<td><?php echo "<a href='" . $linkitem->link_url . "'>" . $linkitem->link_url . "</a>"; ?></td>
							<td><?php echo $newlinkdesc; ?></td>
						</tr>
				<?php      	}
						}
						else { ?>
						<tr>
							<td></td>
							<td>No Links Found to Moderate</td>
							<td></td>
							<td></td>
						</tr>
				<?php } ?>			
				
				</table><br />
				<input type="button" name="CheckAll" value="Check All" onClick="checkAll(document.llmoderateform['links[]'])">
				<input type="button" name="UnCheckAll" value="Uncheck All" onClick="uncheckAll(document.llmoderateform['links[]'])">

				<input type="submit" name="approvelinks" value="Approve Selected Items" />
				<input type="submit" name="deletelinks" value="Delete Selected Items" />
				</form>
			
			</div>
			
			<?php } else { ?>
			<div class="wrap" id='lladmin' style='width:1000px'>
				<h2>Link Library Configuration</h2>
				<a href="<?php echo WP_ADMIN_URL ?>/options-general.php?page=link-library.php&section=moderate">Links awaiting moderation</a> | <a href="http://yannickcorner.nayanna.biz/wordpress-plugins/link-library/" target="linklibrary"><img src="<?php echo $llpluginpath; ?>/icons/btn_donate_LG.gif" /></a> | <a target='llinstructions' href='http://wordpress.org/extend/plugins/link-library/installation/'>Installation Instructions</a> | <a href='http://wordpress.org/extend/plugins/link-library/faq/' target='llfaq'>FAQ</a> | Help also in tooltips | <a href='http://yannickcorner.nayanna.biz/contact-me'>Contact the Author</a><br /><br />
				
				<div>
				<form name='lladmingenform' action="<?php echo WP_ADMIN_URL ?>/options-general.php?page=link-library.php" method="post" id="ll-conf">
				<?php
				if ( function_exists('wp_nonce_field') )
						wp_nonce_field('linklibrarypp-config');
					?>
				<fieldset style='border:1px solid #CCC;padding:10px'>
				<legend class="tooltip" title='These apply to all Settings Sets' style='padding: 0 5px 0 5px;'><strong>General Settings <span style="border:0;padding-left: 15px;" class="submit"><input type="submit" name="submitgen" value="Update General Settings &raquo;" /></span></strong></legend>
				<table>
				<tr>
				<td style='width:200px'>Stylesheet File Name</td>
				<td><input type="text" id="stylesheet" name="stylesheet" size="40" value="<?php echo $genoptions['stylesheet']; ?>"/></td>
				<td style='padding-left: 10px;padding-right:10px'>Number of Style Sets</td>
				<td><input type="text" id="numberstylesets" name="numberstylesets" size="5" value="<?php if ($genoptions['numberstylesets'] == '') echo '5'; echo $genoptions['numberstylesets']; ?>"/></td>
				</tr>
				<tr>
				<td class="tooltip" title="Enter comma-separate list of pages on which the Link Library stylesheet and scripts should be loaded. Primarily used if you display Link Library using the API">Additional pages to load styles and scripts</td>
				<td class="tooltip" title="Enter comma-separate list of pages on which the Link Library stylesheet and scripts should be loaded. Primarily used if you display Link Library using the API"><input type="text" id="includescriptcss" name="includescriptcss" size="40" value="<?php echo $genoptions['includescriptcss']; ?>"/></td>
				<td style="padding-left: 10px;padding-right:10px">Debug Mode</td>
				<td><input type="checkbox" id="debugmode" name="debugmode" <?php if ($genoptions['debugmode']) echo ' checked="checked" '; ?>/></td>
				</tr>
				</table>
				</fieldset>
				</form>
				</div>
				
				<div style='padding-top: 15px'>
					<fieldset style='border:1px solid #CCC;padding:10px'>
					<legend style='padding: 0 5px 0 5px;'><strong>Setting Set Selection and Usage Instructions</strong></legend>				
						<FORM name="settingsetselection">
							Select Current Style Set: 
							<SELECT name="settingsetlist" style='width: 300px'>
							<?php if ($genoptions['numberstylesets'] == '') $numberofsets = 5; else $numberofsets = $genoptions['numberstylesets'];
								for ($counter = 1; $counter <= $numberofsets; $counter++): ?>
									<?php $tempoptionname = "LinkLibraryPP" . $counter;
									   $tempoptions = get_option($tempoptionname); ?>
									   <option value="<?php echo $counter ?>" <?php if ($settings == $counter) echo 'SELECTED';?>>Setting Set <?php echo $counter ?><?php if ($tempoptions != "") echo " (" . $tempoptions['settingssetname'] . ")"; ?></option>
								<?php endfor; ?>
							</SELECT>
							<INPUT type="button" name="go" value="Go!" onClick="window.location= '?page=link-library.php&amp;settings=' + document.settingsetselection.settingsetlist.options[document.settingsetselection.settingsetlist.selectedIndex].value">						
							Copy from: 
							<SELECT name="copysource" style='width: 300px'>
							<?php if ($genoptions['numberstylesets'] == '') $numberofsets = 5; else $numberofsets = $genoptions['numberstylesets'];
								for ($counter = 1; $counter <= $numberofsets; $counter++): ?>
									<?php $tempoptionname = "LinkLibraryPP" . $counter;
									   $tempoptions = get_option($tempoptionname); 
									   if ($counter != $settings):?>
									   <option value="<?php echo $counter ?>" <?php if ($settings == $counter) echo 'SELECTED';?>>Setting Set <?php echo $counter ?><?php if ($tempoptions != "") echo " (" . $tempoptions['settingssetname'] . ")"; ?></option>
									   <?php endif; 
								    endfor; ?>
							</SELECT>
							<INPUT type="button" name="copy" value="Copy!" onClick="window.location= '?page=link-library.php&amp;copy=<?php echo $settings; ?>&source=' + document.settingsetselection.copysource.options[document.settingsetselection.copysource.selectedIndex].value">							
					<br />
					<br />
					<table class='widefat' style='clear:none;width:100%;background: #DFDFDF url(/wp-admin/images/gray-grad.png) repeat-x scroll left top;'>
						<thead>
						<tr>
							<th style='width:40px' class="tooltip" title='Link Library Supports the Creation of up to 5 configurations to display link lists on your site'>
								Set #
							</th>
							<th style='width:130px' class="tooltip" title='Link Library Supports the Creation of up to 5 configurations to display link lists on your site'>
								Set Name
							</th>
							<th class="tooltip" title='Link Library Supports the Creation of up to 5 configurations to display link lists on your site'>
								Code to insert on a Wordpress page to see Link Library
							</th>
						</tr>
						</thead>
						<tr>
						<td style='background: #FFF'><?php echo $settings; ?></td><td style='background: #FFF'><?php echo $options['settingssetname']; ?></a></td><td style='background: #FFF'><?php echo "[link-library-cats settings=" . $settings . "] [link-library-search] [link-library settings=" . $settings . "] [link-library-addlink settings=". $settings . "]"; ?></td><td style='background: #FFF;text-align:center'></td>
						</tr>
					</table> 
					<br />
					</FORM>
					</fieldset>
				</div>
				
				<div>
				<form name="lladminform" action="<?php echo WP_ADMIN_URL ?>/options-general.php?page=link-library.php" method="post" id="analytics-conf">
				<?php
					if ( function_exists('wp_nonce_field') )
						wp_nonce_field('linklibrarypp-config');
					?>
					
					<table>
					<tr>
						<td style='text-align:left; width: 350px'><span style="border:0;" class="submit"><input type="submit" name="submit" value="Update Settings &raquo;" /></span></td>
						<td style='text-align:right'>
							<span><a href='?page=link-library.php&amp;deletesettings=<?php echo $settings ?>' <?php echo "onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to Delete Setting Set '%s'\n  'Cancel' to stop, 'OK' to delete."), $settings )) . "') ) { return true;}return false;\""; ?>>Delete Settings Set <?php echo $settings ?></a></span>
							<span><a href='?page=link-library.php&amp;reset=<?php echo $settings; ?>' <?php echo "onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to reset Setting Set '%s'\n  'Cancel' to stop, 'OK' to reset."), $settings )) . "') ) { return true;}return false;\""; ?>>Reset current Settings Set</a></span>
							<span><a href='?page=link-library.php&amp;resettable=<?php echo $settings; ?>' <?php echo "onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to reset Setting Set '%s' for a table layout\n  'Cancel' to stop, 'OK' to reset."), $settings )) . "') ) { return true;}return false;\""; ?>>Reset current Setting Set for table layout</a></span>
						</td>
					</tr>
					</table>
					
					<div style='padding-top: 15px'>
					<fieldset style='border:1px solid #CCC;padding:10px'>
					<legend style='padding: 0 5px 0 5px;'><strong>Common Parameters</strong></legend>
					<input type='hidden' value='<?php echo $settings; ?>' name='settingsetid' id='settingsetid' />
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
						<td class="tooltip" title="Leave Empty to see all categories<br /><br />Enter list of comma-separated<br />numeric category IDs<br /><br />To find the IDs, go to the Link Categories admin page, place the mouse above a category name and look for its ID in the address shown in your browser's status bar. For example: 2,4,56">
							Categories to be displayed (Empty=All)
						</td>
						<td class="tooltip" title="Leave Empty to see all categories<br /><br />Enter list of comma-separated<br />numeric category IDs<br /><br />For example: 2,4,56">
							<input type="text" id="categorylist" name="categorylist" size="40" value="<?php echo $options['categorylist']; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="tooltip" title="Enter list of comma-separated<br />numeric category IDs that should not be shown<br /><br />For example: 5,34,43">
							Categories to be excluded
						</td>
						<td class="tooltip" title="Enter list of comma-separated<br />numeric category IDs that should not be shown<br /><br />For example: 5,34,43">
							<input type="text" id="excludecategorylist" name="excludecategorylist" size="40" value="<?php echo $options['excludecategorylist']; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="tooltip" title="Only show one category of links at a time">
							Only show one category at a time
						</td>
						<td class="tooltip" title="Only show one category of links at a time">
							<input type="checkbox" id="showonecatonly" name="showonecatonly" <?php if ($options['showonecatonly']) echo ' checked="checked" '; ?>/>
						</td>
						<td class="tooltip" title="Select if AJAX should be used to only reload the list of links without reloading the whole page or HTML GET to reload entire page with a new link">Switching Method</td>
						<td>
							<select name="showonecatmode" id="showonecatmode" style="width:200px;">
								<option value="AJAX"<?php if ($options['showonecatmode'] == 'AJAX' || $options['showonecatmode'] == '') { echo ' selected="selected"';} ?>>AJAX</option>
								<option value="HTMLGET"<?php if ($options['showonecatmode'] == 'HTMLGET') { echo ' selected="selected"';} ?>>HTML GET</option>
							</select>
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
						<td class="tooltip" title="File path is relative to Link Library plugin directory">
							Icon to display when performing AJAX queries
						</td>
						<td class="tooltip" title="File path is relative to Link Library plugin directory">
							<input type="text" id="loadingicon" name="loadingicon" size="40" value="<?php if ($options['loadingicon'] == '') {echo '/icons/Ajax-loader.gif';} else {echo strval($options['loadingicon']);} ?>"/>
						</td>
					</tr>
					<tr>
						<td class="tooltip" title='Only show a limited number of links and add page navigation links'>
							Paginate Results
						</td>
						<td class="tooltip" title='Only show a limited number of links and add page navigation links'>
							<input type="checkbox" id="pagination" name="pagination" <?php if ($options['pagination']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					<tr>
						<td class="tooltip" title="Number of Links to be Displayed per Page in Pagination Mode">
							Links per Page
						</td>
						<td class="tooltip" title="Number of Links to be Displayed per Page in Pagination Mode">
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
					<div style='padding-top:15px'>
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
						<td>
							Display link counts
						</td>
						<td>
							<input type="checkbox" id="showcatlinkcount" name="showcatlinkcount" <?php if ($options['showcatlinkcount']) echo ' checked="checked" '; ?>/>
						</td>
						<td style='width:100px'></td>
						<td style='width:200px'>
						</td>
						<td>
						</td>
					</tr>					
					<tr>
						<td class="tooltip" title="This setting does not apply when selecting My Link Order for the order">
							Direction
						</td>
						<td class="tooltip" title="This setting does not apply when selecting My Link Order for the order">
							<select name="direction" id="direction" style="width:100px;">
								<option value="ASC"<?php if ($options['direction'] == 'ASC') { echo ' selected="selected"';} ?>>Ascending</option>
								<option value="DESC"<?php if ($options['direction'] == 'DESC') { echo ' selected="selected"';} ?>>Descending</option>
							</select>
						</td>
						<td></td>
						<td class="tooltip" title="Use [ and ] in the description to perform special actions using HTML such as inserting images instead of < and >">
							Show Category Description
						</td>
						<td class="tooltip" title="Use [ and ] in the description to perform special actions using HTML such as inserting images instead of < and >">
							<input type="checkbox" id="showcategorydescheaders" name="showcategorydescheaders" <?php if ($options['showcategorydescheaders']) echo ' checked="checked" '; ?>/>
							<span style='margin-left: 17px'>Position:</span>							
							<select name="catlistdescpos" id="catlistdescpos" style="width:100px;">
								<option value="right"<?php if ($options['catlistdescpos'] == 'right') { echo ' selected="selected"';} ?>>Right</option>
								<option value="left"<?php if ($options['catlistdescpos'] == 'left') { echo ' selected="selected"';} ?>>Left</option>
							</select>							
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
						<td class="tooltip" title='Determines the number of alternating div tags that will be placed before and after each link category.<br /><br />These div tags can be used to style of position link categories on the link page.'>
							Number of alternating div classes 
						</td>
						<td class="tooltip" title='Determines the number of alternating div tags that will be placed before and after each link category.<br /><br />These div tags can be used to style of position link categories on the link page.'>
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
						<td class="tooltip" title="Example div class name: linklistcatname, Example Heading Label: h3">
							Div Class Name or Heading label
						</td>
						<td  class="tooltip" title="Example div class name: linklistcatname, Example Heading Label: h3">
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
						<td class="tooltip" title="Use [ and ] in the description to perform special actions using HTML such as inserting images instead of < and >">
							Show Category Description
						</td>
						<td class="tooltip" title="Use [ and ] in the description to perform special actions using HTML such as inserting images instead of < and >">
							<input type="checkbox" id="showcategorydesclinks" name="showcategorydesclinks" <?php if ($options['showcategorydesclinks']) echo ' checked="checked" '; ?>/>
							<span style='margin-left: 17px'>Position:</span>							
							<select name="catdescpos" id="catdescpos" style="width:100px;">
								<option value="right"<?php if ($options['catdescpos'] == 'right') { echo ' selected="selected"';} ?>>Right</option>
								<option value="left"<?php if ($options['catdescpos'] == 'left') { echo ' selected="selected"';} ?>>Left</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tooltip" title='Except for My Link Order mode'>
							Direction
						</td>
						<td class="tooltip" title='Except for My Link Order mode'>
							<select name="linkdirection" id="linkdirection" style="width:200px;">
								<option value="ASC"<?php if ($options['linkdirection'] == 'ASC') { echo ' selected="selected"';} ?>>Ascending</option>
								<option value="DESC"<?php if ($options['linkdirection'] == 'DESC') { echo ' selected="selected"';} ?>>Descending</option>
							</select>
						</td>
						<td></td>
						<td class="tooltip" title='Need to be active for Link Categories to work'>
							Embed HTML anchors
						</td>
						<td class="tooltip" title='Need to be active for Link Categories to work'>
							<input type="checkbox" id="catanchor" name="catanchor" <?php if ($options['catanchor']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					<tr>
						<td class="tooltip" title="Sets default link target window, does not override specific targets set in links">
							Link Target
						</td>
						<td class="tooltip" title="Sets default link target window, does not override specific targets set in links">
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
							Show Hidden Links
						</td>
						<td>
							<input type="checkbox" id="showinvisible" name="showinvisible" <?php if ($options['showinvisible'] == true) echo ' checked="checked" '; ?>/>
						</td>
					</tr>	
					</table>
					<br />
					<strong>Link Sub-Field Configuration Table</strong>
						<table class='widefat' style='margin:15px 5px 10px 0px;clear:none;background: #DFDFDF url(/wp-admin/images/gray-grad.png) repeat-x scroll left top;'>
							<thead>
								<tr>
									<th></th>
									<th class="tooltip" title='This column allows for the output of text/code before a number of links determined by the Display field'>Intermittent Before Link</th>
									<th class="tooltip" title='This column allows for the output of text/code before each link'>Before Link</th>
									<th class="tooltip" title='This column allows for the output of text/code before each link image'>Image</th>
									<th class="tooltip" title='This column allows for the output of text/code before and after each link name'>Link</th>
									<th class="tooltip" title='This column allows for the output of text/code before and after each link date stamp'>Link Date</th>
								</tr>
							</thead>			
							<tr>
								<td style='background: #FFF'>
									Display
								</td>
								<td style='background: #FFF' class="tooltip" title='Frequency of additional output before and after complete link group'>
									<input type="text" id="linkaddfrequency" name="linkaddfrequency" size="10" value="<?php echo strval($options['linkaddfrequency']); ?>"/>
								</td>						
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF'>
									<select name="imagepos" id="imagepos" style="width:150px;">
										<option value="beforename"<?php if ($options['imagepos'] == 'beforename' || $options['imagepos'] == '') { echo ' selected="selected"';} ?>>Before Name</option>
										<option value="aftername"<?php if ($options['imagepos'] == 'aftername') { echo ' selected="selected"';} ?>>After Name</option>
										<option value="afterrssicons"<?php if ($options['imagepos'] == 'afterrssicons') { echo ' selected="selected"';} ?>>After RSS Icons</option>
									</select>
								</td>								
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF' class="tooltip" title='Check to display link date'>
									<input type="checkbox" id="showdate" name="showdate" <?php if ($options['showdate']) echo ' checked="checked" '; ?>/>
								</td>
							</tr>					
							<tr>
								<td style='background: #FFF'>
									Before
								</td>
								<td style='background: #FFF' class="tooltip" title='Output before complete link group (link, notes, desc, etc...)'>
									<input type="text" id="addbeforelink" name="addbeforelink" size="22" value="<?php echo stripslashes($options['addbeforelink']); ?>"/>
								</td>						
								<td style='background: #FFF' class="tooltip" title='Output before complete link group (link, notes, desc, etc...)'>
									<input type="text" id="beforeitem" name="beforeitem" size="22" value="<?php echo stripslashes($options['beforeitem']); ?>"/>
								</td>
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed before each link image'>
									<input type="text" id="beforeimage" name="beforeimage" size="22" value="<?php echo stripslashes($options['beforeimage']); ?>"/>
								</td>								
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed before each link'>
									<input type="text" id="beforelink" name="beforelink" size="22" value="<?php echo stripslashes($options['beforelink']); ?>"/>
								</td>
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed before each date'>
									<input type="text" id="beforedate" name="beforedate" size="22" value="<?php echo stripslashes($options['beforedate']); ?>"/>
								</td>								
							</tr>
							<tr>
								<td style='background: #FFF'>
									After
								</td>
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed after each link image'>
									<input type="text" id="afterimage" name="afterimage" size="22" value="<?php echo stripslashes($options['afterimage']); ?>"/>
								</td>								
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed after each link'>
									<input type="text" id="afterlink" name="afterlink" size="22" value="<?php echo stripslashes($options['afterlink']); ?>"/>
								</td>
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed after each date'>
									<input type="text" id="afterdate" name="afterdate" size="22" value="<?php echo stripslashes($options['afterdate']); ?>"/>
								</td>								
							</tr>
							<tr>
								<td style='background: #FFF'>
									Element Class
								</td>
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF' class="tooltip" title='Class to be assigned to link image'>
									<input type="text" id="imageclass" name="imageclass" size="22" value="<?php echo $options['imageclass']; ?>"/>
								</td>								
								<td style='background: #FFF'>
								</td>
								<td style='background: #FFF'>
								</td>								
							</tr>							
					</table>
					<table class='widefat' style='margin:15px 5px 10px 0px;clear:none;background: #DFDFDF url(/wp-admin/images/gray-grad.png) repeat-x scroll left top;'>
							<thead>
								<tr>
									<th></th>
									<th class="tooltip" title='This column allows for the output of text/code before and after each link description'>Link Description</th>
									<th class="tooltip" title='This column allows for the output of text/code before and after each link notes'>Link Notes</th>
									<th class="tooltip" title='This column allows for the output of text/code before and after the RSS icons'>RSS Icons</th>
									<th class="tooltip" title='This column allows for the output of text/code after each link'>After Link Block</th>
									<th class="tooltip" title='This column allows for the output of text/code after a number of links determined in the first column'>Intermittent After Link</th>
								</tr>
							</thead>			
							<tr>
								<td style='background: #FFF'>
									Display
								</td>						
								<td style='background: #FFF' class="tooltip" title='Check to display link descriptions'>
									<input type="checkbox" id="showdescription" name="showdescription" <?php if ($options['showdescription']) echo ' checked="checked" '; ?>/>
								</td>
								<td style='background: #FFF' class="tooltip" title='Check to display link notes'>
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
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed before each description'>
									<input type="text" id="beforedesc" name="beforedesc" size="22" value="<?php echo stripslashes($options['beforedesc']); ?>"/>
								</td>
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed before each note'>
									<input type="text" id="beforenote" name="beforenote" size="22" value="<?php echo stripslashes($options['beforenote']); ?>"/>
								</td>
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed before RSS Icons'>
									<input type="text" id="beforerss" name="beforerss" size="22" value="<?php echo stripslashes($options['beforerss']); ?>"/>
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
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed after each description'>
									<input type="text" id="afterdesc" name="afterdesc" size="22" value="<?php echo stripslashes($options['afterdesc']); ?>"/>
								</td>
								<td style='background: #FFF' class="tooltip" title='Code/Text to be displayed after each note'>
									<input type="text" id="afternote" name="afternote" size="22" value="<?php echo stripslashes($options['afternote']); ?>"/>
								</td>
								<td  style='background: #FFF' class="tooltip" title='Code/Text to be displayed after RSS Icons'>
									<input type="text" id="afterrss" name="afterrss" size="22" value="<?php echo stripslashes($options['afterrss']); ?>"/>
								</td>
								<td style='background: #FFF' class="tooltip" title='Output after complete link group (link, notes, desc, etc...)'>
									<input type="text" id="afteritem" name="afteritem" size="22" value="<?php echo stripslashes($options['afteritem']); ?>"/>
								</td>	
								<td style='background: #FFF'>
									<input type="text" id="addafterlink" name="addafterlink" size="22" value="<?php echo stripslashes($options['addafterlink']); ?>"/>
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
							Convert [] to &lt;&gt; in Link Description and Notes
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
						<td colspan='1' class="tooltip" title='Used for RSS Preview and RSS Inline Articles options below. Must have write access to directory.'>
							RSS Cache Directory
						</td>
						<td colspan='5' class="tooltip" title='Used for RSS Preview and RSS Inline Articles options below. Must have write access to directory.'>
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
					<tr>
						<td>RSS Preview Width</td>
						<td><input type="text" id="rsspreviewwidth" name="rsspreviewwidth" size="5" value="<?php if ($options['rsspreviewwidth'] == '') echo '900'; else echo strval($options['rsspreviewwidth']); ?>"/></td>
						<td>RSS Preview Height</td>
						<td><input type="text" id="rsspreviewheight" name="rsspreviewheight" size="5" value="<?php if ($options['rsspreviewheight'] == '') echo '700'; else echo strval($options['rsspreviewheight']); ?>"/></td>
						<td></td><td></td>
					</tr>
					</table>
					</fieldset>
					<fieldset style='border:1px solid #CCC;padding:15px;margin:15px;'>
					<legend style='padding: 0 5px 0 5px;'><strong>Thumbnail Generation and Use</strong></legend>
					<table>
					<tr>
						<td>
							Use Thumbshots.org for dynamic link images
						</td>
						<td style='width=75px;padding-right:20px'>
							<input type="checkbox" id="usethumbshotsforimages" name="usethumbshotsforimages" <?php if ($options['usethumbshotsforimages']) echo ' checked="checked" '; ?>/>
						</td>
						<td><INPUT type="button" name="genthumbs" value="Generate Thumbnails and Store locally" onClick="window.location= '?page=link-library.php&amp;settings=<?php echo $settings; ?>&amp;genthumbs=<?php echo $settings; ?>'"></td>
						<td><INPUT type="button" name="genfavicons" value="Generate Favorite Icons and Store locally" onClick="window.location= '?page=link-library.php&amp;settings=<?php echo $settings; ?>&amp;genfavicons=<?php echo $settings; ?>'"></td><td style='width=75px;padding-right:20px'></td>
					</tr>					
					</table>
					</fieldset>					
					<fieldset style='border:1px solid #CCC;padding:15px;margin:15px;'>
					<legend style='padding: 0 5px 0 5px;'><strong>RSS Generation</strong></legend>
					<table>
					<tr>
						<td>
							Publish RSS Feed
						</td>
						<td style='width=75px;padding-right:20px'>
							<input type="checkbox" id="publishrssfeed" name="publishrssfeed" <?php if ($options['publishrssfeed']) echo ' checked="checked" '; ?>/>
						</td>
						<td>Number of items in RSS feed</td><td style='width=75px;padding-right:20px'><input type="text" id="numberofrssitems" name="numberofrssitems" size="3" value="<?php if ($options['numberofrssitems'] == '') echo '10'; else echo strval($options['numberofrssitems']); ?>"/></td>
					</tr>	
					<tr>
						<td>RSS Feed Title</td><td colspan=3><input type="text" id="rssfeedtitle" name="rssfeedtitle" size="80" value="<?php echo strval(wp_specialchars(stripslashes($options['rssfeedtitle']))); ?>"/></td>
					</tr>
					<tr>
						<td>RSS Feed Description</td><td colspan=3><input type="text" id="rssfeeddescription" name="rssfeeddescription" size="80" value="<?php echo strval(wp_specialchars(stripslashes($options['rssfeeddescription']))); ?>"/></td>
					</tr>
					</table>
					</fieldset>					
					</fieldset>
					</div>
					<div>
					<fieldset style='border:1px solid #CCC;padding:10px;margin:15px 0 5px 0;'>
					<legend style='padding: 0 5px 0 5px;'><strong>Link User Submission</strong></legend>
					<table>
						<tr>
							<td colspan=5 class="tooltip" title='Following this link shows a list of all links awaiting moderation. To approve a link, edit it and remove the text in parentheses at the beginning of the link description'><a href="<?php echo WP_ADMIN_URL ?>/link-manager.php?s=LinkLibrary%3AAwaitingModeration%3ARemoveTextToApprove">View list of links awaiting moderation</a></td>
						</tr>
						<tr>
							<td style='width:200px'>Show user links immediately</td>
							<td style='width:75px;padding-right:20px'><input type="checkbox" id="showuserlinks" name="showuserlinks" <?php if ($options['showuserlinks']) echo ' checked="checked" '; ?>/></td>
							<td style='width: 20px'></td>
							<td style='width: 20px'></td>
							<td style='width:250px'>E-mail admin on link submission</td>
							<td style='width:75px;padding-right:20px'><input type="checkbox" id="emailnewlink" name="emailnewlink" <?php if ($options['emailnewlink']) echo ' checked="checked" '; ?>/></td>							
							<td style='width: 20px'></td>
						</tr>
						<tr>
							<td style='width:200px'>Require login to display form</td>
							<td style='width:75px;padding-right:20px'><input type="checkbox" id="addlinkreqlogin" name="addlinkreqlogin" <?php if ($options['addlinkreqlogin']) echo ' checked="checked" '; ?>/></td>
							<td style='width: 20px'></td>
							<td style='width: 20px'></td>
							<td style='width:250px'></td>
							<td style='width:75px;padding-right:20px'></td>							
							<td style='width: 20px'></td>
						</tr>						
						<tr>
							<td style='width:200px'>Add new link label</td>
							<?php if ($options['addnewlinkmsg'] == "") $options['addnewlinkmsg'] = "Add new link"; ?>
							<td><input type="text" id="addnewlinkmsg" name="addnewlinkmsg" size="30" value="<?php echo $options['addnewlinkmsg']; ?>"/></td>
							<td style='width: 20px'></td>
							<td style='width: 20px'></td>
							<td style='width:200px'>Link name label</td>
							<?php if ($options['linknamelabel'] == "") $options['linknamelabel'] = "Link Name"; ?>
							<td><input type="text" id="linknamelabel" name="linknamelabel" size="30" value="<?php echo $options['linknamelabel']; ?>"/></td>
							<td style='width: 20px'></td>
						</tr>
						<tr>
							<td style='width:200px'>Link address label</td>
							<?php if ($options['linkaddrlabel'] == "") $options['linkaddrlabel'] = "Link Address"; ?>
							<td><input type="text" id="linkaddrlabel" name="linkaddrlabel" size="30" value="<?php echo $options['linkaddrlabel']; ?>"/></td>
							<td style='width: 20px'></td>
							<td style='width: 20px'></td>
							<td style='width:200px'>Link RSS label</td>
							<?php if ($options['linkrsslabel'] == "") $options['linkrsslabel'] = "Link RSS"; ?>
							<td><input type="text" id="linkrsslabel" name="linkrsslabel" size="30" value="<?php echo $options['linkrsslabel']; ?>"/></td>
							<td>
								<select name="showaddlinkrss" id="showaddlinkrss" style="width:60px;">
									<option value="false"<?php if ($options['showaddlinkrss'] == false) { echo ' selected="selected"';} ?>>Hide</option>
									<option value="true"<?php if ($options['showaddlinkrss'] == true) { echo ' selected="selected"';} ?>>Show</option>
								</select>
							</td>														
						</tr>
						<tr>
							<td style='width:200px'>Link category label</td>
							<?php if ($options['linkcatlabel'] == "") $options['linkcatlabel'] = "Link Category"; ?>
							<td><input type="text" id="linkcatlabel" name="linkcatlabel" size="30" value="<?php echo $options['linkcatlabel']; ?>"/></td>
							<td>
								<select name="showaddlinkcat" id="showaddlinkcat" style="width:60px;">
									<option value="false"<?php if ($options['showaddlinkcat'] == false) { echo ' selected="selected"';} ?>>Hide</option>
									<option value="true"<?php if ($options['showaddlinkcat'] == true) { echo ' selected="selected"';} ?>>Show</option>
								</select>
							</td>							
							<td style='width: 20px'></td>
							<td style='width:200px'>Link description label</td>
							<?php if ($options['linkdesclabel'] == "") $options['linkdesclabel'] = "Link Description"; ?>
							<td><input type="text" id="linkdesclabel" name="linkdesclabel" size="30" value="<?php echo $options['linkdesclabel']; ?>"/></td>
							<td>
								<select name="showaddlinkdesc" id="showaddlinkdesc" style="width:60px;">
									<option value="false"<?php if ($options['showaddlinkdesc'] == false) { echo ' selected="selected"';} ?>>Hide</option>
									<option value="true"<?php if ($options['showaddlinkdesc'] == true) { echo ' selected="selected"';} ?>>Show</option>
								</select>
							</td>														
						</tr>
						<tr>
							<td style='width:200px'>Link notes label</td>
							<?php if ($options['linknoteslabel'] == "") $options['linknoteslabel'] = "Link Notes"; ?>
							<td><input type="text" id="linknoteslabel" name="linknoteslabel" size="30" value="<?php echo $options['linknoteslabel']; ?>"/></td>
							<td>
								<select name="showaddlinknotes" id="showaddlinknotes" style="width:60px;">
									<option value="false"<?php if ($options['showaddlinknotes'] == false) { echo ' selected="selected"';} ?>>Hide</option>
									<option value="true"<?php if ($options['showaddlinknotes'] == true) { echo ' selected="selected"';} ?>>Show</option>
								</select>
							</td>								
							<td style='width: 20px'></td>
							<td style='width:200px'>Add Link button label</td>
							<?php if ($options['addlinkbtnlabel'] == "") $options['addlinkbtnlabel'] = "Add Link"; ?>
							<td><input type="text" id="addlinkbtnlabel" name="addlinkbtnlabel" size="30" value="<?php echo $options['addlinkbtnlabel']; ?>"/></td>
							<td style='width: 20px'></td>
						</tr>
						<tr>
							<td style='width:200px'>New Link Message</td>
							<?php if ($options['newlinkmsg'] == "") $options['newlinkmsg'] = "New link submitted"; ?>
							<td><input type="text" id="newlinkmsg" name="newlinkmsg" size="30" value="<?php echo $options['newlinkmsg']; ?>"/></td>
							<td style='width: 20px'></td>
							<td style='width: 20px'></td>
							<td style='width:200px'>New Link Moderation Label</td>
							<?php if ($options['moderatemsg'] == "") $options['moderatemsg'] = "it will appear in the list once moderated. Thank you."; ?>
							<td><input type="text" id="moderatemsg" name="moderatemsg" size="30" value="<?php echo $options['moderatemsg']; ?>"/></td>
							<td style='width: 20px'></td>
						</tr>
					</table>
					</fieldset>
					</div>

					<p style="border:0;" class="submit"><input type="submit" name="submit" value="Update Settings &raquo;" /></p>
					
					
				</form>
				</div>
			</div>
			
			<script type="text/javascript">
// Create the tooltips only on document load
jQuery(document).ready(function()
	{
jQuery('.tooltip').each(function()
		{
		jQuery(this).tipTip();
		}
	
);
});
</script>

			<?php }

		} // end config_page()
	
	} // end class LL_Admin

} //endif


function PrivateLinkLibraryCategories($order = 'name', $hide_if_empty = true, $table_width = 100, $num_columns = 1, $catanchor = true, 
							   $flatlist = false, $categorylist = '', $excludecategorylist = '', $showcategorydescheaders = false, 
							   $showonecatonly = false, $settings = '', $loadingicon = '/icons/Ajax-loader.gif', $catlistdescpos = 'right',
							   $debugmode = false, $pagination = false, $linksperpage = 5, $showcatlinkcount = false, $showonecatmode = 'AJAX') {
							   
	global $wpdb;
							   
	$output = '';
							   
	if (!isset($_GET['searchll']))
	{
		$countcat = 0;

		$order = strtolower($order);
		
		// Guess the location
		$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
			
		$output .= "<!-- Link Library Categories Output -->\n\n";
		
		if ($showonecatonly == true && ($showonecatmode == 'AJAX' || $showonecatmode == ''))
		{
			$output .= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
				
			$output .= "function showLinkCat ( _incomingID, _settingsID, _pagenumber) {\n";
			$output .= "var map = {id : _incomingID, settings : _settingsID, page: _pagenumber}\n";
			$output .= "\tjQuery('#contentLoading').toggle();jQuery.get('" . WP_PLUGIN_URL . "/link-library/link-library-ajax.php', map, function(data){jQuery('#linklist" . $settings. "').replaceWith(data);jQuery('#contentLoading').toggle();initTree();});\n";
			$output .= "}\n";
				
			$output .= "</SCRIPT>\n\n";
		}
		
		// Handle link category sorting
		$direction = 'ASC';
		if (substr($order,0,1) == '_') {
			$direction = 'DESC';
			$order = substr($order,1);
		}

		if (!isset($direction)) $direction = '';
		// Fetch the link category data as an array of hashesa
		
		$linkcatquery = "SELECT count(l.link_name) as linkcount, t.name, t.term_id, t.slug as category_nicename, tt.description as category_description ";
		$linkcatquery .= "FROM " . $wpdb->prefix . "terms t LEFT JOIN " . $wpdb->prefix. "term_taxonomy tt ON (t.term_id = tt.term_id)";
		
		$linkcatquery .= " LEFT JOIN " . $wpdb->prefix . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) LEFT JOIN " . $wpdb->prefix . "links l on (tr.object_id = l.link_id) ";
		
		$linkcatquery .= "WHERE tt.taxonomy = 'link_category'";
		
		if ($hide_if_empty)
		{
			if (!$showuserlinks)
				$linkcatquery .= " AND l.link_description not like '%LinkLibrary:AwaitingModeration:RemoveTextToApprove%' ";			
		}
			
		if ($categorylist != "")
			$linkcatquery .= " AND t.term_id in (" . $categorylist. ")";
			
		if ($excludecategorylist != "")
			$linkcatquery .= " AND t.term_id not in (" . $excludecategorylist . ")";
			
		$linkcatquery .= " GROUP BY t.name ";
			
		if ($order == "name")
			$linkcatquery .= " ORDER by t.name " . $direction;
		elseif ($order == "id")
			$linkcatquery .= " ORDER by t.term_id " . $direction;
		elseif ($order == "order")
			$linkcatquery .= " ORDER by t.term_order " . $direction;
		elseif ($order == "catlist")
			$linkcatquery .= " ORDER by FIELD(t.term_id," . $categorylist . ") ";
			
		$catnames = $wpdb->get_results($linkcatquery);
		
		if ($debugmode)
		{
			$output .= "\n<!-- Category Query: " . print_r($linkcatquery, TRUE) . "-->\n\n";
			$output .= "\n<!-- Category Results: " . print_r($catnames, TRUE) . "-->\n\n";
		}

		// Display each category

		if ($catnames) {
			
			$output .=  "<div id=\"linktable\" class=\"linktable\">";
			
			if (!$flatlist)
				$output .= "<table width=\"" . $table_width . "%\">\n";
			else
				$output .= "<ul>\n";
				
			$linkcount = 0;
				
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
				{
					if ($showonecatmode == 'AJAX' || $showonecatmode == '')
						$cattext = "<a href='#' onClick=\"showLinkCat('" . $catname->term_id. "', '" . $settings . "', 1);return false;\" >";
					elseif ($showonecatmode == 'HTMLGET')
						$cattext = "<a href='?cat_id=" . $catname->term_id . "'>";
				}
				else if ($catanchor)
				{
					if (!$pagination)
						$cattext = '<a href="#' . $catname->category_nicename . '">';
					elseif ($pagination)
					{
						$pageposition = $linkcount / $linksperpage;
						$pageposition = ceil($pageposition);
						if ($pageposition == 0 && !isset($_GET['page']))
							$cattext = '<a href="' . get_permalink() . '#' . $catname->category_nicename . '">';
						else
							$cattext = '<a href="?page=' . ($pageposition == 0 ? 1 : $pageposition) . '#' . $catname->category_nicename . '">';
							
						$linkcount = $linkcount + $catname->linkcount;						
					}
				}
				else
					$cattext = '';
		
				$catitem = '';
				if ($catlistdescpos == 'right' || $catlistdescpos == '')
				{
					$catitem .= $catname->name;
					if ($showcatlinkcount)
						$catitem .= " (" . $catname->linkcount . ")";
				}
				
				if ($showcategorydescheaders)
				{
					$catname->category_description = wp_specialchars($catname->category_description);
					$catname->category_description = str_replace("[", "<", $catname->category_description);
					$catname->category_description = str_replace("]", ">", $catname->category_description);
					$catname->category_description = str_replace("&quot;", "\"", $catname->category_description);
					$catitem .= $catname->category_description;				
				}
				
				if ($catlistdescpos == 'left')
				{
					$catitem .= $catname->name;
					if ($showcatlinkcount)
						$catitem .= " (" . $catname->linkcount . ")";
				}
					
				
				
				if ($catanchor || $showonecatonly)
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
			
			if ($showonecatonly && ($showonecatmode == 'AJAX' || $showonecatmode == ''))
			{
				if ($loadingicon == '') $loadingicon = '/icons/Ajax-loader.gif';
				$output .= "<div class='contentLoading' id='contentLoading' style='display: none;'><img src='" . WP_PLUGIN_URL . "/link-library" . $loadingicon . "' alt='Loading data, please wait...'></div>\n";
			}
		}
		else
		{
			$output .= "<div>No categories found.</div>";	
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


function PrivateLinkLibrary($order = 'name', $hide_if_empty = true, $catanchor = true,
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
								$pagination = false, $linksperpage = 5, $hidecategorynames = false, $settings = '',
								$showinvisible = false, $showdate = false, $beforedate = '', $afterdate = '', $catdescpos = 'right',
								$showuserlinks = false, $rsspreviewwidth = 900, $rsspreviewheight = 700, $beforeimage = '', $afterimage = '',
								$imagepos = 'beforename', $imageclass = '', $AJAXpageid = 1, $debugmode = false, $usethumbshotsforimages = false,
								$showonecatmode = 'AJAX') {
								
	global $wpdb;
	
	$output = "\n<!-- Beginning of Link Library Output -->\n\n";
	
	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );	
	if ( !defined('WP_ADMIN_URL') )
		define( 'WP_ADMIN_URL', get_option('siteurl') . '/wp-admin');
	
	// Guess the location
	$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
	
	$currentcategory = 1;
	
	if ($showonecatonly && $showonecatmode == 'AJAX' && $AJAXcatid != '' && $_GET['searchll'] == "")
	{
		$categorylist = $AJAXcatid;
		$ajaxcatid = $categorylist;
	}
	elseif ($showonecatonly && $showonecatmode == 'HTMLGET' && $_GET['cat_id'] != "" && $_GET['searchll'] == "")
	{
		$categorylist = $_GET['cat_id'];
		$ajaxcatid = $categorylist;
	}	
	elseif ($showonecatonly && $AJAXcatid == '' && $defaultsinglecat != '' && $_GET['searchll'] == "")
	{
		$categorylist = $defaultsinglecat;
		$ajaxcatid = $categorylist;
	}
	elseif ($showonecatonly && $AJAXcatid == '' && $defaultsinglecat == '' && $_GET['searchll'] == "")
	{
		$catquery = "SELECT distinct t.name, t.term_id ";
		$catquery .= "FROM " . $wpdb->prefix . "terms t ";
		$catquery .= "LEFT JOIN " . $wpdb->prefix . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
		$catquery .= "LEFT JOIN " . $wpdb->prefix . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
		$catquery .= "LEFT JOIN " . $wpdb->prefix . "links l ON (tr.object_id = l.link_id) ";
		$catquery .= "WHERE tt.taxonomy = 'link_category' ";
			
		if ($hide_if_empty)
			$catquery .= "AND l.link_id is not NULL AND l.link_description not like '%LinkLibrary:AwaitingModeration:RemoveTextToApprove%' ";
					
		if ($categorylist != "")
			$catquery .= " AND t.term_id in (" . $categorylist. ")";
				
		if ($excludecategorylist != "")
			$catquery .= " AND t.term_id not in (" . $excludecategorylist . ")";

		if ($showinvisible == false)
			$catquery .= " AND l.link_visible != 'N'";	
			
		$mode = "normal";
			
		if ($order == "name")
			$catquery .= " ORDER by name " . $direction;
		elseif ($order == "id")
			$catquery .= " ORDER by t.term_id " . $direction;
		elseif ($order == "order")
			$catquery .= " ORDER by t.term_order " . $direction;
		elseif ($order == "catlist")
			$catquery .= " ORDER by FIELD(t.term_id," . $categorylist . ") ";
				
		if ($linkorder == "name")
			$catquery .= ", link_name " . $linkdirection;
		elseif ($linkorder == "id")
			$catquery .= ", link_id " . $linkdirection;
		elseif ($linkorder == "order")
			$catquery .= ", link_order ". $linkdirection;
			
		$catitems = $wpdb->get_results($catquery);
		
		if ($debugmode)
		{
			$output .= "\n<!-- AJAX Default Category Query: " . print_r($catquery, TRUE) . "-->\n\n";
			$output .= "\n<!-- AJAX Default Category Results: " . print_r($catitems, TRUE) . "-->\n\n";
		}
		
		if ($catitems)
		{
			$categorylist = $catitems[0]->term_id;
			$ajaxcatid = $categorylist;
		}
	}
		
	$linkquery = "SELECT distinct *, UNIX_TIMESTAMP(l.link_updated) as link_date, ";
	$linkquery .= "IF (DATE_ADD(l.link_updated, INTERVAL " . get_option('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated ";
	$linkquery .= "FROM " . $wpdb->prefix . "terms t ";
	$linkquery .= "LEFT JOIN " . $wpdb->prefix . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
	$linkquery .= "LEFT JOIN " . $wpdb->prefix . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
	$linkquery .= "LEFT JOIN " . $wpdb->prefix . "links l ON (tr.object_id = l.link_id) ";
	$linkquery .= "WHERE tt.taxonomy = 'link_category' ";
	
	if ($hide_if_empty)
		$linkquery .= "AND l.link_id is not NULL AND l.link_description not like '%LinkLibrary:AwaitingModeration:RemoveTextToApprove%' ";
			
	if ($categorylist != "")
		$linkquery .= " AND t.term_id in (" . $categorylist. ")";
		
	if ($excludecategorylist != "")
		$linkquery .= " AND t.term_id not in (" . $excludecategorylist . ")";

	if ($showinvisible == false)
		$linkquery .= " AND l.link_visible != 'N'";	
	
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
		$linkitemsforcount = $wpdb->get_results($linkquery);
		
		$numberoflinks = count($linkitemsforcount);
		
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
	
	$linkitems = $wpdb->get_results($linkquery);
	
	if ($debugmode)
	{
		$output .= "\n<!-- Link Query: " . print_r($linkquery, TRUE) . "-->\n\n";
		$output .= "\n<!-- Link Results: " . print_r($linkitems, TRUE) . "-->\n\n";
	}
	
	if ($pagination)
	{
		if (count($linkitems) > $linksperpage)
		{
			array_pop($linkitems);
			$nextpage = true;
		}
		else
			$nextpage = false;		
		$preroundpages = $numberoflinks / $linksperpage;	
		$numberofpages = ceil( $preroundpages * 1 ) / 1; 
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
							
						$catlink = '<div class="' . $catnameoutput . '">';
						
						if ($catdescpos == "right" || $catlistdescpos == '')
							$catlink .= $linkitem->name;
						
						if ($showcategorydesclinks)
						{
							$catlink .= "<span class='linklistcatnamedesc'>";
							$linkitem->description = str_replace("[", "<", $linkitem->description);
							$linkitem->description = str_replace("]", ">", $linkitem->description);
							$catlink .= $linkitem->description;				
							$catlink .= '</span>';
						}
						
						if ($catdescpos == "left")
							$catlink .= $linkitem->name;
						
						$catlink .= "</div>";
					}
					else if ($divorheader == true)
					{
						if ($mode == "search")
							$linkitem->name = highlightWords($linkitem->name, $searchterms);
							
						$catlink = '<'. $catnameoutput . '>';
						
						if ($catdescpos == "right" || $catlistdescpos == '')
							$catlink .= $linkitem->name;
						
						if ($showcategorydesclinks)
						{
							$catlink .= "<span class='linklistcatnamedesc'>";
							$linkitem->description = str_replace("[", "<", $linkitem->description);
							$linkitem->description = str_replace("]", ">", $linkitem->description);
							$catlink .= $linkitem->description;				
							$catlink .= '</span>';
						}
						
						if ($catdescpos == "left")
							$catlink .= $linkitem->name;
						
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
					{
						$catstartlist .= "<div class='linklisttableheaders'><tr>";
						
						if ($linkheader != "")
							$catstartlist .= "<th><div class='linklistcolumnheader'>".$linkheader."</div></th>";
						
						if ($descheader != "")
							$catstartlist .= "<th><div class='linklistcolumnheader'>".$descheader."</div></th>";
							
						if ($notesheader != "")
							$catstartlist .= "<th><div class='linklistcolumnheader'>".$notesheader."</div></th>";
							
						$catstartlist .= "</tr></div>\n";
					}
					else
						$catstartlist .= '';
				}
				else
					$catstartlist = "\n\t<ul>\n";
					
				
				$output .= $cattext . $catlink . $catenddiv . $catstartlist; 
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
			
			if ($showuserlinks == true || strpos($linkitem->link_description, "LinkLibrary:AwaitingModeration:RemoveTextToApprove") == false)
			{
				$linkcount = $linkcount + 1;
				
				if ($linkaddfrequency > 0)
					if (($linkcount - 1) % $linkaddfrequency == 0)
						$output .= stripslashes($addbeforelink);
				
				if (!isset($linkitem->recently_updated)) $linkitem->recently_updated = false; 
				$output .= stripslashes($beforeitem);
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
					$descnotes = str_replace("[", "<", $descnotes);
					$descnotes = str_replace("]", ">", $descnotes);
				}
				else
					$descnotes = wp_specialchars($linkitem->link_notes, ENT_QUOTES);
				
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
				
				if ( ($linkitem->link_image != null || $usethumbshotsforimages) && ($show_images || $show_image_and_name)) {
					$imageoutput = stripslashes($beforeimage) . '<a href="' . $the_link . '"' . $rel . $title . $target. '>';
					
					if ($usethumbshotsforimages)
						$imageoutput .= '<img src="http://open.thumbshots.org/image.aspx?url=' . $the_link . '"';
					elseif ( strpos($linkitem->link_image, 'http') !== false )
						$imageoutput .= '<img src="' . $linkitem->link_image . '"';
					else // If it's a relative path
						$imageoutput .= '<img src="' . get_option('siteurl') . $linkitem->link_image . '"';
						
					$imageoutput .= $alt . $title;
					
					if ($imageclass != '')
						$imageoutput .= ' class="' . $imageclass . '" ';
					
					$imageoutput .= "/>";
					 
					$imageoutput .= '</a>' . stripslashes($afterimage);
				}
				
				if ( ($linkitem->link_image != null || $usethumbshotsforimages) && ($show_images || $show_image_and_name) && ($imagepos == 'beforename' || $imagepos == "")) {
					$output .= $imageoutput;
				}
						
				if ($show_image_and_name || !$show_images)
					$output .= stripslashes($beforelink) . '<a href="' . $the_link . '"' . $rel . $title . $target. '>' . $name . '</a>';
				
				if (($showadmineditlinks) && current_user_can("manage_links")) {
					$output .= $between . '<a href="' . WP_ADMIN_URL . '/link.php?action=edit&link_id=' . $linkitem->link_id .'">(Edit)</a>';
				}
				
				if ($showupdated && $linkitem->recently_updated) {
					$output .= get_option('links_recently_updated_append');
				}
				
				$output .= stripslashes($afterlink);
				
				if ( ($linkitem->link_image != null || $usethumbshotsforimages) && ($show_images || $show_image_and_name) && $imagepos == 'aftername') {
					$output .= $imageoutput;
				}

				if ($use_html_tags || $mode == "search") {
					$desc = $linkitem->link_description;
					$desc = str_replace("[", "<", $desc);
					$desc = str_replace("]", ">", $desc);
				}
				else {
					$desc = wp_specialchars($linkitem->link_description, ENT_QUOTES);
				}
				
				$formatteddate = date("F d Y", $linkitem->link_date);
				
				if ($showdate)
					$output .= $between . stripslashes($beforedate) . $formatteddate . stripslashes($afterdate);
				
				if ($showdescription)
					$output .= $between . stripslashes($beforedesc) . $desc . stripslashes($afterdesc);

				if ($shownotes) {
					$output .= $between . stripslashes($beforenote) . $descnotes . stripslashes($afternote);
				}
				
				//$output .= '<div class="thumbshots"><a href="' . $the_link . '"' . $rel . $title . $target. '><img src="http://open.thumbshots.org/image.aspx?url=' . $the_link .'" border="1"></a></div>';
				
				if ($show_rss || $show_rss_icon || $rsspreview)
					$output .= stripslashes($beforerss) . '<div class="rsselements">';
					
				if ($show_rss && ($linkitem->link_rss != '')) {
					$output .= $between . '<a class="rss" href="' . $linkitem->link_rss . '">RSS</a>';
				}
				if ($show_rss_icon && ($linkitem->link_rss != '')) {
					$output .= $between . '<a class="rssicon" href="' . $linkitem->link_rss . '"><img src="' . $llpluginpath . '/icons/feed-icon-14x14.png" /></a>';
				}	
				if ($rsspreview && $linkitem->link_rss != '')
				{
					$output .= $between . '<a href="' . WP_PLUGIN_URL . '/link-library/rsspreview.php?keepThis=true&linkid=' . $linkitem->link_id . '&previewcount=' . $rsspreviewcount . '" title="Preview of RSS feed for ' . $cleanname . '" class="rssbox"><img src="' . $llpluginpath . '/icons/preview-16x16.png" /></a>';
				}
				
				if ($show_rss || $show_rss_icon || $rsspreview)
					$output .= '</div>' . stripslashes($afterrss);
									
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
				
				if ( ($linkitem->link_image != null || $usethumbshotsforimages) && ($show_images || $show_image_and_name) && $imagepos == 'afterrssicons') {
					$output .= $imageoutput;
				}

				
						
				$output .= stripslashes($afteritem) . "\n";
				
				//$output .= "</div>";
				
				if ($linkaddfrequency > 0)
					if ($linkcount % $linkaddfrequency == 0)
						$output .= stripslashes($addafterlink);
					
			}
							
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
			$dotbelow = false;
			$dotabove = false;
							
			if ($numberofpages > 1)
			{
				$output .= "<div class='pageselector'>";	
				
				if ($pagenumber != 1)
				{
					$output .= "<span class='previousnextactive'>";
				
					if (!$showonecatonly)
						$output .= "<a href='?page_id=" . get_the_ID() . "&page=" . $previouspagenumber . "'>Previous</a>";
					elseif ($showonecatonly)
					{
						if ($showonecatmode == 'AJAX' || $showonecatmode == '')
							$output .= "<a href='#' onClick=\"showLinkCat('" . $ajaxcatid . "', '" . $settings . "', " . $previouspagenumber . ");return false;\" >Previous</a>";
						elseif ($showonecatmode == 'HTMLGET')
							$output .= "<a href='?page_id=" . get_the_ID() . "&page=" . $previouspagenumber . "&cat_id=" . $ajaxcatid . "' >Previous</a>";
					}
						
					$output .= "</span>";
				}
				else
					$output .= "<span class='previousnextinactive'>Previous</span>";
				
				for ($counter = 1; $counter <= $numberofpages; $counter++)
				{
					if ($counter <= 2 || $counter >= $numberofpages - 1 || ($counter <= $pagenumber + 2 && $counter >= $pagenumber - 2))
					{
						if ($counter != $pagenumber)
							$output .= "<span class='unselectedpage'>";
						else
							$output .= "<span class='selectedpage'>";
						
						if (!$showonecatonly)
							$output .= "<a href='?page_id=" . get_the_ID() . "&page=" . $counter . "'>" . $counter . "</a>";
						elseif ($showonecatonly)
						{
							if ($showonecatmode == 'AJAX' || $showonecatmode == '')
								$output .= "<a href='#' onClick=\"showLinkCat('" . $ajaxcatid . "', '" . $settings . "', " . $counter . ");return false;\" >" . $counter . "</a>";
							elseif ($showonecatmode == 'HTMLGET')
								$output .= "<a href='?page_id=" . get_the_ID() . "&page=" . $counter . "&cat_id=" . $ajaxcatid . "' >" . $counter . "</a>";
						}
							
						$output .= "</a></span>";
					}
					
					if ($counter >= 2 && $counter < $pagenumber - 2 && $dotbelow == false)
					{
						$output .= "...";
						$dotbelow = true;
					}
						
					if ($counter > $pagenumber + 2 && $counter < $numberofpages - 1 && $dotabove == false)
					{
						$output .= "...";
						$dotabove = true;
					}					
				}
				
				if ($pagenumber != $numberofpages)
				{
					$output .= "<span class='previousnextactive'>";
					
					if (!$showonecatonly)
						$output .= "<a href='?page_id=" . get_the_ID() . "&page=" . $nextpagenumber . "'>Next</a>";
					elseif ($showonecatonly)
					{
						if ($showonecatmode == 'AJAX' || $showonecatmode == '')
							$output .= "<a href='#' onClick=\"showLinkCat('" . $ajaxcatid . "', '" . $settings . "', " . $nextpagenumber . ");return false;\" >Next</a>";
						elseif ($showonecatmode == 'HTMLGET')
							$output .= "<a href='?page_id=" . get_the_ID() . "&page=" . $nextpagenumber . "&cat_id=" . $ajaxcatid . "' >Next</a>";
					}
					
					$output .= "</span>";
				}
				else
					$output .= "<span class='previousnextinactive'>Next</span>";
					
				$output .= "</div>";
			}		
		}
		
		$currentcategory = $currentcategory + 1;
		
		$output .= "</div>\n";
		
	}
	else
	{
		$output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
		$output .= "No links found.\n";
		$output .= "</div>";			
	}
	
	if ($rsspreview)
	{
		$output .= "<script type='text/javascript'>\n";
		$output .= "jQuery(document).ready(function() {\n";
		$output .= "\tjQuery('a.rssbox').fancybox(\n";
		$output .= "\t\t{\n";
		$output .= "\t\t\t'frameWidth'	:	" . (($rsspreviewwidth == "") ?  900 : $rsspreviewwidth) . ",\n";
		$output .= "\t\t\t'frameHeight'	:	" . (($rsspreviewheight == "") ? 700 : $rsspreviewheight) . "\n";
		$output .= "\t\t}\n";
		$output .= ");";
		$output .= "});";
		$output .= "</script>";
	}
	
	$output .= "\n<!-- End of Link Library Output -->\n\n";
	
	return $output;
}

function PrivateLinkLibrarySearchForm() {

	$output = "<form method='get' id='llsearch'>\n";
	$output .= "<div>\n";
	$output .= "<input type='text' onfocus=\"this.value=''\" value='Search...' name='searchll' id='searchll' />\n";
	$output .= "<input type='hidden' value='" .  get_the_ID() . "' name='page_id' id='page_id' />\n";
	$output .= "<input type='submit' value='Search' />\n";
	$output .= "</div>\n";
	$output .= "</form>\n\n";
	
	return $output;
}

function PrivateLinkLibraryAddLinkForm($selectedcategorylist = '', $excludedcategorylist = '', $addnewlinkmsg = '', $linknamelabel = '', $linkaddrlabel = '',
										$linkrsslabel = '', $linkcatlabel = '', $linkdesclabel = '', $linknoteslabel = '', $addlinkbtnlabel = '', $hide_if_empty = true,
										$showaddlinkrss = false, $showaddlinkdesc = false, $showaddlinkcat = false, $showaddlinknotes = false,
										$addlinkreqlogin = false) {
										
	global $wpdb;
	
	if (($addlinkreqlogin && current_user_can("read")) || !$addlinkreqlogin)
	{
		$output = "<form method='post' id='lladdlink'>\n";
		$output .= "<div class='lladdlink'>\n";
		
		if ($addnewlinkmsg == "") $addnewlinkmsg = "Add new link";
		$output .= "<div id='lladdlinktitle'>" . $addnewlinkmsg . "</div>\n";
		
		$output .= "<table>\n";
		
		if ($linknamelabel == "") $linknamelabel = "Link name";
		$output .= "<tr><th>" . $linknamelabel . "</th><td><input type='text' name='link_name' id='link_name' /></td></tr>\n";
			
		if ($linkaddrlabel == "") $linkaddrlabel = "Link address";
		$output .= "<tr><th>" . $linkaddrlabel . "</th><td><input type='text' name='link_url' id='link_url' /></td></tr>\n";
		
		if ($showaddlinkrss)
		{
			if ($linkrsslabel == "") $linkrsslabel = "Link RSS";
			$output .= "<tr><th>" . $linkrsslabel . "</th><td><input type='text' name='link_rss' id='link_rss' /></td></tr>\n";
		}
		
		$linkcatquery = "SELECT distinct t.name, t.term_id, t.slug as category_nicename, tt.description as category_description ";
		$linkcatquery .= "FROM " . $wpdb->prefix . "terms t, " . $wpdb->prefix. "term_taxonomy tt ";
		
		if ($hide_if_empty)
			$linkcatquery .= ", " . $wpdb->prefix . "term_relationships tr ";
		
		$linkcatquery .= "WHERE t.term_id = tt.term_id AND tt.taxonomy = 'link_category'";
		
		if ($hide_if_empty)
			$linkcatquery .= " AND t.term_id = tr.term_taxonomy_id ";
			
		if ($selectedcategorylist != "")
			$linkcatquery .= " AND t.term_id in (" . $selectedcategorylist. ")";
			
		if ($excludedcategorylist != "")
			$linkcatquery .= " AND t.term_id not in (" . $excludedcategorylist . ")";
			
		$linkcatquery .= " ORDER by t.name " . $direction;
			
		$linkcats = $wpdb->get_results($linkcatquery);
			
		if ($linkcats)
		{
			if ($showaddlinkcat)
			{
				if ($linkcatlabel == "") $linkcatlabel = "Link category";
				
				$output .= "<tr><th>" . $linkcatlabel . "</th><td><SELECT name='link_category' id='link_category'>";
				foreach ($linkcats as $linkcat)
				{
					$output .= "<OPTION VALUE='" . $linkcat->term_id . "'>" . $linkcat->name;
				}
				
				$output .= "</SELECT></td></tr>";
			}
			else
			{
				$output .= "<input type='hidden' name='link_category' id='link_category' value='" . $linkcats[0]->term_id . "'>";
			}
		}
		
		if ($showaddlinkdesc)
		{
			if ($linkdesclabel == "") $linkdesclabel = "Link description";
			$output .= "<tr><th>" . $linkdesclabel . "</th><td><input type='text' name='link_description' id='link_description' /></td></tr>\n";
		}
		
		if ($showaddlinknotes)
		{
			if ($linknoteslabel == "") $linknoteslabel = "Link notes";
			$output .= "<tr><th>" . $linknoteslabel . "</th><td><input type='text' name='link_notes' id='link_notes' /></td></tr>\n";
		}
			
		$output .= "</table>\n";
		
		if ($addlinkbtnlabel == "") $addlinkbtnlabel = "Add link";
		$output .= '<span style="border:0;" class="submit"><input type="submit" name="submit" value="' . $addlinkbtnlabel . '" /></span>';
		
		$output .= "</div>\n";
		$output .= "</form>\n\n";
	}

	return $output;
}

$version = "2.2";

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
	$options['showinvisible'] = false;
	$options['showdate'] = false;
	$options['beforedate'] = '';
	$options['afterdate'] = '';
	$options['catdescpos'] = 'right';	
	$options['showuserlinks'] = false;	
	$options['addnewlinkmsg'] = "Add new link";
	$options['linknamelabel'] = "Link name";
	$options['linkaddrlabel'] = "Link address";
	$options['linkrsslabel'] = "Link RSS";
	$options['linkcatlabel'] = "Link Category";
	$options['linkdesclabel'] = "Link Description";
	$options['linknoteslabel'] = "Link Notes";
	$options['addlinkbtnlabel'] = "Add Link";
	$options['newlinkmsg'] = "New link submitted";
	$options['moderatemsg'] = "it will appear in the list once moderated. Thank you.";
	$options['rsspreviewwidth'] = 900;
	$options['rsspreviewheight'] = 700;
	$options['beforeimage'] = '';
	$options['afterimage'] = '';
	$options['imagepos'] = 'beforename';	
	$options['imageclass'] = '';
	$options['emailnewlink'] = false;
	$options['showaddlinkrss'] = false;
	$options['showaddlinkdesc'] = false;
	$options['showaddlinkcat'] = false;
	$options['showaddlinknotes'] = false;
	$options['usethumbshotsforimages'] = false;
	$options['addlinkreqlogin'] = false;
	$options['showcatlinkcount'] = false;
	$options['publishrssfeed'] = false;
	$options['numberofrssitems'] = 10;
	$options['rssfeedtitle'] = 'Link Library-Generated RSS Feed';
	$options['rssfeeddescription'] = 'Description of Link Library-Generated Feed';
	$options['showonecatmode'] = 'AJAX';
	
	update_option('LinkLibraryPP1',$options);
	
	$genoptions['stylesheet'] = 'stylesheet.css';
	$genoptions['numberstylesets'] = 5;
	$genoptions['includescriptcss'] = '';
	$genoptions['debugmode'] = false;
	
	update_option('LinkLibraryGeneral', $genoptions);
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
 *   catlistdescpos (default 'right') - Position of category description relative to name
 *   debugmode (default false)
 *   pagination (default false)
 *   linksperpage (default 5)
 *   showcatlinkcount (default false)
 *   showonecatmode (default 'AJAX')
 */

function LinkLibraryCategories($order = 'name', $hide_if_empty = true, $table_width = 100, $num_columns = 1, $catanchor = true, 
							   $flatlist = false, $categorylist = '', $excludecategorylist = '', $showcategorydescheaders = false,
							   $showonecatonly = false, $settings = '', $loadingicon = '/icons/Ajax-loader.gif', $catlistdescpos = 'right', $debugmode = false,
							   $pagination = false, $linksperpage = 5, $showcatlinkcount = false, $showonecatmode = 'AJAX') {
	
	if (strpos($order, 'AdminSettings') != false)
	{
		$settingsetid = substr($order, 13);
		$settingsetname = "LinkLibraryPP" . $settingsetid;
		$options = get_option($settingsetname);
		
		$genoptions = get_option('LinkLibraryGeneral');

		return PrivateLinkLibraryCategories($options['order'], $options['hide_if_empty'], $options['table_width'], $options['num_columns'], $options['catanchor'], $options['flatlist'],
								 $options['categorylist'], $options['excludecategorylist'], $options['showcategorydescheaders'], $options['showonecatonly'], '',
								 $options['loadingicon'], $options['catlistdescpos'], $genoptions['debugmode'], $options['pagination'], $options['linksperpage'],
								 $options['showcatlinkcount'], $options['showonecatmode']);   
	}
	else
		return PrivateLinkLibraryCategories($order, $hide_if_empty, $table_width, $num_columns, $catanchor, $flatlist, $categorylist, $excludecategorylist, $showcategorydescheaders,
		$showonecatonly, $settings, $loadingicon, $catlistdescpos, $debugmode, $pagination, $linksperpage, $showcatlinkcount, $showonecatmode);   
	
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
 *   showinvisible (default false) - Shows links that are set to be invisible
 *   showdate (default false) - Determines is link update date should be displayed
 *   beforedate (default null) - Code/Text to be displayed before link date
 *   afterdate (default null) - Code/Text to be displated after link date
 *   catdescpos (default 'right') - Position of link category description output
 *   showuserlinks (default false) - Specifies if user submitted links should be shown immediately after submission
 *   rsspreviewwidth (default 900) - Specifies the width of the box in which RSS previews are displayed
 *   rsspreviewheight (default 700) - Specifies the height of the box in which RSS previews are displayed
 *   beforeimage (default null) - Code/Text to be displayed before link image
 *   afterimage (default null) - Code/Text to be displayed after link image
 *   imagepos (default beforename) - Position of image relative to link name
 *   imageclass (default null) - Class that will be assigned to link images
 *   debugmode (default false) - Adds debug information as comments in the Wordpress output to facilitate remote debugging
 *   usethumbshotsforimages (default false) - Uses thumbshots.org to generate images for links
 *   showonecatmode (default AJAX) - Method used to load different categories when only showing one at a time
 */

function LinkLibrary($order = 'name', $hide_if_empty = true, $catanchor = true,
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
								$settings = '', $showinvisible = false, $showdate = false, $beforedate = '', $afterdate = '', $catdescpos = 'right',
								$showuserlinks = false, $rsspreviewwidth = 900, $rsspreviewheight = 700, $beforeimage = '', $afterimage = '', $imagepos = 'beforename',
								$imageclass = '', $AJAXpageid = 1, $debugmode = false, $usethumbshotsforimages = false, $showonecatmode = 'AJAX') {
								
	
	if (strpos($order, 'AdminSettings') !== false)
	{
		$settingsetid = substr($order, 13);
		$settingsetname = "LinkLibraryPP" . $settingsetid;
		$options = get_option($settingsetname);
		
		$genoptions = get_option('LinkLibraryGeneral');		

		return PrivateLinkLibrary($options['order'], $options['hide_if_empty'], $options['catanchor'], $options['showdescription'], $options['shownotes'],
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
								  $options['pagination'], $options['linksperpage'], $options['hidecategorynames'], $settingsetid, $options['showinvisible'],
								  $options['showdate'], $options['beforedate'], $options['afterdate'], $options['catdescpos'], $options['showuserlinks'],
								  $options['rsspreviewwidth'], $options['rsspreviewheight'], $options['beforeimage'], $options['afterimage'], $options['imagepos'],
								  $options['imageclass'], $AJAXpageid, $genoptions['debugmode'], $options['usethumbshotsforimages'], 'AJAX');	
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
								$pagination, $linksperpage, $hidecategorynames, $settings, $showinvisible, $showdate, $beforedate, $afterdate, $catdescpos,
								$showuserlinks, $rsspreviewwidth, $rsspreviewheight, $beforeimage, $afterimage, $imagepos, $imageclass, '', $debugmode,
								$usethumbshotsforimages, $showonecatmode);
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
		
	$genoptions = get_option('LinkLibraryGeneral');

	return PrivateLinkLibraryCategories($options['order'], $options['hide_if_empty'], $options['table_width'], $options['num_columns'], $options['catanchor'], $options['flatlist'],
								 $selectedcategorylist, $excludedcategorylist, $options['showcategorydescheaders'], $options['showonecatonly'], $settings,
								 $options['loadingicon'], $options['catlistdescpos'], $genoptions['debugmode'], $options['pagination'], $options['linksperpage'],
								 $options['showcatlinkcount'], $options['showonecatmode']);
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
	
	if ($_POST['link_name'])
	{
		$message = "<div class='llmessage'>" . $options['newlinkmsg'];
		if ($options['showuserlinks'] == false)
			$message .= ", " . $options['moderatemsg'];
		else
			$message .= ".";
			
		$message .= "</div>";
		
		echo $message;
		
		$newlinkcat = array($_POST['link_category']);
		
		if ($options['showuserlinks'] == false)
			$newlinkdesc = "(LinkLibrary:AwaitingModeration:RemoveTextToApprove)" . $_POST['link_description'];
		else
			$newlinkdesc = $_POST['link_description'];
			
		$newlink = array("link_name" => wp_specialchars(stripslashes($_POST['link_name'])), "link_url" => wp_specialchars(stripslashes($_POST['link_url'])), "link_rss" => wp_specialchars(stripslashes($_POST['link_rss'])),
			"link_description" => wp_specialchars(stripslashes($newlinkdesc)), "link_notes" => wp_specialchars(stripslashes($_POST['link_notes'])), "link_category" => $newlinkcat);
		wp_insert_link($newlink);
		
		if ($options['emailnewlink'])
		{
			$adminmail = get_option('admin_email');
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			
			$message = "A user submitted a new link to your Wordpress Link database.<br /><br />";
			$message .= "Link Name: " . wp_specialchars(stripslashes($_POST['link_name'])) . "<br />";
			$message .= "Link Address: " . wp_specialchars(stripslashes($_POST['link_url'])) . "<br />";
			$message .= "Link RSS: " . wp_specialchars(stripslashes($_POST['link_rss'])) . "<br />";
			$message .= "Link Description: " . wp_specialchars(stripslashes($_POST['link_description'])) . "<br />";
			$message .= "Link Notes: " . wp_specialchars(stripslashes($_POST['link_notes'])) . "<br />";
			$message .= "Link Category: " . $_POST['link_category'] . "<br /><br />";
						
			if ( !defined('WP_ADMIN_URL') )
				define( 'WP_ADMIN_URL', get_option('siteurl') . '/wp-admin');
				
			if ($options['showuserlinks'] == false)
				$message .= "<a href='" . WP_ADMIN_URL . "/link-manager.php?s=LinkLibrary%3AAwaitingModeration%3ARemoveTextToApprove'>Moderate new links</a>";
			elseif ($options['showuserlinks'] == true)
				$message .= "<a href='" . WP_ADMIN_URL . "/link-manager.php'>View links</a>";
				
			$message .= "<br /><br />Message generated by <a href='http://yannickcorner.nayanna.biz/wordpress-plugins/link-library/'>Link Library</a> for Wordpress";
			
			wp_mail($adminmail, htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . " - New link added: " . htmlspecialchars($_POST['link_name']), $message, $headers);
		}
	}
	
	if ($categorylistoverride != '')
		$selectedcategorylist = $categorylistoverride;
	else
		$selectedcategorylist = $options['categorylist'];
		
	if ($excludecategoryoverride != '')
		$excludedcategorylist = $excludecategoryoverride;
	else
		$excludedcategorylist = $options['excludecategorylist'];
	
	return PrivateLinkLibraryAddLinkForm($selectedcategorylist, $excludedcategorylist, $options['addnewlinkmsg'], $options['linknamelabel'], $options['linkaddrlabel'],
										 $options['linkrsslabel'], $options['linkcatlabel'], $options['linkdesclabel'], $options['linknoteslabel'],
										 $options['addlinkbtnlabel'], $options['hide_if_empty'], $options['showaddlinkrss'], $options['showaddlinkdesc'],
										 $options['showaddlinkcat'], $options['showaddlinknotes'], $options['addlinkreqlogin']);	
	
	
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
		
	$genoptions = get_option('LinkLibraryGeneral');
	
	$linklibraryoutput = "";
	
	if ($genoptions['debugmode'] == true)
		$linklibraryoutput .= "\n<!-- Setting Set Info:" . print_r($options, TRUE) . "-->\n";
		
	$linklibraryoutput .= PrivateLinkLibrary($options['order'], $options['hide_if_empty'], $options['catanchor'], $selectedshowdescription, $selectedshownotes,
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
								  $options['hidecategorynames'], $settings, $options['showinvisible'], $options['showdate'], $options['beforedate'],
								  $options['afterdate'], $options['catdescpos'], $options['showuserlinks'], $options['rsspreviewwidth'], $options['rsspreviewheight'],
								  $options['beforeimage'], $options['afterimage'], $options['imagepos'], $options['imageclass'], '', $genoptions['debugmode'],
								  $options['usethumbshotsforimages'], $options['showonecatmode']); 
		
	return $linklibraryoutput;
}

function populate_link_field($link_id) {
	global $wpdb;
	
	$tablename = $wpdb->prefix . "links";
	$wpdb->update( $tablename, array( 'link_updated' => date("Y-m-d H:i") ), array( 'link_id' => $link_id ));

}

function ll_rss_link() {
	global $rss_settings;
	
	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		
	// Guess the location
	$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
	
	if ($rss_settings != "")
	{
		$settingsname = 'LinkLibraryPP' . $rss_settings;
		$options = get_option($settingsname);	

		$feedtitle = ($options['rssfeedtitle'] == "" ? "Link Library Generated Feed" : $options['rssfeedtitle']);	
				
		echo '<link rel="alternate" type="application/rss+xml" title="' . wp_specialchars(stripslashes($feedtitle)) . '" href="' . $llpluginpath . 'rssfeed.php?settingset=' . $rss_settings . '" />';
	}
}

add_shortcode('link-library-cats', 'link_library_cats_func');

add_shortcode('link-library-search', 'link_library_search_func');

add_shortcode('link-library-addlink', 'link_library_addlink_func');

add_shortcode('link-library', 'link_library_func');

// adds the menu item to the admin interface
add_action('admin_menu', array('LL_Admin','add_config_page'));

add_filter('admin_head', 'admin_scripts'); // the_posts gets triggered before wp_head

add_action('wp_head', 'll_rss_link');

add_filter('the_posts', 'conditionally_add_scripts_and_styles'); // the_posts gets triggered before wp_head

add_action('add_link', 'populate_link_field');

add_action('edit_link', 'populate_link_field');

function admin_scripts() {
	echo '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/link-library/tiptip/jquery.tipTip.minified.js"></script>'."\n";
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('wpurl').'/wp-content/plugins/link-library/tiptip/tipTip.css">'."\n";
}

function conditionally_add_scripts_and_styles($posts){
	if (empty($posts)) return $posts;
	
	$load_jquery = false;
	$load_fancybox = false;
	$load_style = false;
	global $testvar;
	
	$genoptions = get_option('LinkLibraryGeneral');

	if (is_admin()) 
	{
		$load_jquery = false;
		$load_fancybox = false;
		$load_style = false;
	}
	else
	{
		foreach ($posts as $post) {		
			$continuesearch = true;
			$searchpos = 0;
			$settingsetids = array();
			
			while ($continuesearch) 
			{
				$linklibrarypos = stripos($post->post_content, 'link-library ', $searchpos);
				if ($linklibrarypos == false)
				{
					$linklibrarypos = stripos($post->post_content, 'link-library]', $searchpos);
					if ($linklibrarypos == false)
						if (stripos($post->post_content, 'link-library-cats') || stripos($post->post_content, 'link-library-addlink'))
							$load_style = true;
				}
				$continuesearch = $linklibrarypos;
				if ($continuesearch)
				{
					$load_style = true;
					$shortcodeend = stripos($post->post_content, ']', $linklibrarypos);
					if ($shortcodeend)
						$searchpos = $shortcodeend;
					else
						$searchpos = $linklibrarypos + 1;
						
					if ($shortcodeend)
					{
						$settingconfigpos = stripos($post->post_content, 'settings=', $linklibrarypos);
						if ($settingconfigpos && $settingconfigpos < $shortcodeend)
						{
							$settingset = substr($post->post_content, $settingconfigpos + 9, $shortcodeend - $settingconfigpos - 9);
								
							$settingsetids[] = $settingset;
						}
						else if (count($settingsetids) == 0)
						{
							$settingsetids[] = 1;
						}
					}
				}	
			}
		}
		
		if ($settingsetids)
		{
			foreach ($settingsetids as $settingsetid)
			{
				$settingsname = 'LinkLibraryPP' . $settingsetid;
				$options = get_option($settingsname);			
				
				if ($options['showonecatonly'])
				{
					$load_jquery = true;
				}
		
				if ($options['rsspreview'])
				{
					$load_fancybox = true;
				}

				if ($options['publishrssfeed'] == true)			
				{
					global $rss_settings;
					$rss_settings = $settingsetid;
				}	
			}
		}
			
		if ($genoptions['includescriptcss'] != '')
		{
			$pagelist = explode (',', $genoptions['includescriptcss']);
			foreach($pagelist as $pageid) {
				if (is_page($pageid))
				{
					$load_jquery = true;
					$load_fancybox = true;
					$load_style = true;
				}
			}
		}
	}
	
	if ($load_style)
	{		
		if ($genoptions == "")
			$genoptions['stylesheet'] = 'stylesheet.css';
			
		wp_enqueue_style('linklibrarystyle', get_bloginfo('wpurl') . '/wp-content/plugins/link-library/' . $genoptions['stylesheet']);	
	}
 
	if ($load_jquery)
	{
		wp_enqueue_script('jquery');
	}
		
	if ($load_fancybox)
	{
		wp_enqueue_script('fancyboxpack', get_bloginfo('wpurl') . '/wp-content/plugins/link-library/fancybox/jquery.fancybox-1.3.1.pack.js', "", "1.3.1");
		wp_enqueue_style('fancyboxstyle', get_bloginfo('wpurl') . '/wp-content/plugins/link-library/fancybox/jquery.fancybox-1.3.1.css');	
	}
 
	return $posts;
}
?>
