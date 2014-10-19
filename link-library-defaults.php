<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/************************** Function called to create default settings or to reset them on user request **************************/
function ll_reset_options( $settings = 1, $layout = 'list', $setoptions = 'return' ) {

    if ($layout == 'list') {
        $options['num_columns'] = 1;
        $options['showdescription'] = false;
        $options['shownotes'] = false;
        $options['beforenote'] = '<br />';
        $options['afternote'] = '';
        $options['beforeitem'] = '<li>';
        $options['afteritem'] = '</li>';
        $options['beforedesc'] = '';
        $options['afterdesc'] = '';
        $options['displayastable'] = false;
        $options['beforelink'] = '';
        $options['afterlink'] = '';
        $options['showcolumnheaders'] = false;
        $options['linkheader'] = '';
        $options['descheader'] = '';
        $options['notesheader'] = '';
        $options['beforerss'] = '';
        $options['afterrss'] = '';
        $options['beforedate'] = '';
        $options['afterdate'] = '';
        $options['beforeimage'] = '';
        $options['afterimage'] = '';
        $options['beforeweblink'] = '';
        $options['afterweblink'] = '';
        $options['beforetelephone'] = '';
        $options['aftertelephone'] = '';
        $options['beforeemail'] = '';
        $options['afteremail'] = '';
        $options['beforelinkhits'] = '';
        $options['afterlinkhits'] = '';
    }
    elseif ($layout == "table")
    {
        $options['num_columns'] = 3;
        $options['showdescription'] = true;
        $options['shownotes'] = true;
        $options['beforenote'] = '<td>';
        $options['afternote'] = '</td>';
        $options['beforeitem'] = '<tr>';
        $options['afteritem'] = '</tr>';
        $options['beforedesc'] = '<td>';
        $options['afterdesc'] = '</td>';
        $options['displayastable'] = true;
        $options['beforelink'] = '<td>';
        $options['afterlink'] = '</td>';
        $options['showcolumnheaders'] = true;
        $options['linkheader'] = 'Application';
        $options['descheader'] = 'Description';
        $options['notesheader'] = 'Similar to';
        $options['beforerss'] = '<td>';
        $options['afterrss'] = '</td>';
        $options['beforedate'] = '<td>';
        $options['afterdate'] = '</td>';
        $options['beforeimage'] = '<td>';
        $options['afterimage'] = '</td>';
        $options['beforeweblink'] = '<td>';
        $options['afterweblink'] = '</td>';
        $options['beforetelephone'] = '<td>';
        $options['aftertelephone'] = '</td>';
        $options['beforeemail'] = '<td>';
        $options['afteremail'] = '</td>';
        $options['beforelinkhits'] = '<td>';
        $options['afterlinkhits'] = '</td>';
    }

    $options['order'] = 'name';
    $options['hide_if_empty'] = true;
    $options['table_width'] = 100;
    $options['catanchor'] = true;
    $options['flatlist'] = 'table';
    $options['categorylist'] = null;
    $options['excludecategorylist'] = null;
    $options['showrating'] = false;
    $options['showupdated'] = false;
    $options['show_images'] = false;
    $options['use_html_tags'] = false;
    $options['show_rss'] = false;
    $options['nofollow'] = false;
    $options['catlistwrappers'] = 1;
    $options['beforecatlist1'] = '';
    $options['beforecatlist2'] = '';
    $options['beforecatlist3'] = '';
    $options['divorheader'] = false;
    $options['catnameoutput'] = 'linklistcatname';
    $options['show_rss_icon'] = false;
    $options['linkaddfrequency'] = 0;
    $options['addbeforelink'] = '';
    $options['addafterlink'] = '';
    $options['linktarget'] = '';
    $options['showcategorydescheaders'] = false;
    $options['showcategorydesclinks'] = false;
    $options['settingssetname'] = 'Default';
    $options['showadmineditlinks'] = true;
    $options['showonecatonly'] = false;
    $options['loadingicon'] = '/icons/Ajax-loader.gif';
    $options['defaultsinglecat'] = '';
    $options['rsspreview'] = false;
    $options['rsspreviewcount'] = 3;
    $options['rssfeedinline'] = false;
    $options['rssfeedinlinecontent'] = false;
    $options['rssfeedinlinecount'] = 1;
    $options['direction'] = 'ASC';
    $options['linkdirection'] = 'ASC';
    $options['linkorder'] = 'name';
    $options['pagination'] = false;
    $options['linksperpage'] = 5;
    $options['hidecategorynames'] = false;
    $options['showinvisible'] = false;
    $options['showdate'] = false;
    $options['catdescpos'] = 'right';
    $options['catlistdescpos'] = 'right';
    $options['showuserlinks'] = false;
    $options['addnewlinkmsg'] = __('Add new link', 'link-library');
    $options['linknamelabel'] = __('Link name', 'link-library');
    $options['linkaddrlabel'] = __('Link address', 'link-library');
    $options['linkrsslabel'] = __('Link RSS', 'link-library');
    $options['linkcatlabel'] = __('Link Category', 'link-library');
    $options['linkdesclabel'] = __('Link Description', 'link-library');
    $options['linknoteslabel'] = __('Link Notes', 'link-library');
    $options['addlinkbtnlabel'] = __('Add Link', 'link-library');
    $options['newlinkmsg'] = __('New link submitted.', 'link-library');
    $options['moderatemsg'] = __('It will appear in the list once moderated. Thank you.', 'link-library');
    $options['rsspreviewwidth'] = 900;
    $options['rsspreviewheight'] = 700;
    $options['imagepos'] = 'beforename';
    $options['imageclass'] = '';
    $options['emailnewlink'] = false;
    $options['showaddlinkrss'] = false;
    $options['showaddlinkdesc'] = false;
    $options['showaddlinkcat'] = false;
    $options['showaddlinknotes'] = false;
    $options['usethumbshotsforimages'] = false;
    $options['uselocalimagesoverthumbshots'] = false;
    $options['addlinkreqlogin'] = false;
    $options['showcatlinkcount'] = false;
    $options['publishrssfeed'] = false;
    $options['numberofrssitems'] = 10;
    $options['rssfeedtitle'] = __('Link Library-Generated RSS Feed', 'link-library');
    $options['rssfeeddescription'] = __('Description of Link Library-Generated Feed', 'link-library');
    $options['showonecatmode'] = 'AJAX';
    $options['paginationposition'] = 'AFTER';
    $options['addlinkcustomcat'] = false;
    $options['linkcustomcatlabel'] = __('User-submitted category', 'link-library');
    $options['linkcustomcatlistentry'] = __('User-submitted category (define below)', 'link-library');
    $options['searchlabel'] = 'Search';
    $options['dragndroporder'] = '1,2,3,4,5,6,7,8,9,10,11,12';
    $options['showname'] = true;
    $options['cattargetaddress'] = '';
    $options['displayweblink'] = 'false';
    $options['sourceweblink'] = 'primary';
    $options['showtelephone'] = 'false';
    $options['sourcetelephone'] = 'primary';
    $options['showemail'] = 'false';
    $options['showlinkhits'] = false;
    $options['weblinklabel'] = '';
    $options['telephonelabel'] = '';
    $options['emaillabel'] = '';
    $options['showaddlinkreciprocal'] = false;
    $options['linkreciprocallabel'] = __('Reciprocal Link', 'link-library');
    $options['showaddlinksecondurl'] = false;
    $options['linksecondurllabel'] = __('Secondary Address', 'link-library');
    $options['showaddlinktelephone'] = false;
    $options['linktelephonelabel'] = __('Telephone', 'link-library');
    $options['showaddlinkemail'] = false;
    $options['linkemaillabel'] = __('E-mail', 'link-library');
    $options['emailcommand'] = '';
    $options['sourceimage'] = 'primary';
    $options['sourcename'] = 'primary';
    $options['enablerewrite'] = false;
    $options['rewritepage'] = '';
    $options['storelinksubmitter'] = false;
    $options['maxlinks'] = '';
    $options['showcaptcha'] = false;
    $options['beforelinkrating'] = '';
    $options['afterlinkrating'] = '';
    $options['linksubmitternamelabel'] = __('Submitter Name', 'link-library');
    $options['showlinksubmittername'] = false;
    $options['linksubmitteremaillabel'] = __('Submitter E-mail', 'link-library');
    $options['showaddlinksubmitteremail'] = false;
    $options['linksubmittercommentlabel'] = __('Submitter Comment', 'link-library');
    $options['showlinksubmittercomment'] = false;
    $options['addlinkcatlistoverride'] = '';
    $options['showlargedescription'] = false;
    $options['beforelargedescription'] = '';
    $options['afterlargedescription'] = '';
    $options['showcustomcaptcha'] = false;
    $options['customcaptchaquestion'] = __('Is boiling water hot or cold?', 'link-library');
    $options['customcaptchaanswer'] = __('hot','link-library');
    $options['rssfeedaddress'] = '';
    $options['addlinknoaddress'] = false;
    $options['featuredfirst'] = false;
    $options['showlinksonclick'] = false;
    $options['linklargedesclabel'] = __('Large Description', 'link-library');
    $options['showuserlargedescription'] = false;
    $options['usetextareaforusersubmitnotes'] = false;
    $options['showcatonsearchresults'] = false;
    $options['shownameifnoimage'] = false;
    $options['searchresultsaddress'] = '';
    $options['enable_link_popup'] = false;
    $options['link_popup_text'] = __( '%link_image%<br />Click through to visit %link_name%.', 'link-library');
    $options['popup_width'] = 300;
    $options['popup_height'] = 400;
    $options['nocatonstartup'] = false;
    $options['linktitlecontent'] = 'linkname';
	$options['singlelinkid'] = '';

    if ( 'return_and_set' == $setoptions ) {
        $settingsname = 'LinkLibraryPP' . $settings;
        update_option($settingsname, $options);
    }

    return $options;
}

// Function used to set general initial settings or reset them on user request
function ll_reset_gen_settings( $setoptions = 'return' ) {
    $genoptions['numberstylesets'] = 1;
    $genoptions['includescriptcss'] = '';
    $genoptions['debugmode'] = false;
    $genoptions['schemaversion'] = '5.0';
    $genoptions['pagetitleprefix'] = '';
    $genoptions['pagetitlesuffix'] = '';
    $genoptions['thumbshotscid'] = '';
    $genoptions['emaillinksubmitter'] = false;
    $genoptions['suppressemailfooter'] = false;
    $genoptions['moderatorname'] = '';
    $genoptions['moderatoremail'] = '';
    $genoptions['approvalemailtitle'] = '';
    $genoptions['approvalemailbody'] = '';
    $genoptions['rejectedemailtitle'] = '';
    $genoptions['rejectedemailbody'] = '';
    $genoptions['moderationnotificationtitle'] = '';
    $genoptions['linksubmissionthankyouurl'] = '';
    $genoptions['usefirstpartsubmittername'] = '';
    $genoptions['recipcheckaddress'] = get_bloginfo('wpurl');
    $genoptions['recipcheckdelete403'] = false;
    $genoptions['imagefilepath'] = 'absolute';
    $genoptions['catselectmethod'] = 'multiselectlist';
    $genoptions['hidedonation'] = false;
	$genoptions['updatechannel'] = 'standard';

    $stylesheetlocation = plugins_url( 'stylesheettemplate.css' , __FILE__ );
    $genoptions['fullstylesheet'] = @file_get_contents($stylesheetlocation);

    if ( 'return_and_set' == $setoptions ) {
        update_option('LinkLibraryGeneral', $genoptions);
    }
    return $genoptions;
}