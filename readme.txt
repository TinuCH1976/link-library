WordPress Plugin
Link Library 0.2 (http://nayanna.biz)
For WordPress 1.5.
------------------------------------------------------

This plugin is available on http://dev.wp-plugins.org/browser/link-library/

The purpose of this plugin is to add some functions to be able to produce a complete link page with
text descriptions for all items. It provides the following functions:

1) Function to print out a list of link categories in table format with option links to category
anchors (get_links_cats_anchor)
2) Function to print a tree of links with link categories. New Options are to insert anchors to
categories and to display link notes in addition to categories. (get_links_anchor_notes)
3) Function to print links. New option is to display notes in addition to descriptions. New option to only display links from selected categories. New option to print both images and names of links. (get_links_notes function)

You can see this plugin in action at: http://nayanna.biz/index.php/useful-links/ or at http://www.coldclimategardening.com/garden-blog-directory/. I hope you will find it useful.

To use the plugin:

   1. Drop into your plugins directory and activate.
   2. Use these functions in links.php for your links page or use these functions in a new template for other pages. Find your links.php file in your themes folder. 

Each function has a few parameters that need to be configured.

<?php get_links_cats_anchor('name', 1, 50, 3, 1); ?>
 
Parameters:

 *   order (default 'name') - Sort link categories by 'name' or 'id'
 *   hide_if_empty (default true)  - Suppress listing empty link categories
 *   table_width (default 100) - Width of table, percentage
 *   num_columns (default 1) - Number of columns in table
 *   catanchor (default false) - Determines if links to generated anchors should be created

A few CSS classes have been inserted to enable formatting of the plugin output. For example, when the show_css parameter is enabled, a tag is inserted for class rss. This can be used to display an RSS button on the page completed created using a style sheet.

<?php get_links_anchor_notes('name', 1, 1, 1); ?> 

Parameters:

 *   order (default 'name')  - Sort link categories by 'name' or 'id'
 *   hide_if_empty (default true)  - Supress listing of empty link categories
 *   catanchor (default false) - Adds name anchors to category links to be able to link directly to categories
 *   shownotes (default false) - Shows notes in addition to description for links (useful since notes field is larger than description)
 *   categorylist (default null) - Only show links inside of selected categories. Enter category numbers in a string separated by commas
 *   show_image_and_name (default false) - Show both image and name instead of only one or the other
 *   use_html_tags (default false) - Use HTML tags for formatting instead of just displaying them
 *   show_rss (default false) - Display RSS URI if available in link description (This is done by adding a class id called rss in the output page. This tag can then be formatted using a style sheet. Please refer to the site http://schinckel.blogsome.com/2005/06/01/rss-icons-as-css-code/ for an example on creating such a style sheet entry)
 *   beforenote (default <br />) - Code to print out between the description and notes
 */
 
The get_links_notes function adds the following parameters to the original Wordpress get_links: Even though this is an original template tag and the information is shown in the Codex, I think you should keep the same format of showing the actual tag with parameters after it.

Parameters:

 **   category (default -1)  - The category to use. If no category supplied uses all
 **   before (default '')  - the html to output before the link
 **   after (default '<br />')  - the html to output after the link
 **   between (default ' ')  - the html to output between the link/image
 **     and it's description. Not used if no image or show_images == true 
 **   show_images (default true) - whether to show images (if defined).
 **   orderby (default 'id') - the order to output the links. E.g. 'id', 'name',
 **     'url', 'description', or 'rating'. Or maybe owner. If you start the name with an underscore the order will be reversed.
 **     You can also specify 'rand' as the order which will return links in a random order.
 **   show_description (default true) - whether to show the description if show_images=false/not defined .
 **   show_rating (default false) - show rating stars/chars
 **   limit (default -1) - Limit to X entries. If not specified, all entries are shown.
 **   show_updated (default 0) - whether to show last updated timestamp
 **   show_notes - determines if notes should be displayed in addition to description
 **   show_image_and_name (default false) - Show both image and name instead of only one or the other

Place this code in the template of your choice to see the link library. 

Here are some examples of what you can do with these functions:
   
   Print out list of link categories:   
   <?php get_links_cats_anchor('name', 1, 50, 3, 1); ?>
   
   Print out full link list:   
   <?php get_links_anchor_notes('name', 1, 1, 1); ?>
   
   Print out link list from selected categories:
   <?php get_links_anchor_notes('name', 1, 1, 1, '7,2'); ?>
   
   Print out link list from all categories and show both link name and image:
   <?php get_links_anchor_notes('name', 1, 1, 1, '', 1); ?>

   
   

0.2 Changes and Notes:

      * Added multiple new options to configure the output of the link library
      * Updated documentation and readme (Thanks for Kathy Purdy for the help)

0.1 Changes and Notes:

	* Works with WordPress 1.5.
	* First release of the link-library plugin

------------------------------------------------------


