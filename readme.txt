WordPress Plugin
Link Library 0.1 (http://nayanna.biz)
For WordPress 1.5.
------------------------------------------------------

This plugin is available on http://dev.wp-plugins.org/browser/link-library/

The purpose of this plugin is to add some functions to be able to produce a complete link page with
text descriptions for all items. It provides three functions:

1) Function to print out a list of link categories in table format with option links to category
anchors
2) Function to print a tree of links with link categories. New Options are to insert anchors to
categories and to replace link descriptions with link notes.
3) Function to print links. New option is to replace link description with notes.

You can see this plugin in action at: http://nayanna.biz/index.php/useful-links/. I hope you will find it useful.

To use the plugin:

   1. Drop into your plugins directory and activate.
   2. Edit your links.php file in your themes folder and use the following functions:
   
   Print out list of link categories:   
   <?php get_links_cats_anchor('name', 1, 50, 3, 1); ?>
   
   Print out full link list:   
   <?php get_links_anchor_notes('name', 1, 1, 1); ?>

   3. Use links.php as the template for your links page and you're good to go.
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
 *   shownotes (default false) - Shows notes instead of description for links (useful since notes field is larger than description)
 
The get_links_notes function only adds one parameters to the original Wordpress get_links to specify is link notes should replace link descriptions.

Place this code in the wp-layout.css file.