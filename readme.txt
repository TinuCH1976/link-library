=== Link Library ===
Contributors: jackdewey
Donate link: http://yannickcorner.nayanna.biz/wordpress-plugins/
Tags: link, list, page, library, AJAX, RSS, feeds, inline
Requires at least: 2.7
Tested up to: 2.8.3
Stable tag: trunk

The purpose of this plugin is to add the ability to output a list of link categories and a complete list of links with notes and descriptions.

== Description ==

This plugin is used to be able to create a page on your web site that will contain a list of all of the link categories that you have defined inside of the Links section of the Wordpress administration, along with all links defined in these categories. The user can select a sub-set of categories to be displayed or not displayed. Link Library also offers a mode where only one category is shown at a time, using AJAX queries to load other categories based on user input.

For links that carry RSS feed information, Link Library can display a preview of the latest feed items inline with the all links or in a separate preview window.

The latest version of this plugin has been completely re-vamped to use the filter plugin methods to add contents to the pages. It also contains a configuration page under the admin tools to be able to configure all outputs. This page allows for up to five different configurations to be created to display links on different pages of a Wordpress site. The previous API has been restored in version 1.1 for users who just prefer to make calls to PHP functions from their pages.

You can see a few examples of pages using Link Library on my personal site:<br/>
- [Library in table form](http://yannickcorner.nayanna.biz/freeware-database/)<br/>
- [Library in unordered list form with RSS feed icons](http://yannickcorner.nayanna.biz/favorite-links/)<br/>
- [Library only showing one category at a time through AJAX queries](http://yannickcorner.nayanna.biz/freeware-database-ajax-version/)<br/>
- [Library in unordered list form with 1 full RSS item per feed inline and 5 RSS item full previews when selecting preview icon](http://yannickcorner.nayanna.biz/links-page-with-preview/)<br/>

Examples from actual users can be found on my [site](http://yannickcorner.nayanna.biz/wordpress-plugins/).

All pages are generated using different configurations all managed by Link Library. Link Library is compatible with the [My Link Order](http://wordpress.org/extend/plugins/my-link-order/) plugin to define category and link ordering.

- [Changelog](http://wordpress.org/extend/plugins/link-library/other_notes/)
- [Support Forum](http://wordpress.org/tags/link-library)

== Installation ==

1. Download the plugin
1. Upload link-library.php to the /wp-content/plugins/ directory
1. Activate the plugin in the Wordpress Admin
1. Configure the desired output in the Link Library plugin configuration page.
1. In the Wordpress Admin, create a new page containing the following codes, where # should be replaced by the Settings Set number:<br/>
   [link-library-cats settings=#]<br/>
   [link-library settings = #]

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

== Changelog ==

= 2.3.2 =
* Fixed bug with RSS feeds which tried to load RSS feeds even if no feed address was specified

= 2.3.1 =
* Added configuration field to specify RSS library cache directory. Had been hard-coded by error in version 2.3

= 2.3 =
* Added messages in admin panel to show that data is saved or potential errors.
* Added new ability to show RSS feed previews in a preview box or inline with links.

= 2.2 =
* Added new mode that only shows one category at a time.
* New category can be selected through category list.
* Default first category can be selected by user.
* Uses AJAX to fetch data.

= 2.1 =
* Added ability to display edit links next to links in page for editors and administrators that are logged in.

= 2.0 =
* Added ability to define multiple groups of settings to use Link Library on different pages with different configurations.

= 1.3.2 =
* Added option to display link category descriptions, with option to embed HTML code in description.

= 1.3.1 =
* Optimized some of the code for category parsing and corrected a few syntax errors.
* Categories with special characters will also be handled better.

= 1.3 =
* Created a new stylesheet for Link Library styles. Added new reset settings link for a table layout.

= 1.2.5 =
* Adds new option to specify a target window for all links

= 1.2.4 =
* Corrects second issue related to exclusion list and category order set to included list

= 1.2.3=
* Corrects a bug between the exclusion list and setting category order to included list with no included list defined

= 1.2.2 =
* Correct a few issues with escape characters and the nofollow condition

= 1.2 =
* Added new options to output extra code before and after complete link groups after a user-defined number of links.
* Enables the display of links are data cells in a table row.

= 1.1.9 =
* Added new option to show RSS link using standard icon instead of only textual link.

= 1.1.8.1 =
* Fixed bug with Show Image and Name option. Now works as expected.

= 1.1.8 =
* Added new ordering option to follow order set by [My Link Order](http://wordpress.org/extend/plugins/my-link-order/) plugin

= 1.1.7 = 
* Added new options to give users flexibility to choose between div class tags and heading tags.
* Users upgrading need to select the desired output and set the value of the class name (e.g. linklistcatname) or desired heading style (e.g. h2).

= 1.1.6 =
* The H2 tags that were previously placed before and after the names of the link categories have been replaced by a div class called linklistcatname.
* This allows for more flexibility in formatting the element using your blog’s stylesheet.

== Frequently Asked Questions ==

= Can Link Library be used as before by calling PHP functions? =

For legacy users of Link Library (pre-1.0), it is still possible to call the back-end functions of the plugin from PHP code to display the contents of your library directly from a page template.

The main differences are that the function names have been changed to reflect the plugin name. However, the parameters are compatible with the previous function, with a few additions having been made. Also, it is important to note that the function does not output the Link Library content by themselves as they did. You now need to print the return value of these functions, which can be simply done with the echo command. Finally, it is possible to call these PHP functions with a single argument ('AdminSettings1', 'AdminSettings2', 'AdminSettings3', 'AdminSettings4' or 'AdminSettings5') so that the settings defined in the Admin section are used.

Here would be the installation procedure:

1. Download the plugin
1. Upload link-library.php to the /wp-content/plugins/ directory
1. Activate the plugin in the Wordpress Admin
1. Use the following functions in a [new template](http://codex.wordpress.org/Pages#Page_Templates) and select this template for your page that should display your Link Library.

`&lt;?php echo LinkLibraryCategories('name', 1, 100, 3, 1, 0, '', '', '', false, '', ''); ?&gt;<br />
`&lt;br /&gt;<br />
&lt;?php echo LinkLibrary('name', 1, 1, 1, 1, 0, 0, '', 0, 0, 1, 1, '&lt;td>', '&lt;/td&gt;', 1, '', '&lt;tr&gt;', '&lt;/tr&gt;', '&lt;td&gt;', '&lt;/td&gt;', 1, '&lt;td&gt;', '&lt;/td&gt;', 1, "Application", "Description", "Similar to", 1, '', '', '', false, 'linklistcatname', false, 0, null, null, null, false, false, false, false, '', ''); ?&gt;

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
   showcategorydescheaders (default null) - Show category descriptions in category list<br/>
   showonecatonly (default false) - Enable AJAX mode showing only one category at a time<br/>
   settings (default NULL) - Settings Set ID, only used when showonecatonly is true<br/>
   loadingicon (default NULL) - Path to icon to display when only show one category at a time<br/>

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
   catlistwrappers (default 1) - Number of different sets of alternating elements to be placed before and after each link category section<br/>
   beforecatlist1 (default null) - First element to be placed before a link category section<br/>
   beforecatlist2 (default null) - Second element to be placed before a link category section<br/>
   beforecatlist3 (default null) - Third element to be placed before a link category section<br/>
   divorheader (default false) - Output div before and after cat name if false, output heading tag if true<br/>
   catnameoutput (default linklistcatname) - Name of div class or heading to output<br/>   
   showrssicon (default false) - Output RSS URI if available and assign to standard RSS icon<br />
   linkaddfrequency (default 0) - Frequency at which extra before and after output should be placed around links<br />
   addbeforelink (default null) - Addition output to be placed before link<br />
   addafterlink (default null) - Addition output to be placed after link<br />
   linktarget (default null) - Specifies the link target window<br />
   showcategorydescheaders (default false) - Display link category description when printing category list<br />
   showcategorydesclinks (default false) - Display link category description when printing links<br />
   showadmineditlinks (default false) - Display edit links in output if logged in as administrator<br />
   showonecatonly (default false) - Only show one category at a time<br />
   AJAXcatid (default null) - Category ID for AJAX sub-queries<br />
   defaultsinglecat (default null) - ID of first category to be shown in single category mode<br />
   rsspreview (default false) - Add preview links after RSS feed addresses<br />
   rssfeedpreviewcount(default 3) - Number of RSS feed items to show in preview<br />
   rssfeedinline (default false) - Shows latest feed items inline with link list<br />
   rssfeedinlinecontent (default false) - Shows latest feed items contents inline with link list<br />
   rssfeedinlinecount (default 1) - Number of RSS feed items to show inline<br />
   beforerss (default null) - String to output before RSS block<br />
   afterrss (default null) - String to output after RSS block<br />


== Screenshots ==

1. The Settings Panel used to configure the output of Link Library
2. A sample output page, displaying a list of categories and the links for all categories.