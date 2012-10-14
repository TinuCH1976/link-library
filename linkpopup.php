<?php
	require_once( '../../../wp-load.php' );
	require_once( 'link-library.php' );
	  
    if ( isset( $_GET['linkid'] ) && isset( $_GET['settings']) ) {
        $link_id = intval( $_GET['linkid'] );
        $settings_id = intval( $_GET['settings'] );
    } else {
        wp_die();
    }    
    
    $linkquery = "SELECT distinct *, l.link_id as proper_link_id, UNIX_TIMESTAMP(l.link_updated) as link_date, ";
    $linkquery .= "IF (DATE_ADD(l.link_updated, INTERVAL " . get_option('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated ";
    $linkquery .= "FROM " . $my_link_library_plugin->db_prefix() . "terms t ";
    $linkquery .= "LEFT JOIN " . $my_link_library_plugin->db_prefix() . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
    $linkquery .= "LEFT JOIN " . $my_link_library_plugin->db_prefix() . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
    $linkquery .= "LEFT JOIN " . $my_link_library_plugin->db_prefix() . "links l ON (tr.object_id = l.link_id) ";
    $linkquery .= "LEFT JOIN " . $my_link_library_plugin->db_prefix() . "links_extrainfo le ON (l.link_id = le.link_id) ";	
    $linkquery .= "WHERE tt.taxonomy = 'link_category' ";

	$linkquery .= "AND l.link_id = " . $link_id;
    
    global $wpdb;
    
    $linkitem = $wpdb->get_row($linkquery, ARRAY_A);
    
    $the_link = '#';
    if (!empty($linkitem['link_url']) )
        $the_link = esc_html($linkitem['link_url']);

    $the_second_link = '#';
    if (!empty($linkitem['link_second_url']) )
        $the_second_link = esc_html($linkitem['link_second_url']);
    
    $cleanname = esc_html($linkitem['link_name'], ENT_QUOTES);
    
    $name = $cleanname;
    
    $alt = ' alt="' . $cleanname . '"';
    
    $title = esc_html($linkitem['link_description'], ENT_QUOTES);
    
    if ('' != $title)
		$title = ' title="' . $title . '"';
    
    $rel = $linkitem['link_rel'];
    if ('' != $rel and !$options['nofollow'] and !$linkitem['link_no_follow'])
        $rel = ' rel="' . $rel . '"';
    else if ('' != $rel and ($options['nofollow'] or $linkitem['link_no_follow']))
        $rel = ' rel="' . $rel . ' nofollow"';
    else if ('' == $rel and ($options['nofollow'] or $linkitem['link_no_follow']))
        $rel = ' rel="nofollow"';
    
    $options = get_option( 'LinkLibraryPP' . $settings_id );
    
    $target = $linkitem['link_target'];
    if ( !empty( $target ) ) {
        $target = ' target="' . $target . '"';
    } else {
        $target = $options['linktarget'];
        if ( !empty( $target ) )
            $target = ' target="' . $target . '"';
    }
    
    $popup_text = ( !empty( $options['link_popup_text'] ) ? $options['link_popup_text'] : __( '%link_image%<br />Click through to visit %link_name%.', 'link-library') );
    
    if ( ( strpos( $popup_text, '%link_image%' ) !== false ) && !empty( $linkitem['link_image'] ) ) {
        $imageoutput = stripslashes( $options['beforeimage'] ) . '<a href="';

        if ($options['sourceimage'] == 'primary' || $options['sourceimage'] == '')
            $imageoutput .= $the_link;
        elseif ($options['sourceimage'] == 'secondary')
            $imageoutput .= $the_second_link;

        $imageoutput .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link ' . ( $linkitem['link_featured'] ? 'featured' : '' ). '" ' . $rel . $title . $target. '>';

        if ($options['usethumbshotsforimages'])
        {
            if ($options['thumbshotscid'] == '')
                $imageoutput .= '<img src="http://open.thumbshots.org/image.aspx?url=' . $the_link . '"';
            elseif ($options['thumbshotscid'] != '')
                $imageoutput .= '<img src="http://images.thumbshots.com/image.aspx?cid=' . $options['thumbshotscid'] . 
                    '&v=1&w=120&h=90&url=' . $the_link . '"';											
        }
        elseif ( strpos($linkitem['link_image'], 'http') !== false )
            $imageoutput .= '<img src="' . $linkitem['link_image'] . '"';
        else // If it's a relative path
            $imageoutput .= '<img src="' . get_option('siteurl') . $linkitem['link_image'] . '"';

        $imageoutput .= $alt . $title;

        if ($options['imageclass'] != '')
            $imageoutput .= ' class="' . $options['imageclass'] . '" ';

        $imageoutput .= "/>";

        $imageoutput .= '</a>' . stripslashes($options['afterimage']);
        
        $popup_text = str_replace( '%link_image%', $imageoutput, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_image%' ) !== false )  && empty( $linkitem['link_image'] ) ) {
        $popup_text = str_replace( '%link_image%', '', $popup_text );
    }
    
    
    if ( ( strpos( $popup_text, '%link_name%' ) !== false ) && !empty( $name ) ) {
        if ( ( $options['sourcename'] == 'primary' && $the_link != '#') || ($options['sourcename'] == 'secondary' && $the_second_link != '#')) {
            $nameoutput .= '<a href="';

            if ( $sourcename == 'primary' || $sourcename == '' )
                $nameoutput .= $the_link;
            elseif ( $sourcename == 'secondary' )
                $nameoutput .= $the_second_link;

            $nameoutput .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enablelinkpopup ? 'thickbox' : 'track_this_link' ) . ( $linkitem['link_featured'] ? ' featured' : '' ). '" ' . $rel . $title . $target. '>';
        }

        $nameoutput .= $name;

        if (($options['sourcename'] == 'primary' && $the_link != '#') || ($options['sourcename'] == 'secondary' && $the_second_link != '#'))
            $nameoutput .= '</a>';

        $popup_text = str_replace( '%link_name%', $nameoutput, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_name%' ) !== false ) && empty( $name ) ) {
        $popup_text = str_replace( '%link_name%', '', $popup_text );
    }
    
    if ( ( strpos( $popup_text, '%link_cat_name%' ) !== false ) && !empty( $linkitem['name'] ) ) {            $popup_text = str_replace( '%link_cat_name%', $linkitem['name'], $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_cat_name%' ) !== false ) && empty( $linkitem['name'] ) ) {
        $popup_text = str_replace( '%link_cat_name%', '', $popup_text );
    }
    
    if ( ( strpos( $popup_text, '%link_cat_desc%' ) !== false ) && !empty( $linkitem['description'] ) ) { 
        $cleandesc = str_replace('[', '<', $linkitem['description']);
        $cleandesc = str_replace(']', '>', $cleandesc);
            
        $popup_text = str_replace( '%link_cat_desc%', $cleandesc, $popup_text );
    } elseif ( ( strpos( $popup_text, '%link_cat_desc%' ) !== false ) && empty( $linkitem['description'] ) ) {
        $popup_text = str_replace( '%link_cat_desc%', '', $popup_text );
    }    
    
    echo '<div class="linkpopup">' . $popup_text . '</div>';
    
    ?>

