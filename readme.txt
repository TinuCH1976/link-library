=== Link Library ===
Contributors: jackdewey
Donate link: http://yannickcorner.nayanna.biz/wordpress-plugins/
Tags: link, list, page
Requires at least: 2.7
Tested up to: 2.7
Stable tag: trunk

The purpose of this plugin is to add the ability to output a list of link categories and a complete list of links with notes and descriptions.

== Description ==

This plugin is used to be able to create a page on your web site that will contain a list of all of the link categories that you have defined inside of the Links section of the Wordpress administration, along with all links defined in these categories.

The latest version of this plugin has been completely re-vamped to use the filter plugin methods to add contents to the pages. It also contains a configuration page under the admin tools to be able to configure all outputs. The previous API has been restored in version 1.1 for users who just prefer to make calls to PHP functions from their pages.

You can see a demonstration of the output of the plugin [here](http://yannickcorner.nayanna.biz/freeware-database/).

== Installation ==

1. Download the plugin
1. Upload link-library.php to the /wp-content/plugins/ directory
1. Activate the plugin in the Wordpress Admin
1. Configure the desired output in the Link Library plugin configuration page.
1. In the Wordpress Admin, create a new page containing the following codes:<br/>
   [link-library-cats]<br/>
   [link-library]

To override the settings specified inside of the plugin settings page, the two commands can be called with options. Here is the syntax to call these options:

[link-library-cats categorylistoverride="28"]

Overrides the list of categories to be displayed in the category list

[link-library-cats excludecategoryoverride="28"]

Overrides the list of categories to be excluded in the category list

[link-library categorylistoverride="28"]

Overrides the list of categories to be displayed in the link list

[link-library excludecategoryoverride="28"]

Overrides the list of categories to be excluded in the link list

[link-library notesoverride=0]

Set to 0 or 1 to display or not display link notes

[link-library descoverride=0]

Set to 0 or 1 to display or not display link descriptions

[link-library rssoverride=0]

Set to 0 or 1 to display or not display rss information

[link-library tableoverride=0]

Set to 0 or 1 to display links in an unordered list or a table.

For legacy users, please see the Other Notes section for usage information.

== Other Notes ==

For legacy users of Link Library (pre-1.0), it is still possible to call the back-end functions of the plugin from PHP code to display the contents of your library directly from a page template.

The main differences are that the function names have been changed to reflect the plugin name. However, the parameters are compatible with the previous function, with a few additions having been made. Also, it is important to note that the function does not output the Link Library content by themselves as they did. You now need to print the return value of these functions, which can be simply done with the echo command. Finally, it is possible to call these PHP functions with a single argument ('AdminSettings') so that the settings used in the Admin section are used.

Here would be the installation procedure:

1. Download the plugin
1. Upload link-library.php to the /wp-content/plugins/ directory
1. Activate the plugin in the Wordpress Admin
1. Use the following functions in a [new template](http://codex.wordpress.org/Pages#Page_Templates) and select this template for your page that should display your Link Library.

`&lt;?php echo LinkLibraryCategories('name', 1, 100, 3, 1, 0, '', ''); ?&gt;<br />
`&lt;br /&gt;<br />
&lt;?php echo LinkLibrary('name', 1, 1, 1, 1, 0, 0, '', 0, 0, 1, 1, '&lt;td>', '&lt;/td&gt;', 1, '', '&lt;tr&gt;', '&lt;/tr&gt;', '&lt;td&gt;', '&lt;/td&gt;', 1, '&lt;td&gt;', '&lt;/td&gt;', 1, "Application", "Description", "Similar to"); ?&gt;

=function LinkLibraryCategories()=

 Output a list of all links categories, listed by category, using the settings in $wpdb->linkcategories and output it as table

 Parameters:<br/>
   order (default 'name')  - Sort link categories by 'name', 'id', 'catlist'. When set to 'AdminSettings', will use parameters set in Admin Settings Panel.<br/>
   hideifempty (default true)  - Supress listing empty link categories<br/>
   tablewitdh (default 100) - Width of table, percentage<br/>
   numcolumns (default 1) - Number of columns in table<br/>
   catanchor (default false) - Determines if links to generated anchors should be created<br/>
   flatlist (default false) - When set to true, displays an unordered list instead of a table<br/>
   categorylist (default null) - Specifies a comma-separate list of the only categories that should be displayed<br/>
   excludecategorylist (default null) - Specifies a comma-separate list of the categories that should not be displayed<br/>

=function LinkCategory()=

 Output a list of all links, listed by category, using the settings in $wpdb->linkcategories and output it as a nested HTML unordered list. Can also insert anchors for categories

 Parameters:<br/>
   order (default 'name')  - Sort link categories by 'name', 'id' or 'catlist'. When set to 'AdminSettings', will use parameters set in Admin Settings Panel.<br/>
   hideifempty (default true)  - Supress listing empty link categories<br/>
   catanchor (default false) - Adds name anchors to categorie links to be able to link directly to categories<br/>
   showdescription (default false) - Displays link descriptions. Added for 2.1 since link categories no longer have this setting<br/>
   shownotes (default false) - Shows notes in addition to description for links (useful since notes field is larger than description)<br/>
   showrating (default false) - Displays link ratings. Added for 2.1 since link categories no longer have this setting<br/>
   showupdated (default false) - Displays link updated date. Added for 2.1 since link categories no longer have this setting<br/>
   categorylist (default null) - Only show links inside of selected categories. Enter category numbers in a string separated by commas<br/>
   showimages (default false) - Displays link images. Added for 2.1 since link categories no longer have this setting<br/>
   showimageandname (default false) - Show both image and name instead of only one or the other<br/>
   usehtmltags (default false) - Use HTML tags for formatting instead of just displaying them<br/>
   showrss (default false) - Display RSS URI if available in link description<br/>
   beforenote (default &lt;br /&gt;) - Code to print out between the description and notes<br/>
   nofollow (default false) - Adds nofollow tag to outgoing links<br/>
   excludecategorylist (default null) - Specifies a comma-separate list of the categories that should not be displayed<br/>
   afternote (default null) - Code / Text to be displayed after note<br/>
   beforeitem (default null) - Code / Text to be displayed before item<br/>
   afteritem (default null) - Code / Text to be displayed after item<br/>
   beforedesc (default null) - Code / Text to be displayed before description<br/>
   afterdesc (default null) - Code / Text to be displayed after description<br/>
   displayastable (default false) - Display lists of links as a table (when true) or as an unordered list (when false)<br/>
   beforelink (default null) - Code / Text to be displayed before link<br/>
   afterlink (default null) - Code / Text to be displayed after link<br/>
   showcolumnheaders (default false) - Show column headers if rendering in table mode<br/>
   linkheader (default null) - Text to be shown in link column when displaying as table<br/>
   descheader (default null) - Text to be shown in desc column when displaying as table<br/>
   notesheader (default null) - Text to be shown in notes column when displaying as table<br/>

== Update Notes ==

Version 1.1.6: The H2 tags that were previously placed before and after the names of the link categories have been replaced by a div class called linklistcatname. This allows for more flexibility in formatting the element using your blog’s stylesheet.

== Screenshots ==

1. The Settings Panel used to configure the output of Link Library
2. A sample output page, displaying a list of categories and the links for all categories.