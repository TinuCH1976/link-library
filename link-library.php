<?
/*
Plugin Name: Link Library
Plugin URI: http://wordpress.org/extend/plugins/link-library/
Description: Functions to generate link library page with a list of link
categories with hyperlinks to the actual link lists. Other options are
the ability to display notes on top of descriptions, to only display
selected categories and to display names of links at the same time
as their related images.
Version: 1.0
Author: Yannick Lefebvre
Author URI: http://yannickcorner.nayanna.biz/

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright © 2007 Yannick Lefebvre

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


// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

// Guess the location
$llpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
 
 
 
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
			if ( isset($_GET['reset']) && $_GET['reset'] == "true") {
					$options['order'] = 'name';
					$options['hide_if_empty'] = true;
					$options['table_width'] = 100;
					$options['num_columns'] = 1;
					$options['catanchor'] = false;
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
				update_option('LinkLibraryPP',$options);
			}
			if ( isset($_POST['submit']) ) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the Link Library for WordPress options.'));
				check_admin_referer('linklibrarypp-config');
				
				foreach (array('order', 'table_width', 'num_columns', 'categorylist', 'excludecategorylist', 'beforenote', 'afternote','position',
							   'beforeitem', 'afteritem', 'beforedesc', 'afterdesc', 'beforelink','afterlink','linkheader', 'descheader', 'notesheader') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = strtolower($_POST[$option_name]);
					}
				}
				
				foreach (array('hide_if_empty', 'catanchor', 'showdescription', 'shownotes', 'showrating', 'showupdated', 'show_images', 
								'show_image_and_name', 'use_html_tags', 'show_rss', 'nofollow','showcolumnheaders') as $option_name) {
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

				update_option('LinkLibraryPP', $options);
			}

			$options  = get_option('LinkLibraryPP');
			?>
			<div class="wrap">
				<h2>Link Library Configuration</h2>
				<form action="" method="post" id="analytics-conf">
					<table class="form-table" style="width:100%;">
					<?php
					if ( function_exists('wp_nonce_field') )
						wp_nonce_field('linklibrarypp-config');
					?>
					<tr>
						<th scope="row" valign="top">
							<label for="order">Results Order</label>
						</th>
						<td>
							<select name="order" id="order" style="width:200px;">
								<option value="name"<?php if ($options['order'] == 'name') { echo ' selected="selected"';} ?>>Order by Name</option>
								<option value="id"<?php if ($options['order'] == 'id') { echo ' selected="selected"';} ?>>Order by ID</option>
							</select>
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
					<?php if ($options['flatlist'] == false) { ?>		
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
					<?php } ?>
					<tr>
						<th scope="row" valign="top">
							<label for="catanchor">Embed HTML anchors</label>
						</th>
						<td>
							<input type="checkbox" id="catanchor" name="catanchor" <?php if ($options['catanchor']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="categorylist">Categories to be displayed (comma-separated)</label>
						</th>
						<td>
							<input type="text" id="categorylist" name="categorylist" size="40" value="<?php echo $options['categorylist']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="excludecategorylist">Categories to be excluded (comma-separated)</label>
						</th>
						<td>
							<input type="text" id="excludecategorylist" name="excludecategorylist" size="40" value="<?php echo $options['excludecategorylist']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
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
					<?php if ($options['displayastable'] == true) { ?>		
					<tr>
						<th scope="row" valign="top">
							<label for="showcolumnheaders">Show Column Headers</label>
						</th>
						<td>
							<input type="checkbox" id="showcolumnheaders" name="showcolumnheaders" <?php if ($options['showcolumnheaders']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<?php if ($options['showcolumnheaders'] == true) { ?>	
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
					<?php } ?>						
					<?php } ?>						
					<tr>
						<th scope="row" valign="top">
							<label for="beforeitem">Output before complete link item</label>
						</th>
						<td>
							<input type="text" id="beforeitem" name="beforeitem" size="40" value="<?php echo $options['beforeitem']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afteritem">Output after complete link item</label>
						</th>
						<td>
							<input type="text" id="afteritem" name="afteritem" size="40" value="<?php echo $options['afteritem']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="beforelink">Output before individual link item</label>
						</th>
						<td>
							<input type="text" id="beforelink" name="beforelink" size="40" value="<?php echo $options['beforelink']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afterlink">Output after individual link item</label>
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
					<?php if ($options['showdescription'] == true) { ?>		
					<tr>
						<th scope="row" valign="top">
							<label for="beforedesc">Output before Link Description</label>
						</th>
						<td>
							<input type="text" id="beforedesc" name="beforedesc" size="40" value="<?php echo $options['beforedesc']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afterdesc">Output after Link Description</label>
						</th>
						<td>
							<input type="text" id="afternote" name="afternote" size="40" value="<?php echo $options['afternote']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<?php } ?>						
					<tr>
						<th scope="row" valign="top">
							<label for="shownotes">Show Link Notes</label>
						</th>
						<td>
							<input type="checkbox" id="shownotes" name="shownotes" <?php if ($options['shownotes']) echo ' checked="checked" '; ?>/>
						</td>
					</tr>
					<?php if ($options['shownotes'] == true) { ?>		
					<tr>
						<th scope="row" valign="top">
							<label for="beforenote">Output before Link Note</label>
						</th>
						<td>
							<input type="text" id="beforenote" name="beforenote" size="40" value="<?php echo $options['beforenote']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<tr>
						<th scope="row" valign="top">
							<label for="afternote">Output after Link Note</label>
						</th>
						<td>
							<input type="text" id="afternote" name="afternote" size="40" value="<?php echo $options['afternote']; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;"/>
						</td>
					</tr>	
					<?php } ?>	
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
							<label for="show_rss">Show RSS Link</label>
						</th>
						<td>
							<input type="checkbox" id="show_rss" name="show_rss" <?php if ($options['show_rss']) echo ' checked="checked" '; ?>/>
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

					
					</table>
					<p style="border:0;" class="submit"><input type="submit" name="submit" value="Update Settings &raquo;" /></p>
					
					<p><a href="?page=link-library.php&amp;reset=true">Reset all settings</a></p>
				</form>
			</div>
			<?php

		} // end config_page()

		function restore_defaults() {
				$options['order'] = 'name';
				$options['hide_if_empty'] = true;
				$options['table_width'] = 100;
				$options['num_columns'] = 1;
				$options['catanchor'] = false;
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
	
			update_option('LinkLibraryPP',$options);
		}
		
	} // end class LL_Admin

} //endif


function get_links_cats_anchor() {

	$options  = get_option('LinkLibraryPP');
	
	$countcat = 0;

	$order = strtolower($options['order']);

	// Handle link category sorting
	$direction = 'ASC';
	if (substr($order,0,1) == '_') {
		$direction = 'DESC';
		$order = substr($order,1);
	}

	if (!isset($direction)) $direction = '';
	// Fetch the link category data as an array of hashesa
	
	$selectedcategorylist = $options['categorylist'];
	$excludedcategorylist = $options['excludecategorylist'];
	$cats = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0&include=$selectedcategorylist&exclude=$excludedcategorylist");

	// Display each category

	if ($cats) {
		$output =  "<div class=\"linktable\">";
		
		if (!$options['flatlist'])
			$output .= "<table width=\"" . $options['table_width'] . "%\">\n";
		else
			$output .= "<ul>\n";
			
		foreach ( (array) $cats as $cat) {
			// Handle each category.
			// First, fix the sort_order info
			//$orderby = $cat['sort_order'];
			//$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;
			
			if ($cat->category_parent == 0)
			{

				// Display the category name
				$countcat += 1;
				if (!$options['flatlist'] and (($countcat % $options['num_columns'] == 1) or ($countcat == 1) )) $output .= "<tr>\n";
							
				if (!$options['flatlist'])
					$catfront = '	<td><a ';
				else
					$catfront = '	<li><a ';
				$linkcatnospaces = str_replace ( ' ', '', $cat->cat_name );
	
				if ($options['catanchor'])
					$cattext = 'href="#' . $linkcatnospaces . '" ';
				else
					$cattext = '';
	
				$catitem = '>' . $cat->cat_name . "</a>";
				
				$output .= ($catfront . $cattext . $catitem );
					
				if (!$options['flatlist'])
					$catterminator = "	</td>\n";
				else
					$catterminator = "	</li>\n";
				
				$output .= ($catterminator);
	
				
				if (!$options['flatlist'] and ($countcat % $options['num_columns'] == 0)) $output .= "</tr>\n";
			}
		}
	}
	if (!$options['flatlist'] and (($countcat % $options['num_columns'] = 3) or ($countcat == 1))) $output .= "</tr>\n";
	if (!$options['flatlist'])
		$output .= "</table>\n";
	else
		$output .= "</ul>";
	$output .= "</div>\n";
	
	return $output;
}


function get_links_notes($category = '', $before = '', $after = '<br />',
                   $between = ' ', $show_images = true, $orderby = 'name',
                   $show_description = true, $show_rating = false,
                   $limit = -1, $show_updated = 1, $show_notes = false, $show_image_and_name = false, $use_html_tags = false, 
				   $show_rss = false, $beforenote = '<br />', $afternote = '', $nofollow = false, $echo = true,
				   $beforedesc = '', $afterdesc = '', $beforelink = '', $afterlink = ''
				   ) {

	$order = 'ASC';
	if ( substr($orderby, 0, 1) == '_' ) {
		$order = 'DESC';
		$orderby = substr($orderby, 1);
	}

	if ( $category == -1 ) //get_bookmarks uses '' to signify all categories
		$category = '';
		
    $results = get_bookmarks("category_name=$category&orderby=$orderby&order=$order&show_updated=$show_updated&limit=$limit");

	if ( !$results )
		return;
		
	$output = '';
	
    foreach ( (array) $results as $row) {
		if (!isset($row->recently_updated)) $row->recently_updated = false;
        $output .= $before;
		$output .= $beforelink;
        if ($show_updated && $row->recently_updated)
            $output .= get_option('links_recently_updated_prepend');
			
        $the_link = '#';
        if (!empty($row->link_url) )
            $the_link = wp_specialchars($row->link_url);

        $rel = $row->link_rel;
		if ('' != $rel and $nofollow)
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

        $output .= '<a href="' . $the_link . '"' . $rel . $title . $target. '>';
		
        if ( $row->link_image != null && $show_images ) {
			if ( strpos($row->link_image, 'http') !== false )
				$output .= "<img src=\"$row->link_image\" $alt $title />";
			else // If it's a relative path
				$output .= "<img src=\"" . get_option('siteurl') . "$row->link_image\" $alt $title />";
		} else {
			$output .= $name;
		}
		
        $output .= '</a>' . $afterlink;
		
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

		if ($show_notes && ($descnotes != '')) {
			$output .= $beforenote . $between . $descnotes;
		}
		if ($show_rss && ($row->link_rss != '')) {
		    $output .= $between . '<a class="rss" href="' . $row->link_rss . '">RSS</a>';
		}
        $output .= "$after\n";
    } // end while
	
	return $output;
}

function get_links_anchor_notes() {
								
	$options = get_option('LinkLibraryPP');
								
	$order = strtolower($options['order']);

	// Handle link category sorting
	$direction = 'ASC';	
	if ('_' == substr($order,0,1)) {
		$direction = 'DESC';
		$order = substr($order,1);
	}

	if (!isset($direction)) $direction = '';

	// Fetch the link category data as an array of hashesa

	$selectedcategorylist = $options['categorylist'];
	$excludedcategorylist = $options['excludecategorylist'];

	$cats = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0&include=$selectedcategorylist&exclude=$excludedcategorylist");
	
    // Display each category
	if ($cats) {
		$output = "<div class=\"linklist\">\n";
		
		foreach ( (array) $cats as $cat) {
		
			if ($cat->category_parent == 0)
			{

				$linkcatnospaces = str_replace ( ' ', '', $cat->cat_name );
			
				// Display the category name
				$catfront = '	';
				if ($catanchor)
					$cattext = '<div id="' . $linkcatnospaces . '">';
				else
					$cattext = '';
				$catlink = '<h2>' . $cat->cat_name . "</h2>";
				if ($catanchor)
					$catenddiv = '</div>';
				else
					$catenddiv = '';
					
					
					
				if ($options['displayastable'] == true)
				{
					$catstartlist = "\n\t<table class='linklisttable'>\n";
					if ($options['showcolumnheaders'] == true)
						$catstartlist .= '<tr><td><h3>'.$options['linkheader'].'</h3></td><td><h3>'.$options['descheader'].'</h3></td><td><h3>'.$options['notesheader'].'</h3></td></tr>'."\n";
					else
						$catstartlist .= '';
				}
				else
					$catstartlist = "\n\t<ul>\n";
					
				
				$output .= $catfront . $cattext . $catlink . $catenddiv . $catstartlist; 
				
				// Call get_links() with all the appropriate params
				$linklist = get_links_notes($cat->cat_name,
					$options['beforeitem'],$options['afteritem'],"\n",
					$options['show_images'],
					$options['order'],
					$options['showdescription'],
					$options['$showrating'],
					-1,
					$options['showupdated'],
					$options['shownotes'],
					$options['show_image_and_name'],
					$options['use_html_tags'],
					$options['show_rss'],
					$options['beforenote'],
					$options['afternote'],
					$options['nofollow'],
					1,
					$options['beforedesc'],
					$options['afterdesc'],
					$options['beforelink'],
					$options['afterlink']);
					
				$output .= $linklist;
								
				// Close the last category
				if ($options['displayastable'])
					$output .= "\t</table>\n";
				else
					$output .= "\t</ul>\n";
			}
		}
		$output .= "</div>\n";
		
	}
	
	return $output;
}

function link_library_callback($content)
{	
	if(!preg_match('<!--Link Library Categories-->', $content) && !preg_match('<!--Link Library-->', $content)) {
		return $content;
	}
	
	//$linklibrarycats = get_links_cats_anchor('name', 1, 100, 3, 1, 0, '', '');
	$linklibrarycats = get_links_cats_anchor();
	
	//$linklibrary = get_links_anchor_notes('name', 1, 1, 1, 1, 0, 0, '', 0, 0, 1, 1, '<td>', '</td>', 1, '', '<tr>', '</tr>', '<td>', '</td>', 1, '<td>', '</td>', 1, "Application", "Description", "Similar to");
	$linklibrary = get_links_anchor_notes();
	
	$tempcontent = str_replace('<!--Link Library Categories-->', $linklibrarycats, $content);
	
	return str_replace('<!--Link Library-->', $linklibrary, $tempcontent);
}


$version = "1.0";

$options  = get_option('LinkLibraryPP',"");

if ($options == "") {
	$options['order'] = 'name';
	$options['hide_if_empty'] = true;
	$options['table_width'] = 100;
	$options['num_columns'] = 1;
	$options['catanchor'] = false;
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
	
	
	update_option('LinkLibraryPP',$options);
} 

// adds the menu item to the admin interface
add_action('admin_menu', array('LL_Admin','add_config_page'));

add_filter('the_content', 'link_library_callback', 50);

?>
