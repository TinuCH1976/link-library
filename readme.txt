=== Plugin Name ===
Contributors: jackdewey
Tags: link, list
Requires at least: 2.7
Tested up to: 2.7
Stable tag: trunk

The purpose of this plugin is to add the ability to output a list of link categories and a complete list of links with notes and descriptions.

== Description ==

This plugin is used to be able to create a page on your web site that will contain a list of all of the link categories that you have defined inside of the Links section of the Wordpress administration, along with all links defined in these categories.

The latest version of this plugin has been completely re-vamped to use the filter plugin methods to add contents to the pages. It also contains a configuration page under the admin tools to be able to configure all outputs.

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

== Frequently Asked Questions ==

There are no FAQs at this time.

== Screenshots ==

1. The Settings Panel used to configure the output of Link Library
2. A sample output page, displaying a list of categories and the links for all categories.