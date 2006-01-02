<?
/*
Plugin Name: Link Library
Plugin URI: http://nayanna.biz/
Description: Functions to generate link library page with a list of link
categories with hyperlinks to the actual link lists. Other options are
the ability to display notes on top of descriptions, to only display
selected categories and to display names of links at the same time
as their related images.
Version: 0.2
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
	if (substr($order,0,1) == '_') {
		$direction = ' DESC';
		$order = substr($order,1);
	}

	// if 'name' wasn't specified, assume 'id':
	$cat_order = ('name' == $order) ? 'cat_name' : 'cat_id';

	if (!isset($direction)) $direction = '';
	// Fetch the link category data as an array of hashesa
	$cats = $wpdb->get_results("
		SELECT DISTINCT link_category, cat_name, show_images, 
			show_description, show_rating, show_updated, sort_order, 
			sort_desc, list_limit
		FROM `$wpdb->links` 
		LEFT JOIN `$wpdb->linkcategories` ON (link_category = cat_id)
		WHERE link_visible =  'Y'
			AND list_limit <> 0
		ORDER BY $cat_order $direction ", ARRAY_A);

	// Display each category
	

	if ($cats) {
		echo '<div class="linktable">';
		echo '<table width="'. $table_width . '%">'."\n";
		foreach ($cats as $cat) {
			// Handle each category.
			// First, fix the sort_order info
			$orderby = $cat['sort_order'];
			$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;

			// Display the category name
			$countcat += 1;
			if (($countcat % $num_columns == 1) or ($countcat == 1) ) echo "<tr>\n";
						
			$catfront = '	<td><a ';
			if ($catanchor)
				$cattext = 'href="#' . $cat['cat_name'] . '" ';
			else
			    $cattext = '';
			$catitem = '>' . $cat['cat_name'] . "</a></td>\n";
			
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
function get_links_notes($category = -1, $before = '', $after = '<br />',
                   $between = ' ', $show_images = true, $orderby = 'name',
                   $show_description = true, $show_rating = false,
                   $limit = -1, $show_updated = 1, $show_notes = false, $show_image_and_name = false, $use_html_tags = false, 
				   $show_rss = false, $beforenote = '<br />', $echo = true
				   ) {

    global $wpdb;

    $direction = ' ASC';
    $category_query = "";
    if ($category != -1) {
        $category_query = " AND link_category = $category ";
    }
    if (get_settings('links_recently_updated_time')) {
        $recently_updated_test = ", IF (DATE_ADD(link_updated, INTERVAL ".get_settings('links_recently_updated_time')." MINUTE) >= NOW(), 1,0) as recently_updated ";
    } else {
		$recently_updated_test = '';
	}
    if ($show_updated) {
        $get_updated = ", UNIX_TIMESTAMP(link_updated) AS link_updated_f ";
    }

    $orderby=strtolower($orderby);
    if ($orderby == '')
        $orderby = 'id';
    if (substr($orderby,0,1) == '_') {
        $direction = ' DESC';
        $orderby = substr($orderby,1);
    }

    switch($orderby) {
        case 'length':
        $length = ",CHAR_LENGTH(link_name) AS length";
        break;
        case 'rand':
            $orderby = 'rand()';
            break;
        default:
            $orderby = " link_" . $orderby;
    }

    if (!isset($length)) {
		$length = "";
	}

    $sql = "SELECT link_url, link_name, link_image, link_target,
            link_description, link_rating, link_rel, link_notes, link_rss $length $recently_updated_test $get_updated
            FROM $wpdb->links
            WHERE link_visible = 'Y' " .
           $category_query;
    $sql .= ' ORDER BY ' . $orderby;
    $sql .= $direction;
    /* The next 2 lines implement LIMIT TO processing */
    if ($limit != -1)
        $sql .= " LIMIT $limit";
    //echo $sql;
    $results = $wpdb->get_results($sql);
    if (!$results) {
        return;
    }
    foreach ($results as $row) {
		if (!isset($row->recently_updated)) $row->recently_updated = false;
        echo($before);
        if ($show_updated && $row->recently_updated) {
            echo get_settings('links_recently_updated_prepend');
        }
        $the_link = '#';
        if (($row->link_url != null) && ($row->link_url != '')) {
            $the_link = $row->link_url;
        }
        $rel = $row->link_rel;
        if ($rel != '') {
            $rel = " rel='$rel'";
        }
		
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
                $title .= ' (Last updated ' . date(get_settings('links_updated_date_format'), $row->link_updated_f + (get_settings('gmt_offset') * 3600)) .')';
            }
        }

        if ('' != $title) {
            $title = " title='$title'";
        }

        $alt = " alt='$name'";
            
        $target = $row->link_target;
        if ('' != $target) {
            $target = " target='$target'";
        }
        echo("<a href='$the_link'");
        echo($rel . $title . $target);
        echo('>');
        if (($row->link_image != null) && $show_images) {
			if (strstr($row->link_image, 'http'))
				echo "<img src='$row->link_image' $alt $title />";
			else // If it's a relative path
            	echo "<img src='" . get_settings('siteurl') . "$row->link_image' $alt $title />";
			if ($show_image_and_name) {
			    echo($between.$name);
			}
        } else {
            echo($name);
        }
        echo('</a>');
        if ($show_updated && $row->recently_updated) {
            echo get_settings('links_recently_updated_append');
        }

		if ($use_html_tags) {
			$desc = $row->link_description;
		}
		else {
			$desc = wp_specialchars($row->link_description, ENT_QUOTES);
		}
		
        if ($show_description && ($desc != '')) {
            echo($between.$desc);
        }

		if ($show_notes && ($descnotes != '')) {
			echo("$beforenote\n");
		    echo($between.$descnotes);
		}
		if ($show_rss && ($row->link_rss != '')) {
		    echo($between . '<a id="rss" href="'.$row->link_rss.'">RSS</a>');
		}
        echo("$after\n");
    } // end while
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
 *   catanchor (default false) - Adds name anchors to categorie links to be able to link directly to categories
 *   shownotes (default false) - Shows notes in addition to description for links (useful since notes field is larger than description)
 *   categorylist (default null) - Only show links inside of selected categories. Enter category numbers in a string separated by commas
 *   show_image_and_name (default false) - Show both image and name instead of only one or the other
 *   use_html_tags (default false) - Use HTML tags for formatting instead of just displaying them
 *   show_rss (default false) - Display RSS URI if available in link description
 *   beforenote (default <br />) - Code to print out between the description and notes
 */

function get_links_anchor_notes($order = 'name', $hide_if_empty = 'obsolete', $catanchor = false, $shownotes = false, $categorylist = '', $show_image_and_name = false, $use_html_tags = false, $show_rss = false, $beforenote = '<br />') {
	global $wpdb;

	$order = strtolower($order);

	// Handle link category sorting
	if (substr($order,0,1) == '_') {
		$direction = ' DESC';
		$order = substr($order,1);
	}

	// if 'name' wasn't specified, assume 'id':
	$cat_order = ('name' == $order) ? 'cat_name' : 'cat_id';
	
	if ($categorylist != '')
	   $catsearch = ' AND link_category in (' . $categorylist . ') ';
	else
	   $catsearch = '';
	   
	if (!isset($direction)) $direction = '';

	// Fetch the link category data as an array of hashesa

	$sql = "SELECT DISTINCT link_category, cat_name, show_images, 
			show_description, show_rating, show_updated, sort_order, 
			sort_desc, list_limit
		FROM $wpdb->links
		LEFT JOIN `$wpdb->linkcategories` ON (link_category = cat_id)
		WHERE link_visible =  'Y'
			AND list_limit <> 0" .
			$catsearch;
    $sql .= ' ORDER BY ' . $cat_order . $direction;
    $cats = $wpdb->get_results($sql, ARRAY_A);
	
    // Display each category
	if ($cats) {
		echo '<div class="linklist">'."\n";
		foreach ($cats as $cat) {
			// Handle each category.
			// First, fix the sort_order info
			$orderby = $cat['sort_order'];
			$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;

			// Display the category name
			$catfront = '	';
			if ($catanchor)
				$cattext = '<a name="' . $cat['cat_name'] . '"></a>';
			else
			    $cattext = '';
			$catlink = '<h2>' . $cat['cat_name'] . "</h2>\n\t<ul>\n";
			
			echo ($catfront . $cattext . $catlink);
			// Call get_links() with all the appropriate params
			get_links_notes($cat['link_category'],
				'<li>',"</li><br />","\n",
				bool_from_yn($cat['show_images']),
				$orderby,
				bool_from_yn($cat['show_description']),
				bool_from_yn($cat['show_rating']),
				$cat['list_limit'],
				bool_from_yn($cat['show_updated']),
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