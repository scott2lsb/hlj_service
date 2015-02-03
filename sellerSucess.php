<?php
require_once('saeDao.php');
require_once 'blog.php';
session_start();
$mmc = memcache_init();
$picURLArray = memcache_get($mmc, $_SESSION['uwid']."_pic");
$picCount = count($picURLArray);
require_once __DIR__ . '/libs/KdtApiClient.php';

?>

<?php

$skuinfo = '';
$skuprice = '';
$priceArray = array();
$skuouter = '';
$skuKucun = '';
foreach ($_POST as $key => $value)
{
    if(strpos($key, "sku")) {
        $skuinfo.="规格:".$value.",";
    }
    if(strpos($key, "type_price")) {
        $skuprice.=$value.",";
        array_push($priceArray,(float)$value);
    }
}
$skuCount = count($priceArray);

for($i=0; $i<$skuCount; $i++) {
    $skuouter.=",";
    $skuKucun.="999".',';
}
$skuinfo = rtrim($skuinfo, ",");
$skuprice = rtrim($skuprice, ",");
$skuKucun = rtrim($skuKucun,",");
$skuouter = substr($skuouter,0,strlen($skuouter)-1);

// 生成价格区间
$minPrice = sprintf("%.2f", min($priceArray));
$maxPrice = sprintf("%.2f", max($priceArray));
if($minPrice != $maxPrice) {
    $priceArea = $minPrice.'~'.$maxPrice;
}else {
    $priceArea = $minPrice;
}


if($_SESSION['ok'] == 0) {
    $title = $_POST['q_title'];
    $country = $_POST['q_country'];
    $cat = $_POST['q_cat'];
    $money = $priceArea;
    $postMoney = 0;
    $datetime = date('Y-m-d H:i:s', time());
    $deliver_method = $_POST['q_deliver_method'];

// 更新博客
    $para = array("uwid" => $_SESSION['uwid'], "title" => $title, "price" => $money, "post_fee" => $postMoney, "country" => $country,
        "category" => $cat, "pic_one" => $picURLArray[0], "pubtime" => $datetime,
        "deliverymethod" => $deliver_method,"sku_info" => $skuinfo . ' ' . $skuprice);
    $lastId = insert($para, "pub");
    $cat_link = array("其他" => "http://hljbloginfo.duapp.com/wordpress/?category_name=others", "日本" => "http://hljbloginfo.duapp.com/wordpress/?category_name=jap",
                      "法国" => "http://hljbloginfo.duapp.com/wordpress/?category_name=fra", "澳大利亚" => "http://hljbloginfo.duapp.com/wordpress/?category_name=aus",
                      "美国" => "http://hljbloginfo.duapp.com/wordpress/?category_name=am", "英国" => "http://hljbloginfo.duapp.com/wordpress/?category_name=eng",
                      "香港" => "http://hljbloginfo.duapp.com/wordpress/?category_name=hk", "德国" => "http://hljbloginfo.duapp.com/wordpress/?category_name=ger");
    $cat_con = "<div><span class='cat_name'>采购地区：</span><span id='cat_area'><a href='$cat_link[$country]'>$country</a></span><span class='cat_name'>   品类：</span><span id='cat_name'><a href='http://hljbloginfo.duapp.com/wordpress/?tag=$cat'>$cat</a></span></div><br/>";
    $pic_des = "";
    $outerLogo = 'http://bcs.duapp.com/honglongjin-service/hlj_weixin_logo 2015.jpg';
    for ($i = 0; $i < $picCount; $i++) {
        $pic_des .= "<div class='picdiv'><img src='{$picURLArray[$i]}' alt='商品图片' class='itemPic'/></div>";
        $des = $_POST['q_des' . ($i + 1)];
        $pic_des .= "<div class='picdes'><p>$des</p></div><br />";
        if($i == $picCount -1) {
            $pic_des .= "<div class='picdes'><p><b style='color: red;'>近期微信支付不稳定，如果不能成功使用“微信支付”，建议选择“其他支付方式-信用卡/储蓄卡”进行付款。</b><br><b>代购说明：</b><br />1、代购商品若非运输破损导致商品无法正常使用，不提供退换。<br />2、代购到货时间通常为付款后2-4周，不排除物流公司原因造成的延迟到货情况，请亲们理解哦！</p></div>";
            $pic_des .= "<div class='picdiv'><img src='{$outerLogo}' alt='商品图片' class='itemPic'/></div>";
            $pic_des .= "<div class='picdes'><p></p></div><br />";
        }
    }
    $catString = '';
    switch($country) {
        case "日本":
            $catString.= '8134620,';
            break;
        case "香港":
            $catString.= '8134730,';
            break;
        case "德国":
            $catString.= '8134741,';
            break;
        case "法国":
            $catString.= '8134807,';
            break;
        case "美国":
            $catString.= '8134809,';
            break;
        case "其他":
            $catString.= '8134852,';
            break;
    }
    if($cat == "美容护肤") {
        $catString.= '8134105';
    }
    if($cat == "母婴用品") {
        $catString.= '8134158';
    }
    if($cat == "数码产品") {
        $catString.= '8134201';
    }
    if($cat == "服装鞋包") {
        $catString.= '8134223';
    }
    if($cat == "其他") {
        $catString.= '8134245';
    }


    echo $catString;

    // 插入有赞商品
    $appId = '15db6704596966a91b';
    $appSecret = '7e6254589d51c55c4b52f7578806e82c';
    $client = new KdtApiClient($appId, $appSecret);
    $method = 'kdt.item.add';
    $params = array(
        'price' => min($priceArray),
        'title' => $title,
        'desc' => $pic_des,
        'is_virtual' => 0,
        'post_fee' => 0,
        'sku_properties' => $skuinfo,
        'sku_quantities' => $skuKucun,
        'sku_prices' => $skuprice,
        'sku_outer_ids' => $skuouter,
        'tag_ids' => $catString,
    );
    $files = array();
    for ($i = 0; $i < $picCount; $i++) {
        array_push($files,array(
            'url' => str_replace(' ','%20',$picURLArray[$i]),
            'field' => 'images[]',
        ));
    }

    $out = $client->post($method, $params, $files);
    var_dump($out);
    $num_iid = $out["response"]["item"]["num_iid"];
    $buyAddress = $out["response"]["item"]["detail_url"];
    // 更新博客
    $more = "<!--more-->";
    $price = "<div id='up_button_area' class='clearfix'><div id='price_area'><span id='price'>价格：￥<span id='priceNum'>{$money}</span></span><span id='post_price'>邮费：￥<span id='postNum'>{$_POST['q_post']}</span></span><span id='method'>{$deliver_method}</span></div><div id='price_button'><a href='{$buyAddress}' id='buy'>我要买</a></div></div>";
    $content = $cat_con . '' . $pic_des . '' . $more . '' . $price;
    $blogid = send_to_other("http://hljbloginfo.duapp.com/wordpress/xmlrpc.php", $title, $content, "hlj_service", "fanganhuan13579", $cat, $country);
    // 防止重复提交
    $_SESSION['ok'] += 1;
}




$paraBlog = array("num_iid" => $num_iid, "blog_url" => "http://hljbloginfo.duapp.com/wordpress/?p=$blogid",
    "buyAddress" => $buyAddress, "shon_off" => 1);
$where = array(id => $lastId);
update($paraBlog, $where, "pub");




?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>红领巾轻发布</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.css"/>
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/cust.css"/>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery-2.0.3.min.js"></script>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.js"></script>
</head>
<body>
<div data-role="page">
    <div data-role="header" data-position="fixed">
        <h1>发布成功！</h1>
    </div>
    <div class="ui-body ui-body-a content-it">
        <p>你已经成功在红领巾上发布一个商品。完成以下操作，即可轻松推广你的宝贝：</p>
        <p><span style="background-color: black;color: white;padding: 1px 1px;">Step1</span></p>
        <p>点击页面最下方<span style="color: red;">查看我的发布</span>，打开你的商品页面。</p>
        <p><span style="background-color: black;color: white;padding: 1px 1px;">Step2 </span></p>
        <p>打开后，点击商品页面右上角的<img src="http://bcs.duapp.com/caolixiang33/share_button.jpg" alt="" id="share_pic" style="max-width: 5%;"/>标记，将商品发送给朋友、分享到朋友圈，也可以收藏在自己的微信里。</p>
        <img src="http://bcs.duapp.com/caolixiang33/share_small.jpg" alt="" id="share_pic" style="max-width: 100%;"/><br>
        <br>
        <p><?php echo "<a href='http://hljbloginfo.duapp.com/wordpress/?p=$blogid'>查看我的发布</a>" ?></p>
    </div>
</div>
</body>
</html>