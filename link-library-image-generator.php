<?php

function link_library_ajax_image_generator ( $my_link_library_plugin_admin ) {

    check_ajax_referer( 'link_library_generate_image' );

    $name = $_POST['name'];
    $url = $_POST['url'];
    $mode = $_POST['mode'];
    $cid = $_POST['cid'];
    $filepath = $_POST['filepath'];
    $filepathtype = $_POST['filepathtype'];
    $linkid = intval($_POST['linkid']);

    echo $my_link_library_plugin_admin->ll_get_link_image($url, $name, $mode, $linkid, $cid, $filepath, $filepathtype );
    exit;
}