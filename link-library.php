<?
/*
Plugin Name: Link Library
Plugin URI: http://nayanna.biz/
Description: Functions to generate link library page with a list of link
categories with hyperlinks to the actual link lists. Other options are
the ability to display notes on top of descriptions, to only display
selected categories and to display names of links at the same time
as their related images.
Version: 0.3, Updated to support Wordpress 2.1
Author: Yannick Lefebvre
Author URI: http://nayanna.biz/

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright © 2005 Yannick Lefebvre

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

/*
 * function get_links_cats_anchor()
 *
 * added by Yannick Lefebvre
 *
 * Output a list of all links categories, listed by category, using the
 * settings in $wpdb->linkcategories and output it as table
 *
 * Parameters:
 *   order (default 'name')  - Sort link categories by 'name' or 'id'
 *   hide_if_empty (default true)  - Supress listing empty link categories
 *   table_witdh (default 100) - Width of table, percentage
 *   num_columns (default 1) - Number of columns in table
 *   catanchor (default false) - Determines if links to generated anchors should be created
 */

function get_links_cats_anchor($order = 'name', $hide_if_empty = 'obsolete', $table_width = 100, $num_columns = 1, $catanchor = false) {
	global $wpdb;
	$countcat = 0;

	$order = strtolower($order);

	// Handle link category sorting
	$direction = 'ASC';
	if (substr($order,0,1) == '_') {
		$direction = 'DESC';
		$order = substr($order,1);
	}

	if (!isset($direction)) $direction = '';
	// Fetch the link category data as an array of hashesa
	$cats = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0");

	// Display each category
	

	if ($cats) {
		echo '<div class="linktable">';
		echo '<table width="'. $table_width . '%">'."\n";
		foreach ( (array) $cats as $cat) {
			// Handle each category.
			// First, fix the sort_order info
			//$orderby = $cat['sort_order'];
			//$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;

			// Display the category name
			$countcat += 1;
			if (($countcat % $num_columns == 1) or ($countcat == 1) ) echo "<tr>\n";
						
			$catfront = '	<td><a ';
			if ($catanchor)
				$cattext = 'href="#' . $cat->cat_name . '" ';
			else
			    $cattext = '';
			$catitem = '>' . $cat->cat_name . "</a></td>\n";
			
			echo ($catfront . $cattext . $catitem);

			
			if ($countcat % $num_columns == 0) echo "</tr>\n";			
		}
	}
	if (($countcat % $num_columns = 3) or ($countcat == 1)) echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";
}

/** function get_links_notes()
 ** Gets the links associated with category n.
 ** Parameters:
 **   category (default -1)  - The category to use. If no category supplied
 **      uses all
 **   before (default '')  - the html to output before the link
 **   after (default '<br />')  - the html to output after the link
 **   between (default ' ')  - the html to output between the link/image
 **     and it's description. Not used if no image or show_images == true
 **   show_images (default true) - whether to show images (if defined).
 **   orderby (default 'id') - the order to output the links. E.g. 'id', 'name',
 **     'url', 'description', or 'rating'. Or maybe owner. If you start the
 **     name with an underscore the order will be reversed.
 **     You can also specify 'rand' as the order which will return links in a
 **     random order.
 **   show_description (default true) - whether to show the description if
 **    show_images=false/not defined .
 **   show_rating (default false) - show rating stars/chars
 **   limit (default -1) - Limit to X entries. If not specified, all entries
 **     are shown.
 **   show_updated (default 0) - whether to show last updated timestamp
 **   show_notes - determines if notes should be displayed in addition to description
 **   show_image_and_name (default false) - Show both image and name instead of only one or the other
 **   use_html_tags (default false) - Use HTML tags for formatting instead of just displaying them
 **   show_rss (default false) - Display RSS URI if available in link description
 **   beforenote (default <br />) - Code to print out between the description and notes
 */
 
function get_links_notes($category = '', $before = '', $after = '<br />',
                   $between = ' ', $show_images = true, $orderby = 'name',
                   $show_description = true, $show_rating = false,
                   $limit = -1, $show_updated = 1, $show_notes = false, $show_image_and_name = false, $use_html_tags = false, 
				   $show_rss = false, $beforenote = '<br />', $echo = true
				   ) {

    global $wpdb;

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
        if ($show_updated && $row->recently_updated)
            $output .= get_option('links_recently_updated_prepend');
			
        $the_link = '#';
        if (!empty($row->link_url) )
            $the_link = wp_specialchars($row->link_url);

        $rel = $row->link_rel;
        if ('' != $rel )
            $rel = ' rel="' . $rel . '"';
		
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
		
        $output .= '</a>';
		
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
            $output .= $between . $desc;

		if ($show_notes && ($descnotes != '')) {
			$output .= $beforenote . $between . $descnotes;
		}
		if ($show_rss && ($row->link_rss != '')) {
		    $output .= $between . '<a id="rss" href="' . $row->link_rss . '">RSS</a>';
		}
        $output .= "$after\n";
    } // end while
	
	if ( !$echo )
		return $output;
	echo $output;
}

/*
 * function get_links_anchor()
 *
 * added by Yannick Lefebvre
 *
 * Output a list of all links, listed by category, using the
 * settings in $wpdb->linkcategories and output it as a nested
 * HTML unordered list. Can also insert anchors for categories
 *
 * Parameters:
 *   order (default 'name')  - Sort link categories by 'name' or 'id'
 *   hide_if_empty (default true)  - Supress listing empty link categories
 *   catanchor (default false) - Adds name anchors to categorie links to be able to link directly to categories\
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
 */

function get_links_anchor_notes($order = 'name', $hide_if_empty = 'obsolete', $catanchor = false,
                                $showdescription = false, $shownotes = false, $showrating = false,
								$showupdated = false, $categorylist = '', $show_images = false, 
								$show_image_and_name = false, $use_html_tags = false, 
								$show_rss = false, $beforenote = '<br />') {
	global $wpdb;

	$order = strtolower($order);

	// Handle link category sorting
	$direction = 'ASC';	
	if ('_' == substr($order,0,1)) {
		$direction = 'DESC';
		$order = substr($order,1);
	}

	if ($categorylist != '')
	   $catsearch = ' AND link_category in (' . $categorylist . ') ';
	else
	   $catsearch = '';
	   
	if (!isset($direction)) $direction = '';

	// Fetch the link category data as an array of hashesa

	$cats = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0");
	
    // Display each category
	if ($cats) {
		echo '<div class="linklist">'."\n";
		foreach ( (array) $cats as $cat) {
		
			// Display the category name
			$catfront = '	';
			if ($catanchor)
				$cattext = '<a name="' . $cat->cat_name . '"></a>';
			else
			    $cattext = '';
			$catlink = '<h2>' . $cat->cat_name . "</h2>\n\t<ul>\n";
			
			echo ($catfront . $cattext . $catlink);
			// Call get_links() with all the appropriate params
			get_links_notes($cat->cat_name,
				'<li>',"</li><br />","\n",
				$show_images,
				'name',
				$showdescription,
				$showrating,
				-1,
				$showupdated,
				$shownotes,
				$show_image_and_name,
			    $use_html_tags,
				$show_rss,
				$beforenote);
				
			// Close the last category
			echo "\t</ul>\n";
		}
		echo "</div>\n";
	}
}

?>