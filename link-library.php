<?php
/*
Plugin Name: Link Library
Plugin URI: http://wordpress.org/extend/plugins/link-library/
Description: Display links on pages with a variety of options
Version: 5.8.0.6
Author: Yannick Lefebvre
Author URI: http://yannickcorner.nayanna.biz/

A plugin for the blogging MySQL/PHP-based WordPress.
Copyright 2013 Yannick Lefebvre

Translations:
French Translation courtesy of Luc Capronnier
Danish Translation courtesy of GeorgWP (http://wordpress.blogos.dk)
Italian Translation courtesy of Gianni Diurno

This program is free software; you can redistribute it and/or
modify it under the terms of the GNUs General Public License
as published addlinkcatlistoverrideby the Free Software Foundation; either version 2
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

require_once(ABSPATH . '/wp-admin/includes/bookmark.php');

global $my_link_library_plugin;
global $my_link_library_plugin_admin;

if ( !get_option( 'link_manager_enabled' ) )
    add_filter( 'pre_option_link_manager_enabled', '__return_true' );

if ( is_admin() ) {
    global $my_link_library_plugin_admin;
    require plugin_dir_path( __FILE__ ) . 'link-library-admin.php';
    $my_link_library_plugin_admin = new link_library_plugin_admin();
}

/*********************************** Link Library Class *****************************************************************************/
class link_library_plugin {

	//constructor of class, PHP4 compatible construction for backward compatibility
	function link_library_plugin() {

        // Functions to be called when plugin is activated and deactivated
        register_activation_hook( __FILE__, array($this, 'll_install' ) );
        register_deactivation_hook( __FILE__, array($this, 'll_uninstall' ) );
	
		$newoptions = get_option('LinkLibraryPP1', "");

		if ($newoptions == "")
		{
            global $my_link_library_plugin_admin;
			$my_link_library_plugin_admin->ll_reset_options(1, 'list');
			$my_link_library_plugin_admin->ll_reset_gen_settings();
		}
        
		// wp_ajax_... is only run for logged usrs
		//add_action( 'wp_ajax_scn_check_url_action', array( &$this, 'ajax_action_check_url' ) );

		// Add short codes
		add_shortcode('link-library-cats', array($this, 'link_library_cats_func'));
		add_shortcode('link-library-search', array($this, 'link_library_search_func'));
		add_shortcode('link-library-addlink', array($this, 'link_library_addlink_func'));
		add_shortcode('link-library-addlinkcustommsg', array($this, 'link_library_addlink_func'));
		add_shortcode('link-library', array($this, 'link_library_func'));

		// Function to print information in page header when plugin present
		add_action('wp_head', array($this, 'll_rss_link'));

		// Function to determine if Link Library is used on a page before printing headers
		add_filter('the_posts', array($this, 'conditionally_add_scripts_and_styles')); 
        // the_posts gets triggered before wp_head

		add_filter('wp_title', array($this, 'll_title_creator'));

		// Re-write rules filters to allow for custom permalinks
		add_filter('rewrite_rules_array', array($this, 'll_insertMyRewriteRules'));
		add_filter('query_vars', array($this, 'll_insertMyRewriteQueryVars'));
        
		// Load text domain for translation of admin pages and text strings
		load_plugin_textdomain( 'link-library', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

    /************************** Link Library Installation Function **************************/
    function ll_install() {
        global $wpdb;

        if (function_exists('is_multisite') && is_multisite()) {
            if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1))
            {
                $originalblog = $wpdb->blogid;

                $bloglist = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
                foreach ($bloglist as $blog) {
                    switch_to_blog($blog);
                    $this->create_table_and_settings();
                }
                switch_to_blog($originalblog);
                return;
            }
        }
        $this->create_table_and_settings();
    }

    function new_network_site($blog_id, $user_id, $domain, $path, $site_id, $meta )
    {
        global $wpdb;

        if ( ! function_exists('is_plugin_active_for_network') )
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        if (is_plugin_active_for_network('link-library/link-library.php')) {
            $originalblog = $wpdb->blogid;
            switch_to_blog($blog_id);
            $this->create_table_and_settings();
            switch_to_blog($originalblog);
        }
    }

    function create_table_and_settings()
    {
        global $wpdb;

        $wpdb->links_extrainfo = $this->db_prefix().'links_extrainfo';

        $creationquery = "CREATE TABLE " . $wpdb->links_extrainfo . " (
				link_id bigint(20) NOT NULL DEFAULT '0',
				link_second_url varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				link_telephone varchar(128) CHARACTER SET utf8 DEFAULT NULL,
				link_email varchar(128) CHARACTER SET utf8 DEFAULT NULL,
				link_visits bigint(20) DEFAULT '0',
				link_reciprocal varchar(255) DEFAULT NULL,
				link_submitter varchar(255) DEFAULT NULL,
				link_submitter_name VARCHAR( 128 ) NULL,
				link_submitter_email VARCHAR( 128 ) NULL,
				link_textfield TEXT NULL,
				link_no_follow VARCHAR(1) NULL,
				link_featured VARCHAR(1) NULL,
				link_manual_updated VARCHAR(1) NULL,
				UNIQUE KEY  link_id (link_id)
				);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($creationquery);

        $genoptions = get_option('LinkLibraryGeneral');

        if ($genoptions != '')
        {
            if ($genoptions['schemaversion'] == '' || floatval($genoptions['schemaversion']) < 3.5)
            {
                $genoptions['schemaversion'] = "3.5";
                update_option('LinkLibraryGeneral', $genoptions);
            }
            elseif (floatval($genoptions['schemaversion']) < "4.6")
            {
                $genoptions['schemaversion'] = "4.6";
                $wpdb->get_results("ALTER TABLE `" . $this->db_prefix() . "links_extrainfo` ADD `link_submitter_name` VARCHAR( 128 ) NULL, ADD `link_submitter_email` VARCHAR( 128 ) NULL , ADD `link_textfield` TEXT NULL ;");

                update_option('LinkLibraryGeneral', $genoptions);
            }
            elseif (floatval($genoptions['schemaversion']) < "4.7")
            {
                $genoptions['schemaversion'] = "4.7";
                $wpdb->get_results("ALTER TABLE `" . $this->db_prefix() . "links_extrainfo` ADD `link_no_follow` VARCHAR( 1 ) NULL;");

                update_option('LinkLibraryGeneral', $genoptions);
            }
            elseif (floatval($genoptions['schemaversion']) < "4.9")
            {
                $genoptions['schemaversion'] = "4.9";
                $wpdb->get_results("ALTER TABLE `" . $this->db_prefix() . "links_extrainfo` ADD `link_featured` VARCHAR( 1 ) NULL;");

                update_option('LinkLibraryGeneral', $genoptions);
            }

            for ($i = 1; $i <= $genoptions['numberstylesets']; $i++) {
                $settingsname = 'LinkLibraryPP' . $i;
                $options = get_option($settingsname);

                if ($options != '')
                {
                    if ($options['showname'] == '')
                        $options['showname'] = true;

                    if ( isset($options['show_image_and_name'] ) && $options['show_image_and_name'] == true)
                    {
                        $options['showname'] = true;
                        $options['show_images'] = true;
                    }

                    if ($options['sourcename'] == '')
                        $options['sourcename'] = 'primary';

                    if ($options['sourceimage'] == '')
                        $options['sourceimage'] = 'primary';

                    if ($options['dragndroporder'] == '')
                    {
                        if ($options['imagepos'] == 'beforename')
                            $options['dragndroporder'] = '1,2,3,4,5,6,7,8,9,10,11,12';
                        elseif ($options['imagepos'] == 'aftername')
                            $options['dragndroporder'] = '2,1,3,4,5,6,7,8,9,10,11,12';
                        elseif ($options['imagepos'] == 'afterrssicons')
                            $options['dragndroporder'] = '2,3,4,5,6,1,7,8,9,10,11,12';
                    }
                    else if ($options['dragndroporder'] != '')
                    {
                        $elementarray = explode(',', $options['dragndroporder']);

                        $allelements = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');
                        foreach ($allelements as $element)
                        {
                            if (!in_array($element, $elementarray))
                            {
                                $elementarray[] = $element;
                                $options['dragndroporder'] = implode(",", $elementarray);
                            }
                        }
                    }

                    if ($options['flatlist'] === true) $options['flatlist'] = 'unordered';
                    elseif ($options['flatlist'] === false) $options['flatlist'] = 'table';
                }

                update_option($settingsname, $options);
            }
        }
    }

    function remove_querystring_var($url, $key) {

        $keypos = strpos($url, $key);
        if ($keypos)
        {
            $ampersandpos = strpos($url, '&', $keypos);
            $newurl = substr($url, 0, $keypos - 1);

            if ($ampersandpos)
                $newurl .= substr($url, $ampersandpos);
        }
        else
            $newurl = $url;

        return $newurl;
    }

    /************************** Link Library Uninstall Function **************************/
    function ll_uninstall() {
        $genoptions = get_option('LinkLibraryGeneral');

        if ($genoptions != '')
        {
            if ( isset( $genoptions['stylesheet'] ) && isset( $genoptions['fullstylesheet'] ) && $genoptions['stylesheet'] != '' && $genoptions['fullstylesheet'] == '')
            {
                $stylesheetlocation = plugins_url( $genoptions['stylesheet'], __FILE__ );
                if ( file_exists( $stylesheetlocation ) )
                    $genoptions['fullstylesheet'] = file_get_contents( $stylesheetlocation );

                update_option('LinkLibraryGeneral', $genoptions);
            }
        }
    }
    
    function db_prefix() {
		global $wpdb;
		if (method_exists($wpdb, "get_blog_prefix"))
			return $wpdb->get_blog_prefix();
		else
			return $wpdb->prefix;
	}
    
    	/******************************************** Print style data to header *********************************************/

	function ll_rss_link() {
		global $llstylesheet, $rss_settings;
		
		if ($rss_settings != "")
		{
			$settingsname = 'LinkLibraryPP' . $rss_settings;
			$options = get_option($settingsname);

			$feedtitle = ($options['rssfeedtitle'] == "" ? __('Link Library Generated Feed', 'link-library') : $options['rssfeedtitle']);

			$xpath = $this->relativePath( dirname( __FILE__ ), ABSPATH );
			echo '<link rel="alternate" type="application/rss+xml" title="' . esc_html(stripslashes($feedtitle)) . '" href="' . plugins_url('rssfeed.php?settingset=' . $rss_settings/* . '&xpath=' . $xpath*/, __FILE__) . '" />';
			unset( $xpath );
		}

		if ($llstylesheet == true)
		{
			$genoptions = get_option('LinkLibraryGeneral');
			
			echo "<style id='LinkLibraryStyle' type='text/css'>\n";
			echo stripslashes($genoptions['fullstylesheet']);
			echo "</style>\n";
		}
	}

	/****************************************** Add Link Category name to page title when option is present ********************************/
	function ll_title_creator($title) {
		global $wp_query;
		global $wpdb;
                global $llstylesheet;
                
                if ($llstylesheet)
                {
                    $genoptions = get_option('LinkLibraryGeneral');

                    $categoryname = ( isset( $wp_query->query_vars['cat_name'] ) ? $wp_query->query_vars['cat_name'] : '' );
                    $catid = ( isset( $_GET['cat_id'] ) ? intval($_GET['cat_id']) : '' );

                    $linkcatquery = "SELECT t.name ";
                    $linkcatquery .= "FROM " . $this->db_prefix() . "terms t LEFT JOIN " . $this->db_prefix(). "term_taxonomy tt ON (t.term_id = tt.term_id) ";
                    $linkcatquery .= "LEFT JOIN " . $this->db_prefix() . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
                    $linkcatquery .= "WHERE tt.taxonomy = 'link_category' AND ";

                    if ($categoryname != '')
                    {
                            $linkcatquery .= "t.slug = '" . $categoryname . "'";
                            $nicecatname = $wpdb->get_var($linkcatquery);
                            return $title . $genoptions['pagetitleprefix'] . $nicecatname . $genoptions['pagetitlesuffix'];
                    }
                    elseif ($catid != '')
                    {
                            $linkcatquery .= "t.term_id = '" . $catid . "'";
                            //echo $linkcatquery;
                            $nicecatname = $wpdb->get_var($linkcatquery);
                            return $title . $genoptions['pagetitleprefix'] . $nicecatname . $genoptions['pagetitlesuffix'];
                    }
                }

		return $title;
	}
    
    	/************************************* Function to add to rewrite rules for permalink support **********************************/
	function ll_insertMyRewriteRules($rules)
	{
		$newrules = array();

		$genoptions = get_option('LinkLibraryGeneral');

		if ($genoptions != '')
		{
			for ($i = 1; $i <= $genoptions['numberstylesets']; $i++) {
				$settingsname = 'LinkLibraryPP' . $i;
				$options = get_option($settingsname);
				
				if ($options['enablerewrite'] == true && $options['rewritepage'] != '')
					$newrules['(' . $options['rewritepage'] . ')/(.+?)$'] = 'index.php?pagename=$matches[1]&cat_name=$matches[2]';
					
				if ($options['publishrssfeed'] == true)
				{
					$xpath = $this->relativePath( dirname( __FILE__ ), ABSPATH );

					if ($options['rssfeedaddress'] != '')
						$newrules['(' . $options['rssfeedaddress'] . ')/(.+?)$'] = plugins_url( 'rssfeed.php?settingset=$matches[1]' . '&xpath=' . $xpath, __FILE__ );
					elseif ($options['rssfeedaddress'] == '')
						$newrules['(linkrss)/(.+?)$'] = plugins_url( 'rssfeed.php?settingset=$matches[1]' . '&xpath=' . $xpath, __FILE__ );

					unset( $xpath );
				}
			}
		}
		
		return $newrules + $rules;
	}

	// Adding the id var so that WP recognizes it
	function ll_insertMyRewriteQueryVars($vars)
	{
		array_push($vars, 'cat_name');
		return $vars;
	}
	
	/*********************************************** Private Link Library Categories Function *************************************/

	function PrivateLinkLibraryCategories($order = 'name', $hide_if_empty = true, $table_width = 100, $num_columns = 1, $catanchor = true, 
				   $flatlist = 'table', $categorylist = '', $excludecategorylist = '', $showcategorydescheaders = false, 
				   $showonecatonly = false, $settings = '', $loadingicon = '/icons/Ajax-loader.gif', $catlistdescpos = 'right',
				   $debugmode = false, $pagination = false, $linksperpage = 5, $showcatlinkcount = false, $showonecatmode = 'AJAX',
				   $cattargetaddress = '', $rewritepage = '', $showinvisible = false, $showuserlinks = false, $showcatonsearchresults = false) {

		global $wpdb;

		$output = '';
                
                $categoryid = '';
		
		if (isset($_GET['cat_id']))
			$categoryid = intval($_GET['cat_id']);

		if (!isset($_GET['searchll']) || $showcatonsearchresults == true)
		{
			$countcat = 0;

			$order = strtolower($order);

			$output .= "<!-- Link Library Categories Output -->\n\n";

			if ($showonecatonly == true && ($showonecatmode == 'AJAX' || $showonecatmode == ''))
			{
				$xpath = $this->relativePath( dirname( __FILE__ ), ABSPATH );

				$output .= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
				$output .= "var ajaxobject;\n";
				$output .= "function showLinkCat ( _incomingID, _settingsID, _pagenumber) {\n";
				$output .= "if (typeof(ajaxobject) != \"undefined\") { ajaxobject.abort(); }\n";
				$output .= "var map = {id : _incomingID, settings : _settingsID, linkresultpage: _pagenumber, xpath: '" . $xpath . "'}\n";
				$output .= "\tjQuery('#contentLoading').toggle();jQuery.get('" . plugins_url( 'link-library-ajax.php', __FILE__ ) . "', map, function(data){jQuery('#linklist" . $settings. "').replaceWith(data);jQuery('#contentLoading').toggle();});\n";
				$output .= "}\n";

				$output .= "</SCRIPT>\n\n";
				
				unset( $xpath );
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
			$linkcatquery .= "FROM " . $this->db_prefix() . "terms t LEFT JOIN " . $this->db_prefix(). "term_taxonomy tt ON (t.term_id = tt.term_id)";
			$linkcatquery .= " LEFT JOIN " . $this->db_prefix() . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";

			$linkcatquery .= " LEFT OUTER JOIN " . $this->db_prefix() . "links l on (tr.object_id = l.link_id";
			
			if ($showinvisible == false)
				$linkcatquery .= " AND l.link_visible != 'N'";
			
			if (!$showuserlinks)
				$linkcatquery .= " AND l.link_description not like '%LinkLibrary:AwaitingModeration:RemoveTextToApprove%' ";
				
			$linkcatquery .= " ) ";

			$linkcatquery .= "WHERE tt.taxonomy = 'link_category'";

			if ($categorylist != "")
				$linkcatquery .= " AND t.term_id in (" . $categorylist. ")";

			if ($excludecategorylist != "")
				$linkcatquery .= " AND t.term_id not in (" . $excludecategorylist . ")";
			
			if ($hide_if_empty == true)
				$linkcatquery .= " AND l.link_name != '' ";

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

				if ($flatlist == 'table')
					$output .= "<table width=\"" . $table_width . "%\">\n";
				elseif ($flatlist == 'unordered')
					$output .= "<ul class='menu'>\n";
				elseif ($flatlist == 'dropdown')
					$output .= "<form name='catselect'><select name='catdropdown' class='catdropdown'>";

				$linkcount = 0;

				foreach ( (array) $catnames as $catname) {
					// Handle each category.
					// First, fix the sort_order info
					//$orderby = $cat['sort_order'];
					//$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;
					
					$catfront = '';
					$cattext = '';
					$catitem = '';

					// Display the category name
					$countcat += 1;
					if ($flatlist == 'table' and (($countcat % $num_columns == 1) or ($num_columns == 1) )) $output .= "<tr>\n";

					if ($flatlist == 'table')
						$catfront = '	<td>';
					elseif ($flatlist == 'unordered')
						$catfront = '	<li>';
					elseif ($flatlist == 'dropdown')
					{
						$catfront = '	<option ';
						if ($categoryid != '' && $categoryid == $catname->term_id)
							$catfront .= 'selected="selected" ';
						$catfront .= 'value="';
					}

					if ($showonecatonly)
					{
						if ($showonecatmode == 'AJAX' || $showonecatmode == '')
						{
							if ($flatlist != 'dropdown')
								$cattext = "<a href='#' onClick=\"showLinkCat('" . $catname->term_id. "', '" . $settings . "', 1);return false;\" >";
							elseif ($flatlist == 'dropdown')
								$cattext = $catname->term_id;
						}
						elseif ($showonecatmode == 'HTMLGET')
						{
							if ($flatlist != 'dropdown')
								$cattext = "<a href='";

							if ($cattargetaddress != '' && strpos($cattargetaddress, "?") != false)
							{
								$cattext .= $cattargetaddress;
								$cattext .= "&cat_id=";
							}
							elseif ($cattargetaddress != '' && strpos($cattargetaddress, "?") == false)
							{
								$cattext .= $cattargetaddress;
								$cattext .= "?cat_id=";
							}
							elseif ($cattargetaddress == '')
								$cattext .= "?cat_id=";

							$cattext .= $catname->term_id;
							
							if ($flatlist != 'dropdown')
								$cattext .= "'>";
						}
						elseif ($showonecatmode == 'HTMLGETPERM')
						{
							if ($flatlist != 'dropdown')
								$cattext = "<a href='";
								
							$cattext .= "/" . $rewritepage . "/" . $catname->category_nicename;
							
							if ($flatlist != 'dropdown')
								$cattext .= "'>";
						}
					}
					else if ($catanchor)
					{
						if (!$pagination)
						{
							if ($flatlist != 'dropdown')
								$cattext = '<a href="';
							
							$cattext .= '#' . $catname->category_nicename;
							
							if ($flatlist != 'dropdown')
								$cattext .= '">';
						}
						elseif ($pagination)
						{
							if ($linksperpage == 0 && $linksperpage == '')
								$linksperpage = 5;

							$pageposition = ( $linkcount + 1 ) / $linksperpage;
							$ceilpageposition = ceil($pageposition);
							if ( $ceilpageposition == 0 && !isset( $_GET['linkresultpage'] ) ) {
								if ($flatlist != 'dropdown')
									$cattext = '<a href="';
								
								$cattext .= get_permalink() . '#' . $catname->category_nicename;
								
								if ( $flatlist != 'dropdown' )
									$cattext .= '">';
							} else {
								if ( $flatlist != 'dropdown' )
									$cattext = '<a href="';
									
								$cattext .= '?linkresultpage=' . ($ceilpageposition == 0 ? 1 : $ceilpageposition) . '#' . $catname->category_nicename;
									
								if ( $flatlist != 'dropdown' )
									$cattext .= '">';
							}

							$linkcount = $linkcount + $catname->linkcount;
						}
					}
					else
						$cattext = '';
					
					if ( $flatlist == 'dropdown' )
						$cattext .= '">';

					if ( $catlistdescpos == 'right' || $catlistdescpos == '' ) {
						$catitem .= '<div class="linkcatname">' . $catname->name . '</div>';
						if ($showcatlinkcount)
							$catitem .= " (" . $catname->linkcount . ")";
					}

					if ( $showcategorydescheaders ) {
						$catname->category_description = esc_html($catname->category_description);
						$catname->category_description = str_replace("[", "<", $catname->category_description);
						$catname->category_description = str_replace("]", ">", $catname->category_description);
						$catname->category_description = str_replace("&quot;", "\"", $catname->category_description);
						$catitem .= "<span class='linkcatdesc'>" . $catname->category_description . "</span>";
					}

					if ($catlistdescpos == 'left')
					{
						$catitem .= '<div class="linkcatname">' . $catname->name . '</div>';
						if ($showcatlinkcount)
							$catitem .= " (" . $catname->linkcount . ")";
					}

					if (($catanchor || $showonecatonly) && $flatlist != 'dropdown')
						$catitem .= "</a>";

					$output .= ($catfront . $cattext . $catitem );

					if ($flatlist == 'table')
						$catterminator = "	</td>\n";
					elseif ($flatlist == 'unordered')
						$catterminator = "	</li>\n";
					elseif ($flatlist == 'dropdown')
						$catterminator = "	</option>\n";

					$output .= ($catterminator);

					if ($flatlist == "table" and ($countcat % $num_columns == 0)) $output .= "</tr>\n";
				}

				if ($flatlist == "table" and ($countcat % $num_columns == 3)) $output .= "</tr>\n";
				if ($flatlist == "table" && $catnames)
					$output .= "</table>\n";
				elseif ($flatlist == 'unordered' && $catnames)
					$output .= "</ul>\n";
				elseif ($flatlist == 'dropdown' && $catnames)
				{
					$output .= "</select>\n";
					$output .= "<button type='button' onclick='showcategory()'>" . __('Go!', 'link-library') . "</button>";
					$output .= "</form>";
				}
					
				$output .= "</div>\n";
				
				if ($showonecatonly && ($showonecatmode == 'AJAX' || $showonecatmode == ''))
				{
					if ($loadingicon == '') $loadingicon = '/icons/Ajax-loader.gif';
					$output .= "<div class='contentLoading' id='contentLoading' style='display: none;'><img src='" . plugins_url( $loadingicon, __FILE__ ) . "' alt='Loading data, please wait...'></div>\n";
				}
				
				if ($flatlist == 'dropdown')
				{
					$output .= "<SCRIPT TYPE='text/javascript'>\n";
					$output .= "\tfunction showcategory(){\n";
					
					if ($showonecatonly && ($showonecatmode == 'AJAX' || $showonecatmode == '') )
					{
						$output .= "catidvar = document.catselect.catdropdown.options[document.catselect.catdropdown.selectedIndex].value;";
						$output .= "showLinkCat(catidvar, '" . $settings . "', 1);return false; }";
					}
					else
					{
						$output .= "\t\tlocation=\n";
						$output .= "document.catselect.catdropdown.options[document.catselect.catdropdown.selectedIndex].value }\n";
					
					}
					$output .= "</SCRIPT>\n";
				}
			}
			else
			{
				$output .= "<div>" . __('No categories found', 'link-library') . ".</div>";
			}

			$output .= "\n<!-- End of Link Library Categories Output -->\n\n";
		}
		return $output;
	}

	function ll_highlight_phrase($str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>')
	{
		if ($str == '')
		{
			return '';
		}

		if ($phrase != '')
		{
			return preg_replace('/('.preg_quote($phrase, '/').'(?![^<]*>))/i', $tag_open."\\1".$tag_close, $str);
		}

		return $str;
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
									$showonecatmode = 'AJAX', $dragndroporder = '1,2,3,4,5,6,7,8,9,10', $showname = true, $displayweblink = 'false',
									$sourceweblink = 'primary', $showtelephone = 'false', $sourcetelephone = 'primary', $showemail = 'false', $showlinkhits = false,
									$beforeweblink = '', $afterweblink = '', $weblinklabel = '', $beforetelephone = '', $aftertelephone = '', $telephonelabel = '',
									$beforeemail = '', $afteremail = '', $emaillabel = '', $beforelinkhits = '', $afterlinkhits = '', $emailcommand = '',
									$sourceimage = '', $sourcename = '', $thumbshotscid = '', $maxlinks = '', $beforelinkrating = '', $afterlinkrating = '',
									$showlargedescription = false, $beforelargedescription = '', $afterlargedescription = '', $featuredfirst = false, $shownameifnoimage = false,
                                    $enablelinkpopup = false, $popupwidth = 300, $popupheight = 400, $nocatonstartup = false, $linktitlecontent = 'linkname' ) {

		global $wpdb;
		
		$output = "\n<!-- Beginning of Link Library Output -->\n\n";

		$currentcategory = 1;
        $categoryname = "";
        
        if ( $showonecatonly && $showonecatmode == 'AJAX' && $AJAXcatid == '' ) {
            $AJAXnocatset = true;
        } else {
            $AJAXnocatset = false;
        }

		if ($showonecatonly && $showonecatmode == 'AJAX' && $AJAXcatid != '' && $_GET['searchll'] == "")
		{
			$categorylist = $AJAXcatid;
		}
		elseif ($showonecatonly && $showonecatmode == 'HTMLGET' && isset($_GET['cat_id']) && $_GET['searchll'] == "")
		{
			$categorylist = intval($_GET['cat_id']);
			$AJAXcatid = $categorylist;
		}
		elseif ($showonecatonly && $showonecatmode == 'HTMLGETPERM' && $_GET['searchll'] == "")
		{
			global $wp_query;

			$categoryname = $wp_query->query_vars['cat_name'];
			$AJAXcatid = $categoryname;
		}
		elseif ($showonecatonly && $AJAXcatid == '' && $defaultsinglecat != '' && $_GET['searchll'] == "")
		{
			$categorylist = $defaultsinglecat;
			$AJAXcatid = $categorylist;
		}
		elseif ($showonecatonly && $AJAXcatid == '' && $defaultsinglecat == '' && $_GET['searchll'] == "")
		{
			$catquery = "SELECT distinct t.name, t.term_id ";
			$catquery .= "FROM " . $this->db_prefix() . "terms t ";
			$catquery .= "LEFT JOIN " . $this->db_prefix() . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
			$catquery .= "LEFT JOIN " . $this->db_prefix() . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
			$catquery .= "LEFT JOIN " . $this->db_prefix() . "links l ON (tr.object_id = l.link_id) ";
            $catquery .= "LEFT JOIN " . $this->db_prefix() . "links_extrainfo le ON (l.link_id = le.link_id) ";	
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

			$catquery .= " ORDER by ";
			
			if ($featuredfirst == true)
				$catquery .= "le.link_featured DESC, ";

			if ($order == "name")
				$catquery .= " name " . $direction;
			elseif ($order == "id")
				$catquery .= " t.term_id " . $direction;
			elseif ($order == "order")
				$catquery .= " t.term_order " . $direction;
			elseif ($order == "catlist")
				$catquery .= " FIELD(t.term_id," . $categorylist . ") ";

			if ($linkorder == "name")
				$catquery .= ", link_name " . $linkdirection;
			elseif ($linkorder == "id")
				$catquery .= ", link_id " . $linkdirection;
			elseif ($linkorder == "order")
				$catquery .= ", link_order ". $linkdirection;
			elseif ($linkorder == "date")
				$catquery .= ", link_updated ". $linkdirection;
				
			$catitems = $wpdb->get_results($catquery);

			if ($debugmode)
			{
				$output .= "\n<!-- AJAX Default Category Query: " . print_r($catquery, TRUE) . "-->\n\n";
				$output .= "\n<!-- AJAX Default Category Results: " . print_r($catitems, TRUE) . "-->\n\n";
			}

			if ($catitems)
			{
				$categorylist = $catitems[0]->term_id;
				$AJAXcatid = $categorylist;
			}
		}
		
		$linkquery = "SELECT distinct *, l.link_id as proper_link_id, UNIX_TIMESTAMP(l.link_updated) as link_date, ";
		$linkquery .= "IF (DATE_ADD(l.link_updated, INTERVAL " . get_option('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated ";
		$linkquery .= "FROM " . $this->db_prefix() . "terms t ";
		$linkquery .= "LEFT JOIN " . $this->db_prefix() . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
		$linkquery .= "LEFT JOIN " . $this->db_prefix() . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
		$linkquery .= "LEFT JOIN " . $this->db_prefix() . "links l ON (tr.object_id = l.link_id) ";
		$linkquery .= "LEFT JOIN " . $this->db_prefix() . "links_extrainfo le ON (l.link_id = le.link_id) ";	
		$linkquery .= "WHERE tt.taxonomy = 'link_category' ";

		if ($hide_if_empty)
			$linkquery .= "AND l.link_id is not NULL AND l.link_description not like '%LinkLibrary:AwaitingModeration:RemoveTextToApprove%' ";

		if ($categorylist != "" || isset($_GET['cat_id']))
			$linkquery .= " AND t.term_id in (" . $categorylist. ")";
		
		if ( isset($categoryname) && $categoryname != "" && $showonecatmode == 'HTMLGETPERM')
			$linkquery .= " AND t.slug = '" . $categoryname. "'";

		if ($excludecategorylist != "")
			$linkquery .= " AND t.term_id not in (" . $excludecategorylist . ")";

		if ($showinvisible == false)
			$linkquery .= " AND l.link_visible != 'N'";	

		if (isset($_GET['searchll']) && $_GET['searchll'] != "")
		{
			$searchterms = explode(" ", $_GET['searchll']);

			if ($searchterms)
			{
				$mode = "search";
				$termnb = 1;

				foreach($searchterms as $searchterm)
				{
                    $searchterm = str_replace( '--', '', $searchterm );
                    $searchterm = str_replace( ';', '', $searchterm );
					if ( $searchterm  == true )
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
						if ($showlargedescription)
							$linkquery .= " OR link_textfield like '%" . $searchterm . "%' ";
					}
				}

				$linkquery .= ")";
			}
		}
		else
			$mode = "normal";
			
		$linkquery .= " ORDER by ";
			
		if ($featuredfirst == true)
			$linkquery .= "link_featured DESC, ";

		if ($order == "name")
			$linkquery .= " name " . $direction;
		elseif ($order == "id")
			$linkquery .= " t.term_id " . $direction;
		elseif ($order == "order")
			$linkquery .= " t.term_order " . $direction;
		elseif ($order == "catlist")
			$linkquery .= " FIELD(t.term_id," . $categorylist . ") ";

		if ($linkorder == "name" || $linkorder == 'random')
			$linkquery .= ", l.link_name " . $linkdirection;
		elseif ($linkorder == "id")
			$linkquery .= ", l.link_id " . $linkdirection;
		elseif ($linkorder == "order")
			$linkquery .= ", l.link_order ". $linkdirection;
		elseif ($linkorder == "date")
			$linkquery .= ", l.link_updated ". $linkdirection;

		if ($pagination && $mode != 'search')
		{
			$linkitemsforcount = $wpdb->get_results($linkquery);

			$numberoflinks = count($linkitemsforcount);

			$quantity = $linksperpage + 1;

			if (isset($_GET['linkresultpage']))
			{
				$pagenumber = $_GET['linkresultpage'];
				$startingitem = ($pagenumber - 1) * $linksperpage;
				$linkquery .= " LIMIT " . $startingitem . ", " . $quantity;
			}
			else
			{
				$pagenumber = 1;
				$linkquery .= " LIMIT 0, " . $quantity;
			}
		}
		
		$linkitems = $wpdb->get_results($linkquery, ARRAY_A);
		
		if ($debugmode)
		{
			$output .= "\n<!-- Link Query: " . print_r($linkquery, TRUE) . "-->\n\n";
			$output .= "\n<!-- Link Results: " . print_r($linkitems, TRUE) . "-->\n\n";
		}

		if ($pagination)
		{
			if ($linksperpage == 0 && $linksperpage == '')
				$linksperpage = 5;

			if (count($linkitems) > $linksperpage)
			{
				array_pop($linkitems);
				$nextpage = true;
			}
			else
				$nextpage = false;

			if( isset( $numberoflinks ) ) {
				$preroundpages = $numberoflinks / $linksperpage;
				$numberofpages = ceil( $preroundpages * 1 ) / 1;
			}
		}
		
		if ($linkorder == 'random')
		{
			shuffle($linkitems);
		}
		
		if ( $maxlinks != '' ) {
			if ( is_numeric( $maxlinks ) ) {
				array_splice( $linkitems, $maxlinks );
			}
		}
        
        echo "<!-- showonecatmode: " . $showonecatonly . ", AJAXnocatset: " . $AJAXnocatset . ", nocatonstartup: " . $nocatonstartup . "-->";

		// Display links
        if ( $linkitems && $showonecatonly && $AJAXnocatset && $nocatonstartup ) {
                $output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
                $output .= '</div>';
        } elseif ( $linkitems ) {
			$output .= "<div id='linklist" . $settings . "' class='linklist'>\n";

			if ( $mode == 'search' ) {
				$output .= "<div class='resulttitle'>" . __('Search Results for', 'link-library') . " '" . $_GET['searchll'] . "'</div>";
			}

			$currentcategoryid = -1;

            $xpath = $this->relativePath( dirname( __FILE__ ), ABSPATH );

			foreach ( $linkitems as $linkitem ) {
				
				if ($currentcategoryid != $linkitem['term_id'])
				{
					if ($currentcategoryid != -1 && $showonecatonly && $_GET['searchll'] == "")
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
							
						$output .= "</div>";

						$currentcategory = $currentcategory + 1;
					}

					$currentcategoryid = $linkitem['term_id'];
					$output .= "<div class='LinkLibraryCat" . $currentcategoryid . "'>";
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
							$cattext = '<div id="' . $linkitem['slug'] . '">';
						else
							$cattext = '';

						if ($divorheader == false)
						{
							if ($mode == "search")
								foreach ($searchterms as $searchterm)
								{
									$linkitem['name'] = $this->ll_highlight_phrase($linkitem['name'], $searchterm, '<span class="highlight_word">', '</span>'); 
								}

							$catlink = '<div class="' . $catnameoutput . '">';

							if ($catdescpos == "right" || $catdescpos == '')
								$catlink .= $linkitem['name'];

							if ($showcategorydesclinks)
							{
								$catlink .= "<span class='linklistcatnamedesc'>";
								$linkitem['description'] = str_replace("[", "<", $linkitem['description']);
								$linkitem['description'] = str_replace("]", ">", $linkitem['description']);
								$catlink .= $linkitem['description'];
								$catlink .= '</span>';
							}

							if ($catdescpos == "left")
								$catlink .= $linkitem['name'];

							$catlink .= "</div>";
						}
						else if ($divorheader == true)
						{
							if ($mode == "search")
							foreach ($searchterms as $searchterm)
							{
								$linkitem['name'] = $this->ll_highlight_phrase($linkitem['name'], $searchterm, '<span class="highlight_word">', '</span>');
							}

							$catlink = '<div class="'. $catnameoutput . '">';

							if ($catdescpos == "right" || $catdescpos == '')
								$catlink .= $linkitem['name'];

							if ($showcategorydesclinks)
							{
								$catlink .= "<span class='linklistcatnamedesc'>";
								$linkitem['description'] = str_replace("[", "<", $linkitem['description']);
								$linkitem['description'] = str_replace("]", ">", $linkitem['description']);
								$catlink .= $linkitem['description'];
								$catlink .= '</span>';
							}

							if ($catdescpos == "left")
								$catlink .= $linkitem['name'];

							$catlink .= '</div>';
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

				if ($rssfeedinline == true) 
					include_once(ABSPATH . WPINC . '/feed.php');

				if ($showuserlinks == true || strpos($linkitem['link_description'], "LinkLibrary:AwaitingModeration:RemoveTextToApprove") == false)
				{
					$linkcount = $linkcount + 1;

					if ($linkaddfrequency > 0)
						if (($linkcount - 1) % $linkaddfrequency == 0)
							$output .= stripslashes($addbeforelink);

					if (!isset($linkitem['recently_updated'])) $linkitem['recently_updated'] = false; 
					$output .= stripslashes($beforeitem);
					if ($showupdated && $linkitem['recently_updated'])
						$output .= get_option('links_recently_updated_prepend'); 

					$the_link = '#';
					if (!empty($linkitem['link_url']) )
						$the_link = esc_html($linkitem['link_url']);

					$the_second_link = '#';
					if (!empty($linkitem['link_second_url']) )
						$the_second_link = esc_html($linkitem['link_second_url']);

					$rel = $linkitem['link_rel'];
					if ('' != $rel and !$nofollow and !$linkitem['link_no_follow'])
						$rel = ' rel="' . $rel . '"';
					else if ('' != $rel and ($nofollow or $linkitem['link_no_follow']))
						$rel = ' rel="' . $rel . ' nofollow"';
					else if ('' == $rel and ($nofollow or $linkitem['link_no_follow']))
						$rel = ' rel="nofollow"';

					if ($use_html_tags) {
						$descnotes = $linkitem['link_notes'];
						$descnotes = str_replace("[", "<", $descnotes);
						$descnotes = str_replace("]", ">", $descnotes);
					}
					else
						$descnotes = esc_html($linkitem['link_notes'], ENT_QUOTES);

					if ($use_html_tags) {
						$desc = $linkitem['link_description'];
						$desc = str_replace("[", "<", $desc);
						$desc = str_replace("]", ">", $desc);
					} else {
						$desc = esc_html($linkitem['link_description'], ENT_QUOTES);
					}

					$cleanname = esc_html($linkitem['link_name'], ENT_QUOTES);
                    
                    if ( $use_html_tags ) {
                        $textfield = stripslashes( $linkitem['link_textfield'] );
                        $textfield = str_replace( '[', '<', $textfield );
						$textfield = str_replace( ']', '>', $textfield );
                    } else {
                        $textfield = stripslashes( $linkitem['link_textfield'] );
                    }
					

					if ($mode == "search")
					{
						foreach ($searchterms as $searchterm)
						{
							$descnotes = $this->ll_highlight_phrase($descnotes, $searchterm, '<span class="highlight_word">', '</span>');
							$desc = $this->ll_highlight_phrase($desc, $searchterm, '<span class="highlight_word">', '</span>');
							$name = $this->ll_highlight_phrase($linkitem['link_name'], $searchterm, '<span class="highlight_word">', '</span>');
							$textfield = $this->ll_highlight_phrase($textfield, $searchterm, '<span class="highlight_word">', '</span>');
						}
				}
					else
						$name = $cleanname;

                    if ( $linktitlecontent == 'linkname' ) {
                        $title = $cleanname;
                    } elseif ($linktitlecontent == 'linkdesc' ) {
                        $title = $desc;
                    }

					if ($showupdated) {
					   if (substr($linkitem['link_updated'],0,2) != '00') {
							$title .= ' ('.__('Last updated', 'link-library') . '  ' . date(get_option('links_updated_date_format'), $linkitem['link_updated'] + (get_option('gmt_offset') * 3600)) .')';
						}
					}

					if (!empty( $title ) )
						$title = ' title="' . $title . '"';

					$alt = ' alt="' . $cleanname . '"';
						
					$target = $linkitem['link_target'];
					if ('' != $target)
						$target = ' target="' . $target . '"';
					else 
					{
						$target = $linktarget;
						if ('' != $target)
							$target = ' target="' . $target . '"';
					}
									
					if ($dragndroporder == '') $dragndroporder = '1,2,3,4,5,6,7,8,9,10';
						$dragndroparray = explode(',', $dragndroporder);
						if ($dragndroparray)
						{
							foreach ($dragndroparray as $arrayelements) {
								switch ($arrayelements) {
									case 1: 	//------------------ Image Output --------------------
										
										if ( (($linkitem['link_image'] != '' || $usethumbshotsforimages)) && ($show_images)) {
											$imageoutput = stripslashes($beforeimage) . '<a href="';

                                            if ( !$enablelinkpopup ) {
                                                if ($sourceimage == 'primary' || $sourceimage == '')
                                                    $imageoutput .= $the_link;
                                                elseif ($sourceimage == 'secondary')
                                                    $imageoutput .= $the_second_link;
                                            } else {
                                                    $imageoutput .= plugins_url( 'linkpopup.php?linkid=' . $linkitem['proper_link_id'] . '&settings=' . $settings . '&height=' . ( empty( $popupheight ) ? 300 : $popupheight ) . '&width=' . ( empty( $popupwidth ) ? 400 : $popupwidth ), __FILE__ ) . '&xpath=' . $xpath;
                                                }

											$imageoutput .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enablelinkpopup ? 'thickbox' : 'track_this_link' ) . '' . ( $linkitem['link_featured'] ? 'featured' : '' ). '" ' . $rel . $title . $target. '>';

											if ($usethumbshotsforimages)
											{
												if ( !empty( $thumbshotscid ) )
													$imageoutput .= '<img src="http://images.thumbshots.com/image.aspx?cid=' . rawurlencode( $thumbshotscid ) . 
														'&v=1&w=120&url=' . $the_link . '"';											
											}
											elseif ( strpos($linkitem['link_image'], 'http') !== false )
												$imageoutput .= '<img src="' . $linkitem['link_image'] . '"';
											else // If it's a relative path
												$imageoutput .= '<img src="' . get_option('siteurl') . $linkitem['link_image'] . '"';
                                            
                                            if ( !$usethumbshotsforimages || ($usethumbshotsforimages && !empty( $thumbshotscid ) ) ) {

                                                $imageoutput .= $alt . $title;

                                                if ($imageclass != '')
                                                    $imageoutput .= ' class="' . $imageclass . '" ';

                                                $imageoutput .= "/>";

                                                $imageoutput .= '</a>' . stripslashes($afterimage);
                                            }
                                        }                                                

										if ( ($linkitem['link_image'] != '' || ( $usethumbshotsforimages && !empty( $thumbshotscid ) ) )  && ($show_images) ) {
											$output .= $imageoutput;
											break;
										}
										elseif ($show_images == false || $shownameifnoimage == false)
											break;

									case 2: 	//------------------ Name Output --------------------   
										if (($showname == true) || ($show_images == true && $linkitem['link_image'] == '' && $arrayelements == 1))
										{
											$output .= stripslashes($beforelink);
											
											if (($sourcename == 'primary' && $the_link != '#') || ($sourcename == 'secondary' && $the_second_link != '#'))
											{
												$output .= '<a href="';

												if ( !$enablelinkpopup ) {
                                                    if ( $sourcename == 'primary' || $sourcename == '' )
                                                        $output .= $the_link;
                                                    elseif ( $sourcename == 'secondary' )
    													$output .= $the_second_link;
                                                } else {
                                                    $output .= plugins_url( 'linkpopup.php?linkid=' . $linkitem['proper_link_id'] . '&settings=' . $settings . '&height=' . ( empty( $popupheight ) ? 300 : $popupheight ) . '&width=' . ( empty( $popupwidth ) ? 400 : $popupwidth ), __FILE__ ) . '&xpath=' . $xpath;
                                                }

												$output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enablelinkpopup ? 'thickbox' : 'track_this_link' ) . ( $linkitem['link_featured'] ? ' featured' : '' ). '" ' . $rel . $title . $target. '>';
											}
											
											$output .= $name;
											
											if (($sourcename == 'primary' && $the_link != '#') || ($sourcename == 'secondary' && $the_second_link != '#'))
												$output .= '</a>';

											if (($showadmineditlinks) && current_user_can("manage_links")) {
												$output .= $between . '<a href="' . add_query_arg( array( 'action' => 'edit', 'link_id' => $linkitem['proper_link_id'] ), admin_url( 'link.php' ) ) . '">(' . __('Edit', 'link-library') . ')</a>';
											}

											if ($showupdated && $linkitem['recently_updated']) {
												$output .= get_option('links_recently_updated_append');
											}

											$output .= stripslashes($afterlink);
										}

										break;

									case 3: 	//------------------ Date Output --------------------   

										$formatteddate = date("F d Y", $linkitem['link_date']);

										if ($showdate)
											$output .= $between . stripslashes($beforedate) . $formatteddate . stripslashes($afterdate);

										break;

									case 4: 	//------------------ Description Output --------------------   

										if ($showdescription)
											$output .= $between . stripslashes($beforedesc) . $desc . stripslashes($afterdesc);

										break;

									case 5: 	//------------------ Notes Output --------------------   

										if ($shownotes) {
											$output .= $between . stripslashes($beforenote) . $descnotes . stripslashes($afternote);
										}

										break;

									case 6: 	//------------------ RSS Icons Output --------------------

										if ($show_rss || $show_rss_icon || $rsspreview)
											$output .= stripslashes($beforerss) . '<div class="rsselements">';

										if ($show_rss && ($linkitem['link_rss'] != '')) {
											$output .= $between . '<a class="rss" href="' . $linkitem['link_rss'] . '">RSS</a>';
										}
										if ($show_rss_icon && ($linkitem['link_rss'] != '')) {
											$output .= $between . '<a class="rssicon" href="' . $linkitem['link_rss'] . '"><img src="' . plugins_url( 'icons/feed-icon-14x14.png', __FILE__ ) . '" /></a>';
										}
										if ($rsspreview && $linkitem['link_rss'] != '')
										{
											$output .= $between . '<a href="' . plugins_url( 'rsspreview.php?keepThis=true&linkid=' . $linkitem['proper_link_id'] . '&previewcount=' . $rsspreviewcount . 'height=' . (($rsspreviewwidth == "") ?  900 : $rsspreviewwidth) . '&width=' . (($rsspreviewheight == "") ? 700 : $rsspreviewheight) . '&xpath=' . urlencode( $xpath ), __FILE__ ) . '" title="' . __('Preview of RSS feed for', 'link-library') . ' ' . $cleanname . '" class="thickbox"><img src="' . plugins_url( 'icons/preview-16x16.png', __FILE__ ) . '" /></a>';
										}
										
										if ($show_rss || $show_rss_icon || $rsspreview)
											$output .= '</div>' . stripslashes($afterrss);

										if ($rssfeedinline && $linkitem['link_rss'])
										{
											$rss = fetch_feed($linkitem['link_rss']);
											if (!is_wp_error( $rss ) ) : 
												$maxitems = $rss->get_item_quantity($rssfeedinlinecount); 

												$rss_items = $rss->get_items(0, $maxitems);
												
												if ($rss_items)
												{
													$output .= '<div id="ll_rss_results">';

													foreach($rss_items as $item)
													{
														$output .= '<div class="chunk" style="padding:0 5px 5px;">';
														$output .= '<div class="rsstitle"><a target="feedwindow" href="' . $item->get_permalink() . '">' . $item->get_title() . '</a> - ' . $item->get_date('j F Y | g:i a') . '</div>';
														if ($rssfeedinlinecontent) $output .= '<div class="rsscontent">' . $item->get_description() . '</div>';
														$output .= '</div>';
														$output .= '<br />';													}

													$output .= '</div>';
												}

											endif;
										}
										break;
									case 7: 	//------------------ Web Link Output --------------------   

										if ($displayweblink != 'false') {
											$output .= $between . stripslashes($beforeweblink) . "<a href='";

											if ($sourceweblink == "primary" || $sourceweblink == "")
												$output .= $the_link;
											elseif ($sourceweblink == "secondary")
												$output .= $the_second_link;

											$output .= "' id='link-" . $linkitem['proper_link_id'] . "' class='track_this_link' " . $target . ">";

											if ($displayweblink == 'address')
											{
												if (($sourceweblink == "primary" || $sourceweblink == '') && $the_link != '')
													$output .= $the_link;
												elseif ($sourceweblink == "secondary" && $the_second_link != '')
													$output .= $the_second_link;
											}
											elseif ($displayweblink == 'label' && $weblinklabel != '')
												$output .= $weblinklabel;

											$output .= "</a>" . stripslashes($afterweblink);
										}

										break;
									case 8: 	//------------------ Telephone Output --------------------   

										if ($showtelephone != 'false')
										{
											$output .= $between . stripslashes($beforetelephone);

											if ($showtelephone != 'plain')
											{
												$output .= "<a href='";

												if (($sourcetelephone == "primary" || $sourcetelephone == '') && $the_link != '')
													$output .= $the_link;
												elseif ($sourcetelephone == "secondary" && $the_second_link != '')
													$output .= $the_second_link;

												$output .= "' id='link-" . $linkitem['proper_link_id'] . "' class='track_this_link' >";
											}

											if ($showtelephone == 'link' || $showtelephone == "plain")
												$output .= $linkitem['link_telephone'];
											elseif ($showtelephone == 'label')
												$output .= $telephonelabel;

											if ($showtelephone != 'plain')
												$output .= "</a>";

											$output .= stripslashes($aftertelephone);
										}
										break;
									case 9: 	//------------------ E-mail Output --------------------   

										if ($showemail != 'false')
										{
											$output .= $between . stripslashes($beforeemail);

											if ($showemail != 'plain')
											{
												$output .= "<a href='";

												if ($showemail == 'mailto' || $showemail == 'mailtolabel')
													$output .= "mailto:" . $linkitem['link_email'];
												elseif ($showemail == 'command' || $showemail == 'commandlabel')
												{
													$newcommand = str_replace("#email", $linkitem['link_email'], $emailcommand);
													$cleanlinkname = str_replace(" ", "%20", $linkitem['link_name']);
													$newcommand = str_replace("#company", $cleanlinkname, $newcommand);
													$output .= $newcommand;												
												}

												$output .= "'>";
											}

											if ($showemail == 'plain' || $showemail == 'mailto' || $showemail == 'command')
												$output .= $linkitem['link_email'];
											elseif ($showemail == 'mailtolabel' || $showemail == 'commandlabel')
												$output .= $emaillabel;
												
											if ($showemail != 'plain')
												$output .= "</a>";

											$output .= stripslashes($afteremail);
										}

										break;
									case 10: 	//------------------ Link Hits Output --------------------   
									
										if ($showlinkhits)
										{
											$output .= $between . stripslashes($beforelinkhits);
											
											$output .= $linkitem['link_visits'];
											
											$output .= stripslashes($afterlinkhits);
										}

										break;

									case 11: 	//------------------ Link Rating Output --------------------   

										if ($showrating)
										{
											$output .= $between . stripslashes($beforelinkrating);

											$output .= $linkitem['link_rating'];

											$output .= stripslashes($afterlinkrating);
										}

										break;
										
									case 12: 	//------------------ Link Large Description Output --------------------   

										if ($showlargedescription)
										{
											$output .= $between . stripslashes($beforelargedescription);

											$output .= $textfield;

											$output .= stripslashes($afterlargedescription);
										}

										break;
									}
								}
							}

					$output .= stripslashes($afteritem) . "\n";

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
            
            if ( $usethumbshotsforimages )
                $output .= '<div class="llthumbshotsnotice"><a href="http://www.thumbshots.com" target="_blank" title="Thumbnails Screenshots by Thumbshots">Thumbnail Screenshots by Thumbshots</a></div>';
            
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
							$output .= "<a href='?page_id=" . get_the_ID() . "&linkresultpage=" . $previouspagenumber . "'>" . __('Previous', 'link-library') . "</a>";
						elseif ($showonecatonly)
						{
							if ($showonecatmode == 'AJAX' || $showonecatmode == '')
								$output .= "<a href='#' onClick=\"showLinkCat('" . $AJAXcatid . "', '" . $settings . "', " . $previouspagenumber . ");return false;\" >" . __('Previous', 'link-library') . "</a>";
							elseif ($showonecatmode == 'HTMLGET')
								$output .= "<a href='?page_id=" . get_the_ID() . "&linkresultpage=" . $previouspagenumber . "&cat_id=" . $AJAXcatid . "' >" . __('Previous', 'link-library') . "</a>";
						}

						$output .= "</span>";
					}
					else
						$output .= "<span class='previousnextinactive'>" . __('Previous', 'link-library') . "</span>";

					for ($counter = 1; $counter <= $numberofpages; $counter++)
					{
						if ($counter <= 2 || $counter >= $numberofpages - 1 || ($counter <= $pagenumber + 2 && $counter >= $pagenumber - 2))
						{
							if ($counter != $pagenumber)
								$output .= "<span class='unselectedpage'>";
							else
								$output .= "<span class='selectedpage'>";

							if (!$showonecatonly)
								$output .= "<a href='?page_id=" . get_the_ID() . "&linkresultpage=" . $counter . "'>" . $counter . "</a>";
							elseif ($showonecatonly)
							{
								if ($showonecatmode == 'AJAX' || $showonecatmode == '')
									$output .= "<a href='#' onClick=\"showLinkCat('" . $AJAXcatid . "', '" . $settings . "', " . $counter . ");return false;\" >" . $counter . "</a>";
								elseif ($showonecatmode == 'HTMLGET')
									$output .= "<a href='?page_id=" . get_the_ID() . "&linkresultpage=" . $counter . "&cat_id=" . $AJAXcatid . "' >" . $counter . "</a>";
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
							$output .= "<a href='?page_id=" . get_the_ID() . "&linkresultpage=" . $nextpagenumber . "'>" . __('Next', 'link-library') . "</a>";
						elseif ($showonecatonly)
						{
							if ($showonecatmode == 'AJAX' || $showonecatmode == '')
								$output .= "<a href='#' onClick=\"showLinkCat('" . $AJAXcatid . "', '" . $settings . "', " . $nextpagenumber . ");return false;\" >" . __('Next', 'link-library') . "</a>";
							elseif ($showonecatmode == 'HTMLGET')
								$output .= "<a href='?page_id=" . get_the_ID() . "&linkresultpage=" . $nextpagenumber . "&cat_id=" . $AJAXcatid . "' >" . __('Next', 'link-library') . "</a>";
						}

						$output .= "</span>";
					}
					else
						$output .= "<span class='previousnextinactive'>" . __('Next', 'link-library') . "</span>";

					$output .= "</div>";
				}
			}
			$xpath = $this->relativePath( dirname( __FILE__ ), ABSPATH );

			$output .= "<script type='text/javascript'>\n";
			$output .= "jQuery(document).ready(function()\n";
			$output .= "{\n";
			$output .= "jQuery('a.track_this_link').click(function() {\n";
			$output .= "linkid = this.id;\n";
			$output .= "linkid = linkid.substring(5);";
			$output .= "path = '" . $xpath . "';";
			$output .= "jQuery.post('" . plugins_url( 'tracker.php', __FILE__ ) . "', {id:linkid, xpath:path});\n";
			$output .= "return true;\n";
			$output .= "});\n";
			$output .= "});\n";
			$output .= "</script>";
			unset( $xpath );
			$currentcategory = $currentcategory + 1;

			$output .= "</div>\n";

		}
		else
		{
			$output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
			$output .= __('No links found', 'link-library') . ".\n";
			$output .= "</div>";
		}
		
		$output .= "\n<!-- End of Link Library Output -->\n\n";

		return $output;
	}

	function PrivateLinkLibrarySearchForm($searchlabel = 'Search', $searchresultsaddress = '') {

		if ($searchlabel == "") $searchlabel = __('Search', 'link-library');
		$output = "<form method='get' id='llsearch'";
                if ($searchresultsaddress != '')
                    $output .= " action='" . $searchresultsaddress . "'";
                $output .= ">\n";
		$output .= "<div>\n";
		$output .= "<input type='text' onfocus=\"this.value=''\" value='" . $searchlabel . "...' name='searchll' id='searchll' />\n";
		$output .= "<input type='hidden' value='" .  get_the_ID() . "' name='page_id' id='page_id' />\n";
		$output .= "<input type='submit' value='" . $searchlabel . "' />\n";
		$output .= "</div>\n";
		$output .= "</form>\n\n";
		
		return $output;
	}

	function PrivateLinkLibraryAddLinkForm($selectedcategorylist = '', $excludedcategorylist = '', $addnewlinkmsg = '', $linknamelabel = '', $linkaddrlabel = '',
											$linkrsslabel = '', $linkcatlabel = '', $linkdesclabel = '', $linknoteslabel = '', $addlinkbtnlabel = '', $hide_if_empty = true,
											$showaddlinkrss = false, $showaddlinkdesc = false, $showaddlinkcat = false, $showaddlinknotes = false,
											$addlinkreqlogin = false, $debugmode = false, $addlinkcustomcat = false, $linkcustomcatlabel = '',
											$linkcustomcatlistentry = 'User-submitted category (define below)', $showaddlinkreciprocal = false,
											$linkreciprocallabel = '', $showaddlinksecondurl = false, $linksecondurllabel = '',
											$showaddlinktelephone = false, $linktelephonelabel = '', $showaddlinkemail = false, $linkemaillabel = '',
											$showcaptcha = false, $captureddata = '', $linksubmitternamelabel = '', $showlinksubmittername = false,
											$linksubmitteremaillabel = '', $showaddlinksubmitteremail = false, $linksubmittercommentlabel = '',
											$showlinksubmittercomment = false, $linksubmissionthankyouurl = '', $addlinkcatlistoverride = '',
											$showcustomcaptcha = false, $customcaptchaquestion = '', $linklargedesclabel = 'Large Description', $showuserlargedescription = false, $usetextareaforusersubmitnotes = false, $settings = 1, $code = 'link-library-addlink') {
											
		global $wpdb;
                $output = "";
                
                $settingsname = 'LinkLibraryPP' . $settings;
                $options = get_option($settingsname);
                
                if ($code == 'link-library-addlink' || $code == 'link-library-addlinkcustommsg')
                {
                    if (isset($_GET['addlinkmessage']))
                        {
                            if ($_GET['addlinkmessage'] == 1)
                                $output = "<div class='llmessage'>" . __('Confirm code not given', 'link-library') . ".</div>";
                            elseif ($_GET['addlinkmessage'] == 2)
                                $output = "<div class='llmessage'>" . __('Captcha code is wrong', 'link-library') . ".</div>";
                            elseif ($_GET['addlinkmessage'] == 3)
                                $output = "<div class='llmessage'>" . __('Captcha code is only valid for 5 minutes', 'link-library') . ".</div>";
                            elseif ($_GET['addlinkmessage'] == 4)
                                $output = "<div class='llmessage'>" . __('No captcha cookie given. Make sure cookies are enabled', 'link-library') . ".</div>";
                            elseif ($_GET['addlinkmessage'] == 5)
                                $output = "<div class='llmessage'>" . __('Captcha answer was not provided.', 'link-library') . "</div>";
                            elseif ($_GET['addlinkmessage'] == 6)
                                $output = "<div class='llmessage'>" . __('Captcha answer is incorrect', 'link-library') . ".</div>";
                            elseif ($_GET['addlinkmessage'] == 7)
                                $output = "<div class='llmessage'>" . __('User Category was not provided correctly. Link insertion failed.', 'link-library') . "</div>";
                            elseif ($_GET['addlinkmessage'] == 8)
                            {
                                $output .= "<div class='llmessage'>" . $options['newlinkmsg'];
                                if ($options['showuserlinks'] == false)
                                        $output .= " " . $options['moderatemsg'];
                                $output .= "</div>";	
                            }
                            elseif ($_GET['addlinkmessage'] == 9)
                                $output = "<div class='llmessage'>" . __('Error: Link does not have an address.', 'link-library') . "</div>";
                            elseif ($_GET['addlinkmessage'] == 10)
                                $output = "<div class='llmessage'>" . __('Error: Link already exists.', 'link-library') . "</div>";                            
                        }
                }
		
		if ($code == 'link-library-addlink' && (($addlinkreqlogin && current_user_can("read")) || !$addlinkreqlogin))
		{
			$output .= "<form method='post' id='lladdlink' action='" . plugins_url('usersubmission.php', __FILE__). "'>\n";
                        
                        $output .= wp_nonce_field('LL_ADDLINK_FORM', '_wpnonce', true, false);
                        $output .= "<input type='hidden' name='thankyouurl' value='" . $linksubmissionthankyouurl . "' />";
                        global $wp_query;
                        $thePostID = $wp_query->post->ID;
                        $output .= "<input type='hidden' name='pageid' value='" . $thePostID . "' />";
                        $output .= "<input type='hidden' name='settingsid' value='" . $settings . "' />";
                        
                        $xpath = $this->relativePath( dirname( __FILE__ ), ABSPATH );
                        $output .= "<input type='hidden' name='xpath' value='" . esc_attr( $xpath ) . "' />";
                        unset( $xpath );

			$output .= "<div class='lladdlink'>\n";
			
			if ($addnewlinkmsg == "") $addnewlinkmsg = __('Add new link', 'link-library');
			$output .= "<div id='lladdlinktitle'>" . $addnewlinkmsg . "</div>\n";

			$output .= "<table>\n";

			if ($linknamelabel == "") $linknamelabel = __('Link name', 'link-library');
			$output .= "<tr><th>" . $linknamelabel . "</th><td><input type='text' name='link_name' id='link_name' value='" . ( isset( $_GET['addlinkname'] ) ? esc_html(stripslashes($_GET['addlinkname']), '1') : '') . "' /></td></tr>\n";

			if ($linkaddrlabel == "") $linkaddrlabel = __('Link address', 'link-library');
			$output .= "<tr><th>" . $linkaddrlabel . "</th><td><input type='text' name='link_url' id='link_url' value='" . ( isset( $_GET['addlinkurl'] ) ? esc_html(stripslashes($_GET['addlinkurl']), '1') : '' ) . "' /></td></tr>\n";

			if ($showaddlinkrss)
			{
				if ($linkrsslabel == "") $linkrsslabel = __('Link RSS', 'link-library');
				$output .= "<tr><th>" . $linkrsslabel . "</th><td><input type='text' name='link_rss' id='link_rss' value='" . ( isset( $_GET['addlinkrss'] ) ? esc_html(stripslashes($_GET['addlinkrss']), '1') : '' ) . "' /></td></tr>\n";
			}

			$linkcatquery = "SELECT distinct t.name, t.term_id, t.slug as category_nicename, tt.description as category_description ";
			$linkcatquery .= "FROM " . $this->db_prefix() . "terms t ";
			$linkcatquery .= "LEFT JOIN " . $this->db_prefix() . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
			$linkcatquery .= "LEFT JOIN " . $this->db_prefix() . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";

			$linkcatquery .= "WHERE tt.taxonomy = 'link_category' ";

			if ($selectedcategorylist != "")
			{
				$linkcatquery .= " AND t.term_id in (" . $selectedcategorylist. ")";
			}

			if ($excludedcategorylist != "")
			{
				$linkcatquery .= " AND t.term_id not in (" . $excludedcategorylist . ")";
			}

			$linkcatquery .= " ORDER by t.name ASC";

			$linkcats = $wpdb->get_results($linkcatquery);

			if ($debugmode)
			{
				$output .= "\n<!-- Category query for add link form:" . print_r($linkcatquery, TRUE) . "-->\n\n";
				$output .= "\n<!-- Results of Category query for add link form:" . print_r($linkcats, TRUE) . "-->\n";
			}

			if ($linkcats)
			{
				if ($showaddlinkcat)
				{
					if ($linkcatlabel == "") $linkcatlabel = __('Link category', 'link-library');

					$output .= "<tr><th>" . $linkcatlabel . "</th><td><SELECT name='link_category' id='link_category'>";

					if ($linkcustomcatlistentry == "") $linkcustomcatlistentry = __('User-submitted category (define below)', 'link-library');

					foreach ($linkcats as $linkcat)
					{
						$output .= "<OPTION VALUE='" . $linkcat->term_id . "' ";
						if ( isset($_GET['addlinkcat']) && $_GET['addlinkcat'] == $linkcat->term_id)
							$output .= "selected";
						$output .= ">" . $linkcat->name;
					}
					
					if ($addlinkcustomcat)
						$output .= "<OPTION VALUE='new'>" . stripslashes($linkcustomcatlistentry) . "\n";
					
					$output .= "</SELECT></td></tr>\n";
				}
				else
				{
					$output .= "<input type='hidden' name='link_category' id='link_category' value='" . $linkcats[0]->term_id . "'>";
				}
				
				if ($addlinkcustomcat)
					$output .= "<tr><th>" .  $linkcustomcatlabel . "</th><td><input type='text' name='link_user_category' id='link_user_category' value='" . ( isset( $_GET['addlinkusercat']) ? esc_html(stripslashes($_GET['addlinkusercat']), '1') : '') . "' /></td></tr>\n";
			}		
			
			if ($showaddlinkdesc)
			{
				if ($linkdesclabel == "") $linkdesclabel = __('Link description', 'link-library');
				$output .= "<tr><th>" . $linkdesclabel . "</th><td><input type='text' name='link_description' id='link_description' value='" . ( isset( $_GET['addlinkdesc'] ) ? esc_html(stripslashes($_GET['addlinkdesc']), '1') : '' ) . "' /></td></tr>\n";
			}
			
			if ($showuserlargedescription)
			{
				if ($linklargedesclabel == "") $linklargedesclabel = __('Large description', 'link-library');
				$output .= "<tr><th style='vertical-align: top'>" . $linklargedesclabel . "</th><td><textarea name='link_textfield' id='link_textfield' cols='66'>" . ( isset( $_GET['addlinktextfield'] ) ? esc_html(stripslashes($_GET['addlinktextfield']), '1') : '' ) . "</textarea></td></tr>\n";
			}
			
			if ($showaddlinknotes)
			{
				if ($linknoteslabel == "") $linknoteslabel = __('Link notes', 'link-library');
				$output .= "<tr><th>" . $linknoteslabel . "</th><td>";
				
				if ($usetextareaforusersubmitnotes == false || $usetextareaforusersubmitnotes == '')
					$output .= "<input type='text' name='link_notes' id='link_notes' value='";
				elseif ($usetextareaforusersubmitnotes == true)
					$output .= "<textarea name='link_notes' id='link_notes'>";
				
				$output .= ( isset( $_GET['addlinknotes'] ) ? esc_html(stripslashes($_GET['addlinknotes']), '1') : '' );
				
				if ($usetextareaforusersubmitnotes == false || $usetextareaforusersubmitnotes == '')
					$output .= "' />";
				elseif ($usetextareaforusersubmitnotes == true)
					$output .= "</textarea>";
				
				$output .= "</td></tr>\n";
			}
			
			if ($showaddlinkreciprocal)
			{
				if ($linkreciprocallabel == "") $linkreciprocallabel = __('Reciprocal Link', 'link-library');
				$output .= "<tr><th>" . $linkreciprocallabel . "</th><td><input type='text' name='ll_reciprocal' id='ll_reciprocal' value='" . ( isset( $_GET['addlinkreciprocal'] ) ? esc_html(stripslashes($_GET['addlinkreciprocal']), '1') : '' ) . "' /></td></tr>\n";
			}
			
			if ($showaddlinksecondurl)
			{
				if ($linksecondurllabel == "") $linksecondurllabel = __('Secondary Address', 'link-library');
				$output .= "<tr><th>" . $linksecondurllabel . "</th><td><input type='text' name='ll_secondwebaddr' id='ll_secondwebaddr' value='" . ( isset( $_GET['addlinksecondurl'] ) ? esc_html(stripslashes($_GET['addlinksecondurl']), '1') : '' ) . "' /></td></tr>\n";
			}
			
			if ($showaddlinktelephone)
			{
				if ($linktelephonelabel == "") $linktelephonelabel = __('Telephone', 'link-library');
				$output .= "<tr><th>" . $linktelephonelabel . "</th><td><input type='text' name='ll_telephone' id='ll_telephone' value='" . ( isset( $_GET['addlinktelephone'] ) ? esc_html(stripslashes($_GET['addlinktelephone']), '1') : '' ) . "' /></td></tr>\n";
			}
			
			if ($showaddlinkemail)
			{
				if ($linkemaillabel == "") $linkemaillabel = __('E-mail', 'link-library');
				$output .= "<tr><th>" . $linkemaillabel . "</th><td><input type='text' name='ll_email' id='ll_email' value='" . ( isset( $_GET['addlinkemail'] ) ? esc_html(stripslashes($_GET['addlinkemail']), '1') : '' ) . "' /></td></tr>\n";
			}
			
			if ($showlinksubmittername)
			{
				if ($linksubmitternamelabel == "") $linksubmitternamelabel = __('Submitter Name', 'link-library');
				$output .= "<tr><th>" . $linksubmitternamelabel . "</th><td><input type='text' name='ll_submittername' id='ll_submittername' value='" . ( isset( $_GET['addlinksubmitname'] ) ? esc_html(stripslashes($_GET['addlinksubmitname']), '1') : '' ) . "' /></td></tr>\n";
			}
			
			if ($showaddlinksubmitteremail)
			{
				if ($linksubmitteremaillabel == "") $linksubmitteremaillabel = __('Submitter E-mail', 'link-library');
				$output .= "<tr><th>" . $linksubmitteremaillabel . "</th><td><input type='text' name='ll_submitteremail' id='ll_submitteremail' value='" . ( isset( $_GET['addlinksubmitemail'] ) ? esc_html(stripslashes($_GET['addlinksubmitemail']), '1') : '' ). "' /></td></tr>\n";
			}
			
			if ($showlinksubmittercomment)
			{
				if ($linksubmittercommentlabel == "") $linksubmittercommentlabel = __('Submitter Comment', 'link-library');
				$output .= "<tr><th style='vertical-align: top;'>" . $linksubmittercommentlabel . "</th><td><textarea name='ll_submittercomment' id='ll_submittercomment' cols='38''>" . ( isset( $_GET['addlinksubmitcomment'] ) ? esc_html(stripslashes($_GET['addlinksubmitcomment']), '1') : '' ) . "</textarea></td></tr>\n";
			}
			
			if ($showcaptcha)
			{
				$output .= "<tr><td></td><td><span id='captchaimage'><img src='" . plugins_url( 'captcha/easycaptcha.php', __FILE__ ) . "' /></span></td></tr>\n";
				$output .= "<tr><th>" . __('Enter code from above image', 'link-library') . "</th><td><input type='text' name='confirm_code' /></td></tr>\n";
			}
			
			if ($showcustomcaptcha)
			{
				if ($customcaptchaquestion == "") $customcaptchaquestion = __('Is boiling water hot or cold?', 'link-library');
				$output .= "<tr><th style='vertical-align: top;'>" . $customcaptchaquestion . "</th><td><input type='text' name='ll_customcaptchaanswer' id='ll_customcaptchaanswer' value='" . (isset( $_GET['ll_customcaptchaanswer'] ) ? esc_html(stripslashes($_GET['ll_customcaptchaanswer']), '1') : '' ) . "' /></td></tr>\n";
			}
						
			$output .= "</table>\n";
			
			if ($addlinkbtnlabel == "") $addlinkbtnlabel = __('Add link', 'link-library');
			$output .= '<span style="border:0;" class="LLUserLinkSubmit"><input type="submit" name="submit" value="' . $addlinkbtnlabel . '" /></span>';
			
			$output .= "</div>\n";
			$output .= "</form>\n\n";		
		}

		return $output;
	}

	function relativePath($from, $to, $ps = DIRECTORY_SEPARATOR) {
		$arFrom = explode($ps, rtrim($from, $ps));
		$arTo = explode($ps, rtrim($to, $ps));
		while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
			array_shift($arFrom);
			array_shift($arTo);
		}
		$return = str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);

		// Don't disclose anything about the path is it's not needed, i.e. is the standard		
		if( $return === '../../../' ) {
			$return = '';
        }

		return $return;
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
	 *   flatlist (default 'table') - When set to true, displays an unordered list instead of a table
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
	 *   cattargetaddress
	 *   rewritepage
	 *   showinvisible
	 */

	function LinkLibraryCategories($order = 'name', $hide_if_empty = true, $table_width = 100, $num_columns = 1, $catanchor = true, 
								   $flatlist = 'table', $categorylist = '', $excludecategorylist = '', $showcategorydescheaders = false,
								   $showonecatonly = false, $settings = '', $loadingicon = '/icons/Ajax-loader.gif', $catlistdescpos = 'right', $debugmode = false,
								   $pagination = false, $linksperpage = 5, $showcatlinkcount = false, $showonecatmode = 'AJAX', $cattargetaddress = '',
								   $rewritepage = '', $showinvisible = false, $showuserlinks = true, $showcatonsearchresults = false) {
		
		if (strpos($order, 'AdminSettings') != false)
		{
			$settingsetid = substr($order, 13);
			$settingsetname = "LinkLibraryPP" . $settingsetid;
			$options = get_option($settingsetname);
			
			$genoptions = get_option('LinkLibraryGeneral');

			return $this->PrivateLinkLibraryCategories($options['order'], $options['hide_if_empty'], $options['table_width'], $options['num_columns'], $options['catanchor'], $options['flatlist'],
									 $options['categorylist'], $options['excludecategorylist'], $options['showcategorydescheaders'], $options['showonecatonly'], '',
									 $options['loadingicon'], $options['catlistdescpos'], $genoptions['debugmode'], $options['pagination'], $options['linksperpage'],
									 $options['showcatlinkcount'], $options['showonecatmode'], $options['cattargetaddress'], $options['rewritepage'], $options['showinvisible'], $options['showuserlinks'], $options['showcatonsearchresults']);   
		}
		else
			return $this->PrivateLinkLibraryCategories($order, $hide_if_empty, $table_width, $num_columns, $catanchor, $flatlist, $categorylist, $excludecategorylist, $showcategorydescheaders,
			$showonecatonly, $settings, $loadingicon, $catlistdescpos, $debugmode, $pagination, $linksperpage, $showcatlinkcount, $showonecatmode, $cattargetaddress,
			$rewritepage, $showinvisible, $showuserlinks, $showcatonsearchresults);   
		
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
	 *   rsscachedir (default null) - Path for SimplePie library to store RSS cache information - Obsolete
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
	 *   dragndroporder (default 1,2,3,4,5,6,7,8,9,10) - Order to display link sub-sections
	 *   displayweblink (default 'false')
	 *   sourceweblink (default 'primary')
	 *   showtelephone (default 'false')
	 *   sourcetelephone (default 'primary')
	 *   showemail (default 'false')
	 *   showlinkhits (default false)
	 *   beforeweblink (default null)
	 *   afterweblink (default null)
	 *   weblinklabel (default null)
	 *   beforetelephone (default null)
	 *   aftertelephone (default null)
	 *   telephonelabel (default null)
	 *   beforeemail (default null)
	 *   afteremail (default null)
	 *   emaillabel (default null)
	 *   beforelinkhits (default null)
	 *   afterlinkhits (default null)
	 *   emailcommand (default null)
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
									$rssfeedinlinecount = 1, $beforerss = '', $afterrss = '', $rsscachedir = NULL, $direction = 'ASC', 
									$linkdirection = 'ASC', $linkorder = 'name', $pagination = false, $linksperpage = 5, $hidecategorynames = false,
									$settings = '', $showinvisible = false, $showdate = false, $beforedate = '', $afterdate = '', $catdescpos = 'right',
									$showuserlinks = false, $rsspreviewwidth = 900, $rsspreviewheight = 700, $beforeimage = '', $afterimage = '', $imagepos = 'beforename',
									$imageclass = '', $AJAXpageid = 1, $debugmode = false, $usethumbshotsforimages = false, $showonecatmode = 'AJAX',
									$dragndroporder = '1,2,3,4,5,6,7,8,9,10', $showname = true, $displayweblink = 'false', $sourceweblink = 'primary', $showtelephone = 'false',
									$sourcetelephone = 'primary', $showemail = 'false', $showlinkhits = false, $beforeweblink = '', $afterweblink = '', $weblinklabel = '',
									$beforetelephone = '', $aftertelephone = '', $telephonelabel = '', $beforeemail = '', $afteremail = '', $emaillabel = '', $beforelinkhits = '',
									$afterlinkhits = '', $emailcommand = '', $sourceimage = 'primary', $sourcename = 'primary', $thumbshotscid = '',
									$maxlinks = '', $beforelinkrating = '', $afterlinkrating = '', $showlargedescription = false, $beforelargedescription = '',
									$afterlargedescription = '', $featuredfirst = false, $shownameifnoimage = false, $enablelinkpopup = false, $popupwidth = 300, $popupheight = 400, $nocatonstartup = false, $linktitlecontent = 'linkname' ) {

		if (strpos($order, 'AdminSettings') !== false)
		{
			$settingsetid = substr($order, 13);
			$settingsetname = "LinkLibraryPP" . $settingsetid;
			$options = get_option($settingsetname);
			
			$genoptions = get_option('LinkLibraryGeneral');		

			return $this->PrivateLinkLibrary($options['order'], $options['hide_if_empty'], $options['catanchor'], $options['showdescription'], $options['shownotes'],
									  $options['showrating'], $options['showupdated'], $options['categorylist'], $options['show_images'],
									  false, $options['use_html_tags'], $options['show_rss'], $options['beforenote'],
									  $options['nofollow'], $options['excludecategorylist'], $options['afternote'], $options['beforeitem'],
									  $options['afteritem'], $options['beforedesc'], $options['afterdesc'], $options['displayastable'],
									  $options['beforelink'], $options['afterlink'], $options['showcolumnheaders'], $options['linkheader'],
									  $options['descheader'], $options['notesheader'], $options['catlistwrappers'], $options['beforecatlist1'], 
									  $options['beforecatlist2'], $options['beforecatlist3'], $options['divorheader'], $options['catnameoutput'],
									  $options['show_rss_icon'], $options['linkaddfrequency'], $options['addbeforelink'], $options['addafterlink'],
									  $options['linktarget'], $options['showcategorydesclinks'], $options['showadmineditlinks'], $options['showonecatonly'],
									  $AJAXcatid, $options['defaultsinglecat'], $options['rsspreview'], $options['rsspreviewcount'], $options['rssfeedinline'],
									  $options['rssfeedinlinecontent'], $options['rssfeedinlinecount'], $options['beforerss'], $options['afterrss'],
									  NULL, $options['direction'], $options['linkdirection'], $options['linkorder'],
									  $options['pagination'], $options['linksperpage'], $options['hidecategorynames'], $settingsetid, $options['showinvisible'],
									  $options['showdate'], $options['beforedate'], $options['afterdate'], $options['catdescpos'], $options['showuserlinks'],
									  $options['rsspreviewwidth'], $options['rsspreviewheight'], $options['beforeimage'], $options['afterimage'], $options['imagepos'],
									  $options['imageclass'], $AJAXpageid, $genoptions['debugmode'], $options['usethumbshotsforimages'], 'AJAX', $options['dragndroporder'],
									  $options['showname'], $options['displayweblink'], $options['sourceweblink'], $options['showtelephone'], $options['sourcetelephone'], 
									  $options['showemail'], $options['showlinkhits'], $options['beforeweblink'], $options['afterweblink'], $options['weblinklabel'],
									  $options['beforetelephone'], $options['aftertelephone'], $options['telephonelabel'], $options['beforeemail'], $options['afteremail'],
									  $options['emaillabel'], $options['beforelinkhits'], $options['afterlinkhits'], $options['emailcommand'], $options['sourceimage'],
									  $options['sourcename'], $genoptions['thumbshotscid'], $options['maxlinks'], $options['beforelinkrating'],
									  $options['afterlinkrating'], $options['showlargedescription'], $options['beforelargedescription'],
									  $options['afterlargedescription'], $options['featuredfirst'], $options['shownameifnoimage'], $options['enable_link_popup'],
                                      $options['popup_width'], $options['popup_height'], $options['nocatonstartup'], $options['linktitlecontent'] );
		}
		else
			return $this->PrivateLinkLibrary($order, $hide_if_empty, $catanchor, $showdescription, $shownotes, $showrating,
									$showupdated, $categorylist, $show_images, false, $use_html_tags, 
									$show_rss, $beforenote, $nofollow, $excludecategorylist, $afternote, $beforeitem, $afteritem,
									$beforedesc, $afterdesc, $displayastable, $beforelink, $afterlink, $showcolumnheaders, 
									$linkheader, $descheader, $notesheader, $catlistwrappers, $beforecatlist1, 
									$beforecatlist2, $beforecatlist3, $divorheader, $catnameoutput, $show_rss_icon,
									$linkaddfrequency, $addbeforelink, $addafterlink, $linktarget, $showcategorydesclinks, $showadmineditlinks,
									$showonecatonly, '', $defaultsinglecat, $rsspreview, $rsspreviewcount, $rssfeedinline, $rssfeedinlinecontent, $rssfeedinlinecount,
									$beforerss, $afterrss, NULL, $direction, $linkdirection, $linkorder,
									$pagination, $linksperpage, $hidecategorynames, $settings, $showinvisible, $showdate, $beforedate, $afterdate, $catdescpos,
									$showuserlinks, $rsspreviewwidth, $rsspreviewheight, $beforeimage, $afterimage, $imagepos, $imageclass, '', $debugmode,
									$usethumbshotsforimages, $showonecatmode, $dragndroporder, $showname, $displayweblink, $sourceweblink, $showtelephone,
									$sourcetelephone, $showemail, $showlinkhits, $beforeweblink, $afterweblink, $weblinklabel, $beforetelephone, $aftertelephone,
									$telephonelabel, $beforeemail, $afteremail, $emaillabel, $beforelinkhits, $afterlinkhits, $emailcommand, $sourceimage, $sourcename,
									$thumbshotscid, $maxlinks, $beforelinkrating, $afterlinkrating, $showlargedescription, $beforelargedescription,
									$afterlargedescription, $featuredfirst, $shownameifnoimage, $enablelinkpopup, $popupwidth, $popupheight, $nocatonstartup, $linktitlecontent );
	}
	
	/********************************************** Function to Process [link-library-cats] shortcode *********************************************/
	
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

		return $this->PrivateLinkLibraryCategories($options['order'], $options['hide_if_empty'], $options['table_width'], $options['num_columns'], $options['catanchor'], $options['flatlist'],
									 $selectedcategorylist, $excludedcategorylist, $options['showcategorydescheaders'], $options['showonecatonly'], $settings,
									 $options['loadingicon'], $options['catlistdescpos'], $genoptions['debugmode'], $options['pagination'], $options['linksperpage'],
									 $options['showcatlinkcount'], $options['showonecatmode'], $options['cattargetaddress'], $options['rewritepage'],
									 $options['showinvisible'], $options['showuserlinks'], $options['showcatonsearchresults']);
	}
	
	/********************************************** Function to Process [link-library-search] shortcode *********************************************/

	function link_library_search_func($atts) {
		extract(shortcode_atts(array(
			'settings' => ''
		), $atts));
		
		if ($settings == '')
			$options = get_option('LinkLibraryPP1');
		else
		{
			$settingsname = 'LinkLibraryPP' . $settings;
			$options = get_option($settingsname);
		}
		
		return $this->PrivateLinkLibrarySearchForm($options['searchlabel'], $options['searchresultsaddress']);
	}
	
	/********************************************** Function to Process [link-library-add-link] shortcode *********************************************/

	function link_library_insert_link( $linkdata, $wp_error = false, $addlinknoaddress = false) {
		global $wpdb;

		$defaults = array( 'link_id' => 0, 'link_name' => '', 'link_url' => '', 'link_rating' => 0 );

		$linkdata = wp_parse_args( $linkdata, $defaults );
		$linkdata = sanitize_bookmark( $linkdata, 'db' );

		extract( stripslashes_deep( $linkdata ), EXTR_SKIP );

		$update = false;

		if ( !empty( $link_id ) )
			$update = true;

		if ( trim( $link_name ) == '' ) {
			if ( trim( $link_url ) != '' ) {
				$link_name = $link_url;
			} else {
				return 0;
			}
		}

		if ($addlinknoaddress == false)
		{			
			if ( trim( $link_url ) == '' )
			return 0;
		}		
		
		if ( empty( $link_rating ) )
			$link_rating = 0;

		if ( empty( $link_image ) )
			$link_image = '';

		if ( empty( $link_target ) )
			$link_target = '';

		if ( empty( $link_visible ) )
			$link_visible = 'Y';

		if ( empty( $link_owner ) )
			$link_owner = get_current_user_id();

		if ( empty( $link_notes ) )
			$link_notes = '';

		if ( empty( $link_description ) )
			$link_description = '';

		if ( empty( $link_rss ) )
			$link_rss = '';

		if ( empty( $link_rel ) )
			$link_rel = '';

		// Make sure we set a valid category
		if ( ! isset( $link_category ) || 0 == count( $link_category ) || !is_array( $link_category ) ) {
			$link_category = array( get_option( 'default_link_category' ) );
		}

		if ( $update ) {
			if ( false === $wpdb->update( $wpdb->links, compact('link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_rating', 'link_rel', 'link_notes', 'link_rss'), compact('link_id') ) ) {
				if ( $wp_error )
					return new WP_Error( 'db_update_error', __( 'Could not update link in the database', 'link-library' ), $wpdb->last_error );
				else
					return 0;
			}
		} else {
			if ( false === $wpdb->insert( $wpdb->links, compact('link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_rel', 'link_notes', 'link_rss') ) ) {
				if ( $wp_error )
					return new WP_Error( 'db_insert_error', __( 'Could not insert link into the database', 'link-library' ), $wpdb->last_error );
				else
					return 0;
			}
			$link_id = (int) $wpdb->insert_id;
		}

		wp_set_link_cats( $link_id, $link_category );

		if ( $update )
			do_action( 'edit_link', $link_id );
		else
			do_action( 'add_link', $link_id );

		clean_bookmark_cache( $link_id );

		return $link_id;
	}

	function link_library_addlink_func($atts, $content, $code) {
		extract(shortcode_atts(array(
			'settings' => '',
			'categorylistoverride' => '',
			'excludecategoryoverride' => ''
		), $atts));
                
		if ($settings == '')
                    $settings = 1;

                $settingsname = 'LinkLibraryPP' . $settings;
                $options = get_option($settingsname);
                
                $genoptions = get_option('LinkLibraryGeneral');
				
		if ($categorylistoverride != '')
			$selectedcategorylist = $categorylistoverride;
		elseif ($options['addlinkcatlistoverride'] != '')
			$selectedcategorylist = $options['addlinkcatlistoverride'];
		else	
			$selectedcategorylist = $options['categorylist'];
			
		if ($excludecategoryoverride != '')
			$excludedcategorylist = $excludecategoryoverride;
		else
			$excludedcategorylist = $options['excludecategorylist'];
			
                return ( isset( $outputmessage ) ? $outputmessage : '') . $this->PrivateLinkLibraryAddLinkForm($selectedcategorylist, $excludedcategorylist, $options['addnewlinkmsg'], $options['linknamelabel'], $options['linkaddrlabel'],
                                                                                 $options['linkrsslabel'], $options['linkcatlabel'], $options['linkdesclabel'], $options['linknoteslabel'],
                                                                                 $options['addlinkbtnlabel'], $options['hide_if_empty'], $options['showaddlinkrss'], $options['showaddlinkdesc'],
                                                                                 $options['showaddlinkcat'], $options['showaddlinknotes'], $options['addlinkreqlogin'], $genoptions['debugmode'],
                                                                                 $options['addlinkcustomcat'], $options['linkcustomcatlabel'], $options['linkcustomcatlistentry'], 
                                                                                 $options['showaddlinkreciprocal'], $options['linkreciprocallabel'], $options['showaddlinksecondurl'], $options['linksecondurllabel'],
                                                                                 $options['showaddlinktelephone'], $options['linktelephonelabel'], $options['showaddlinkemail'], $options['linkemaillabel'],
                                                                                 $options['showcaptcha'], (isset($captureddata) ? $captureddata : null), $options['linksubmitternamelabel'], $options['showlinksubmittername'],
                                                                                 $options['linksubmitteremaillabel'], $options['showaddlinksubmitteremail'], $options['linksubmittercommentlabel'],
                                                                                 $options['showlinksubmittercomment'], $genoptions['linksubmissionthankyouurl'], $options['addlinkcatlistoverride'],
                                                                                 $options['showcustomcaptcha'], $options['customcaptchaquestion'], $options['linklargedesclabel'], $options['showuserlargedescription'], $options['usetextareaforusersubmitnotes'], $settings, $code);
		
		
	}
	
	/********************************************** Function to Process [link-library] shortcode *********************************************/

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
		{
			$settings = 1;
			$options = get_option('LinkLibraryPP1');		
		}
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
				
		if (floatval($genoptions['schemaversion']) < "4.6")
		{
			$this->ll_install();
			$genoptions = get_option('LinkLibraryGeneral');
			
			if ($settings == '')
				$options = get_option('LinkLibraryPP1');		
			else
			{
				$settingsname = 'LinkLibraryPP' . $settings;
				$options = get_option($settingsname);
			}
		}
		
		if ($genoptions['debugmode'] == true)
			$linklibraryoutput .= "\n<!-- Library Settings Info:" . print_r($options, TRUE) . "-->\n";
			
		$linklibraryoutput .= $this->PrivateLinkLibrary( $options['order'], $options['hide_if_empty'], $options['catanchor'], $selectedshowdescription, $selectedshownotes,
									  $options['showrating'], $options['showupdated'], $selectedcategorylist, $options['show_images'],
									  false, $options['use_html_tags'], $options['show_rss'], $options['beforenote'],
									  $options['nofollow'], $excludedcategorylist, $options['afternote'], $options['beforeitem'],
									  $options['afteritem'], $options['beforedesc'], $options['afterdesc'], $overridedisplayastable,
									  $options['beforelink'], $options['afterlink'], $options['showcolumnheaders'], $options['linkheader'],
									  $options['descheader'], $options['notesheader'], 	$options['catlistwrappers'], $options['beforecatlist1'], 
									  $options['beforecatlist2'], $options['beforecatlist3'], $options['divorheader'], $options['catnameoutput'],
									  $options['show_rss_icon'], $options['linkaddfrequency'], $options['addbeforelink'], $options['addafterlink'],
									  $options['linktarget'], $options['showcategorydesclinks'], $options['showadmineditlinks'],
									  $options['showonecatonly'], '', $options['defaultsinglecat'], $options['rsspreview'], $options['rsspreviewcount'], 
									  $options['rssfeedinline'], $options['rssfeedinlinecontent'], $options['rssfeedinlinecount'],
									  $options['beforerss'], $options['afterrss'], NULL, $options['direction'],
									  $options['linkdirection'], $options['linkorder'], $options['pagination'], $options['linksperpage'],
									  $options['hidecategorynames'], $settings, $options['showinvisible'], $options['showdate'], $options['beforedate'],
									  $options['afterdate'], $options['catdescpos'], $options['showuserlinks'], $options['rsspreviewwidth'], $options['rsspreviewheight'],
									  $options['beforeimage'], $options['afterimage'], $options['imagepos'], $options['imageclass'], '', $genoptions['debugmode'],
									  $options['usethumbshotsforimages'], $options['showonecatmode'], $options['dragndroporder'], $options['showname'], $options['displayweblink'],
									  $options['sourceweblink'], $options['showtelephone'], $options['sourcetelephone'], $options['showemail'], $options['showlinkhits'],
									  $options['beforeweblink'], $options['afterweblink'], $options['weblinklabel'], $options['beforetelephone'], $options['aftertelephone'],
									  $options['telephonelabel'], $options['beforeemail'], $options['afteremail'], $options['emaillabel'], $options['beforelinkhits'],
									  $options['afterlinkhits'], $options['emailcommand'], $options['sourceimage'], $options['sourcename'], $genoptions['thumbshotscid'],
									  $options['maxlinks'], $options['beforelinkrating'], $options['afterlinkrating'], $options['showlargedescription'],
									  $options['beforelargedescription'], $options['afterlargedescription'], $options['featuredfirst'], $options['shownameifnoimage'],
                                      ( isset($options['enable_link_popup']) ? $options['enable_link_popup'] : false ), ( isset($options['popup_width']) ? $options['popup_width'] : 300 ), ( isset( $options['popup_height'] ) ? $options['popup_height'] : 400 ), $options['nocatonstartup'], $options['linktitlecontent'] );
			
		return $linklibraryoutput;
	}
	

	function conditionally_add_scripts_and_styles($posts){
		if (empty($posts)) return $posts;
		
		$load_jquery = false;
		$load_thickbox = false;
		$load_style = false;
		global $testvar;
		
		$genoptions = get_option('LinkLibraryGeneral');

		if (is_admin()) 
		{
			$load_jquery = false;
			$load_thickbox = false;
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
						$load_jquery = true;
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
					
					if ( $options['showonecatonly'] ) {
						$load_jquery = true;
					}
			
					if ( $options['rsspreview'] || ( isset( $options['enable_link_popup'] ) && $options['enable_link_popup'] ) ) {
						$load_thickbox = true;
					}

					if ($options['publishrssfeed'] == true) {
						global $rss_settings;
						$rss_settings = $settingsetid;
					}	
				}
			}
				
			if ($genoptions['includescriptcss'] != '')
			{
				$pagelist = explode (',', $genoptions['includescriptcss']);
                $loadscripts = false;
				foreach($pagelist as $pageid) {
                    if ( ( $pageid == 'front' && is_front_page() ) ||
                         ( $pageid == 'category' && is_category() ) ||
                         ( $pageid == 'all') ||
                         ( is_page( $pageid ) ) ) {
                        $load_jquery = true;
						$load_thickbox = true;
						$load_style = true;                        
					}
				}   
			}
		}
		
        global $llstylesheet;
		if ( $load_style ) {			
			$llstylesheet = true;
		} else {
			$llstylesheet = false;
		}
	 
		if ( $load_jquery ) {
			wp_enqueue_script( 'jquery' );
		}
			
		if ( $load_thickbox ) {
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style ( 'thickbox' );
		}
	 
		return $posts;
	}
}

$my_link_library_plugin = new link_library_plugin();

?>