WordPress Plugin
Link Library 0.2 (http://nayanna.biz)
For WordPress 1.5.
------------------------------------------------------

This plugin is available on http://dev.wp-plugins.org/browser/link-library/

The purpose of this plugin is to add some functions to be able to produce a complete link page with
text descriptions for all items. It provides the following functions:

1) Function to print out a list of link categories in table format with option links to category
anchors
2) Function to print a tree of links with link categories. New Options are to insert anchors to
categories and to display link notes in addition to categories.
3) Function to print links. New option is to display notes in addition to descriptions. New option to only display links from selected categories. New option to print both images and names of links.

You can see this plugin in action at: http://nayanna.biz/index.php/useful-links/. I hope you will find it useful.

To use the plugin:

   1. Drop into your plugins directory and activate.
   2. Edit your links.php file in your themes folder and use the following functions:
   
   Print out list of link categories:   
   <?php get_links_cats_anchor('name', 1, 50, 3, 1); ?>
   
   Print out full link list:   
   <?php get_links_anchor_notes('name', 1, 1, 1); ?>
   
   Print out link list from selected categories:
   <?php get_links_anchor_notes('name', 1, 1, 1, '7,2'); ?>
   
   Print out link list from all categories and show both link name and image:
   <?php get_links_anchor_notes('name', 1, 1, 1, '', 1); ?>

   3. Use links.php as the template for your links page and you're good to go or use these functions in a new template for other pages.
   4. View website to confirm proper operation

0.1 Changes and Notes:

	* Works with WordPress 1.5.
	* First release of the link-library plugin

------------------------------------------------------

Each function has a few parameters that need to be configured.

<?php get_links_cats_anchor('name', 1, 50, 3, 1); ?>
 
Parameters:

 *   order (default 'name') - Sort link categories by 'name' or 'id'
 *   hide_if_empty (default true)  - Supress listing empty link categories
 *   table_witdh (default 100) - Width of table, percentage
 *   num_columns (default 1) - Number of columns in table
 *   catanchor (default false) - Determines if links to generated anchors should be created

A few CSS classes have been inserted to enable formatting of the plugin output.

<?php get_links_anchor_notes('name', 1, 1, 1); ?> 

Parameters:

 *   order (default 'name')  - Sort link categories by 'name' or 'id'
 *   hide_if_empty (default true)  - Supress listing empty link categories
 *   catanchor (default false) - Adds name anchors to categorie links to be able to link directly to categories
 *   shownotes (default false) - Shows notes in addition to description for links (useful since notes field is larger than description)
 *   categorylist (default null) - Only show links inside of selected categories. Enter category numbers in a string separated by commas
 *   show_image_and_name (default false) - Show both image and name instead of only one or the other

 
The get_links_notes function adds the following parameters to the original Wordpress get_links:

 **   show_notes - determines if notes should be displayed in addition to description
 **   show_image_and_name (default false) - Show both image and name instead of only one or the other

Place this code in the wp-layout.css file.