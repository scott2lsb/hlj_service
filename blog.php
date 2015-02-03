<?php

require_once 'IXR_Library.php';
function send_to_other($web, $title, $con, $USER, $PASS,$keyword,$country)
{
    $client = new IXR_Client($web);
    $content['title'] = $title;
    $content['categories'] = array();
    array_push($content['categories'],$country.'');
    $content['description'] = $con;
//  $content['custom_fields'] = array( array('key' => 'my_custom_fied','value'=>'yes') );
    $content['mt_keywords'] = $keyword;

    if (!$client->query('metaWeblog.newPost', '', $USER, $PASS, $content, true)) {
        die('Error while creating a new post' . $client->getErrorCode() . " : " . $client->getErrorMessage());
    }
    $ID = $client->getResponse();

    if ($ID) {
    }
    return $ID;
}

function get_a_blog($web,$id,$USER, $PASS)
{
    $client = new IXR_Client($web);
//    $params = array($id,$USER,$PASS);
    if (!$client->query('metaWeblog.getPost',$id,$USER,$PASS)) {
        echo "fuck";
        die('Error while creating a new post' . $client->getErrorCode() . " : " . $client->getErrorMessage());
    }
    $data = $client->getResponse();
    echo $data;
    var_dump($data);

    if ($data) {

//        var_dump($data);
    }
}

