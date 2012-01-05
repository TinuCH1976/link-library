<?php

require_once('../../../wp-load.php');
require_once('link-library.php');

check_admin_referer('LL_ADDLINK_FORM');

$llpluginpath = WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)).'/';
load_plugin_textdomain( 'link-library', $llpluginpath . '/languages', 'link-library/languages');

global $wpdb;

$settings = $_POST['settingsid'];
$settingsname = 'LinkLibraryPP' . $settings;
$options = get_option($settingsname);

$genoptions = get_option('LinkLibraryGeneral');

$valid = false;
$message = "";

if ($_POST['link_name'])
{		
    if ($options['showcaptcha'])
    {
        if (empty($_REQUEST['confirm_code']))
        {
            $valid = false;
            $message = 1;
        }
        else
        {
            if ( isset($_COOKIE['Captcha']) )
            {
                list($Hash, $Time) = explode('.', $_COOKIE['Captcha']);
                if ( md5("ORHFUKELFPTUEODKFJ".$_REQUEST['confirm_code'].$_SERVER['REMOTE_ADDR'].$Time) != $Hash )
                {
                        $valid = false;
                        $message = 2;
                }
                elseif( (time() - 5*60) > $Time)
                {
                        $valid = false;
                        $message = 3;
                }
                else
                {
                        $valid = true;					
                }
            }
            else
            {
                    $valid = false;
                    $message = 4;
            }
        }
    }

    if ($options['showcustomcaptcha'])
    {
        if ($_POST['ll_customcaptchaanswer'] == "")
        {
            $valid = false;
            $message = 5;
        }
        else
        {
            if (strtolower($_POST['ll_customcaptchaanswer']) == strtolower($options['customcaptchaanswer']))
                    $valid = true;
            else
            {
                    $valid = false;
                    $message = 6;
            }
        }
    }

    $captureddata = array();

    
    if ($valid || ($options['showcaptcha'] == false && $options['showcustomcaptcha'] == false))
    {			
        $existinglinkquery = "SELECT * from " . $my_link_library_plugin->db_prefix() . "links l where l.link_name = '" . $_POST['link_name'] . "' ";

        if ( ( $options['addlinknoaddress'] == false ) || ( $options['addlinknoaddress'] == true && $_POST['link_url'] != "" ) )
            $existinglinkquery .= " and l.link_url = 'http://" . $_POST['link_url'] . "'";

        $existinglink = $wpdb->get_var($existinglinkquery);

        if ($existinglink == "" && (($options['addlinknoaddress'] == false && $_POST['link_url'] != "" ) || $options['addlinknoaddress'] == true))
        {
            if ($_POST['link_category'] == 'new' && $_POST['link_user_category'] != '')
            {
                $existingcatquery = "SELECT t.term_id FROM " . $my_link_library_plugin->db_prefix() . "terms t, " . $my_link_library_plugin->db_prefix() . "term_taxonomy tt ";
                $existingcatquery .= "WHERE t.name = '" . $_POST['link_user_category'] . "' AND t.term_id = tt.term_id AND tt.taxonomy = 'link_category'";
                $existingcat = $wpdb->get_var($existingcatquery);

                if (!$existingcat)
                {
                    $newlinkcatdata = array("cat_name" => $_POST['link_user_category'], "category_description" => "", "category_nicename" => $wpdb->escape($_POST['link_user_category']));
                    $newlinkcat = wp_insert_category($newlinkcatdata);
                    $newcatarray = array("term_id" => $newlinkcat);
                    $newcattype = array("taxonomy" => 'link_category');
                    $wpdb->update( $my_link_library_plugin->db_prefix().'term_taxonomy', $newcattype, $newcatarray);
                    $newlinkcat = array($newlinkcat);
                }
                else
                {
                    $newlinkcat = array($existingcat);
                }

                

                $validcat = true;
            }
            elseif ($_POST['link_category'] == 'new' && $_POST['link_user_category'] == '')
            {
                $message = 7;
                $validcat = false;
            }
            else
            {
                $newlinkcat = array($_POST['link_category']);

                $message = 8;

                $validcat = true;
            }

            if ($validcat == true)
            {
                if ($options['showuserlinks'] == false)
                {
                        $newlinkdesc = "(LinkLibrary:AwaitingModeration:RemoveTextToApprove)" . $_POST['link_description'];
                        $newlinkvisibility = 'N';
                }
                else
                {
                        $newlinkdesc = $_POST['link_description'];
                        $newlinkvisibility = 'Y';
                }

                if ($options['storelinksubmitter'] == true)
                {
                        global $current_user;

                        get_currentuserinfo();

                        if ($current_user)
                                $username = $current_user->user_login;
                }

                $newlink = array("link_name" => esc_html(stripslashes($_POST['link_name'])), "link_url" => esc_html(stripslashes($_POST['link_url'])), "link_rss" => esc_html(stripslashes($_POST['link_rss'])),
                        "link_description" => esc_html(stripslashes($newlinkdesc)), "link_notes" => esc_html(stripslashes($_POST['link_notes'])), "link_category" => $newlinkcat, "link_visible" => $newlinkvisibility);
                $newlinkid = $my_link_library_plugin->link_library_insert_link($newlink, false, $options['addlinknoaddress']);

                $extradatatable = $my_link_library_plugin->db_prefix() . "links_extrainfo";
                $wpdb->update( $extradatatable, array( 'link_second_url' => $_POST['ll_secondwebaddr'], 'link_telephone' => $_POST['ll_telephone'], 'link_email' => $_POST['ll_email'], 'link_reciprocal' => $_POST['ll_reciprocal'], 'link_submitter' => $username, 'link_submitter_name' => $_POST['ll_submittername'], 'link_submitter_email' => $_POST['ll_submitteremail'], 'link_textfield' => $_POST['link_textfield']), array( 'link_id' => $newlinkid ));		

                if ($options['emailnewlink'])
                {
                        if ($genoptions['moderatoremail'] != '')
                                $adminmail = $genoptions['moderatoremail'];
                        else
                                $adminmail = $get_option['admin_email'];
                        $headers = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                        $emailmessage = __('A user submitted a new link to your Wordpress Link database.', 'link-library') . "<br /><br />";
                        $emailmessage .= __('Link Name', 'link-library') . ": " . esc_html(stripslashes($_POST['link_name'])) . "<br />";
                        $emailmessage .= __('Link Address', 'link-library') . ": " . esc_html(stripslashes($_POST['link_url'])) . "<br />";
                        $emailmessage .= __('Link RSS', 'link-library') . ": " . esc_html(stripslashes($_POST['link_rss'])) . "<br />";
                        $emailmessage .= __('Link Description', 'link-library') . ": " . esc_html(stripslashes($_POST['link_description'])) . "<br />";
                        $emailmessage .= __('Link Large Description', 'link-library') . ": " . esc_html(stripslashes($_POST['link_textfield'])) . "<br />";
                        $emailmessage .= __('Link Notes', 'link-library') . ": " . esc_html(stripslashes($_POST['link_notes'])) . "<br />";
                        $emailmessage .= __('Link Category', 'link-library') . ": " . $_POST['link_category'] . "<br /><br />";
                        $emailmessage .= __('Reciprocal Link', 'link-library') . ": " . $_POST['ll_reciprocal'] . "<br /><br />";
                        $emailmessage .= __('Link Secondary Address', 'link-library') . ": " . $_POST['ll_secondwebaddr'] . "<br /><br />";
                        $emailmessage .= __('Link Telephone', 'link-library') . ": " . $_POST['ll_telephone'] . "<br /><br />";
                        $emailmessage .= __('Link E-mail', 'link-library') . ": " . $_POST['ll_email'] . "<br /><br />";
                        $emailmessage .= __('Link Submitter', 'link-library') . ": " . $username . "<br /><br />";
                        $emailmessage .= __('Link Submitter Name', 'link-library') . ": " . $_POST['ll_submittername'] . "<br /><br />";
                        $emailmessage .= __('Link Submitter E-mail', 'link-library') . ": " . $_POST['ll_submitteremail'] . "<br /><br />";
                        $emailmessage .= __('Link Comment', 'link-library') . ": " . $_POST['ll_submittercomment'] . "<br /><br />";

                        if ($options['showuserlinks'] == false)
                                $emailmessage .= "<a href='" . WP_ADMIN_URL . "/link-manager.php?s=LinkLibrary%3AAwaitingModeration%3ARemoveTextToApprove'>Moderate new links</a>";
                        elseif ($options['showuserlinks'] == true)
                                $emailmessage .= "<a href='" . WP_ADMIN_URL . "/link-manager.php'>View links</a>";

                        $emailmessage .= "<br /><br />" . __('Message generated by', 'link-library') . " <a href='http://yannickcorner.nayanna.biz/wordpress-plugins/link-library/'>Link Library</a> for Wordpress";

                        if ($emailtitle == '')
                        {
                                $emailtitle = stripslashes($genoptions['moderationnotificationtitle']);
                                $emailtitle = str_replace('%linkname%', esc_html(stripslashes($_POST['link_name'])), $emailtitle);
                        }
                        else
                        {
                                $emailtitle = htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . " - " . __('New link added', 'link-library') . ": " . htmlspecialchars($_POST['link_name']);
                        }

                        wp_mail($adminmail, $emailtitle, $emailmessage, $headers);
                }
            }	
        }
        elseif ($existinglink == "" && ($options['addlinknoaddress'] == false && $_POST['link_url'] == "" ))
        {
            $message = 9;
        }
        else
        {
            $message = 10;
        }
    }
}


if ($_POST['thankyouurl'] != '')
    $redirectaddress = $_POST['thankyouurl'];
else
{
    $redirectaddress = $my_link_library_plugin->remove_querystring_var($_POST['_wp_http_referer'], 'addlinkmessage');
    $redirectaddress = $my_link_library_plugin->remove_querystring_var($redirectaddress, 'addlinkname');
    $redirectaddress = $my_link_library_plugin->remove_querystring_var($redirectaddress, 'addlinkurl');
}

if (strpos($redirectaddress, '?') == false)
    $redirectaddress .= '?addlinkmessage=' . $message;
else
    $redirectaddress .= '&addlinkmessage=' . $message;

if ($valid == false && ($options['showcaptcha'] == true || $options['showcustomcaptcha'] == true))
{
    if ($_POST['link_name'] != '')
        $redirectaddress .= "&addlinkname=" . rawurlencode($_POST['link_name']);
    
    if ($_POST['link_url'] != '')
        $redirectaddress .= "&addlinkurl=" . rawurlencode($_POST['link_url']);
    
    if ($_POST['link_category'] != '')
        $redirectaddress .= "&addlinkcat=" . rawurlencode($_POST['link_category']);
    
    if ($_POST['link_user_category'] != '')
        $redirectaddress .= "&addlinkusercat=" . rawurlencode($_POST['link_user_category']);
    
    if ($_POST['link_description'] != '')
        $redirectaddress .= "&addlinkdesc=" . rawurlencode($_POST['link_description']);
    
    if ($_POST['link_textfield'] != '')
        $redirectaddress .= "&addlinktextfield=" . rawurlencode($_POST['link_textfield']);
    
    if ($_POST['link_rss'] != '')
        $redirectaddress .= "&addlinkrss=" . rawurlencode($_POST['link_rss']);
    
    if ($_POST['link_notes'] != '')
        $redirectaddress .= "&addlinknotes=" . rawurlencode($_POST['link_notes']);
    
    if ($_POST['ll_secondwebaddr'] != '')
        $redirectaddress .= "&addlinksecondurl=" . rawurlencode($_POST['ll_secondwebaddr']);
    
    if ($_POST['ll_telephone'] != '')
        $redirectaddress .= "&addlinktelephone=" . rawurlencode($_POST['ll_telephone']);
    
     if ($_POST['ll_email'] != '')
        $redirectaddress .= "&addlinkemail=" . rawurlencode($_POST['ll_email']);
     
     if ($_POST['ll_reciprocal'] != '')
        $redirectaddress .= "&addlinkreciprocal=" . rawurlencode($_POST['ll_reciprocal']);
     
     if ($_POST['ll_submittername'] != '')
        $redirectaddress .= "&addlinksubmitname=" . rawurlencode($_POST['ll_submittername']);
     
     if ($_POST['ll_submitteremail'] != '')
        $redirectaddress .= "&addlinksubmitemail=" . rawurlencode($_POST['ll_submitteremail']);
     
      if ($_POST['ll_submittercomment'] != '')
        $redirectaddress .= "&addlinksubmitcomment=" . rawurlencode($_POST['ll_submittercomment']);
      
      if ($_POST['ll_customcaptchaanswer'] != '')
        $redirectaddress .= "&addlinkcustomcaptcha=" . rawurlencode($_POST['ll_customcaptchaanswer']);
}

wp_redirect($redirectaddress);

?>
