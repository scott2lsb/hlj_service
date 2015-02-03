<?php
require_once('../saeDao.php');
session_start();
$where_merge = array("wechat_id"=>$_SESSION['uwid'],"state"=>"WAIT_BUYER_PAY");
$out_merge = get($where_merge,"auction");
$where2 = array("wechat_id"=>$_SESSION['uwid'],"state"=>"ADMIN_REQUESTED");
$out2 = get($where2,"auction");
if($out_merge) {
    if($out2) {
        $out2 = array_merge($out_merge,$out2);
    }else {
        $out2 = $out_merge;
    }

}
$count2 = count($out2);
$mmc = memcache_init();
if ($mmc == false) {
    echo "mc init failed\n";
}
function utf_substr($str, $len)
{
    for ($i = 0; $i < $len; $i++) {
        $temp_str = substr($str, 0, 1);
        if (ord($temp_str) > 127) {
            $i++;
            if ($i < $len) {
                $new_str[] = substr($str, 0, 3);
                $str = substr($str, 3);
            }
        } else {
            $new_str[] = substr($str, 0, 1);
            $str = substr($str, 1);
        }
    }
    return join($new_str, '');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>我的红领巾</title>
    <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport">
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.css"/>
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/another.css"/>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery-2.0.3.min.js"></script>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.js"></script>
    <style>
        .mani-pay {
            font-size: 0.8em;
            top: 5px;
            max-height: 20px!important;
        }
        .ui-block-mani {
            max-width: 25%;
        }
        .ui-li-count {
            background-color: red;
            border: none;
            border-radius: 0.5em;
            width: 14px;
            height: 14px;
            font-size: 0.2em !important;
            right: 2px;
            top: 15px;
            z-index: 1000;
        }
        #mani {
            z-index: 10 !important;
        }
        .ui-block-title {
            font-size: 0.8em;
            right: 0;
            width: 50% !important;
            padding: 5px;
            padding-left: 9px;
        }
        .ui-block-img {
            max-width: 25%;
        }
        .ui-grid-b {
            margin-top: 10px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            padding-top: 5px;
            border-bottom: 1px solid #a5a5a5;
        }
        #sellerFloatBottom {
            visibility: hidden;
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0px;
            left: 0px;
            display: block;
            z-index: 999;
            background-color: rgb(0, 0, 0);
            opacity: 0.65;
            border: 1px solid;
        }
        #warning {
            color: red;
            font-size: 0.9em;
            padding-left: 10px;
            padding-right: 10px;
        }
    </style>
</head>
<body>
<div id="sellerFloatBottom">

</div>
<div data-role="page" id="testpage">
    <div data-role="header" data-position="fixed">
        <div data-role="navbar">
            <ul data-role="listview" data-count-theme="b" id="status">
                <li></li>
                <li><span class="ui-li-count"><?php echo $count2 ?></span></li>
                <li></li>
                <li></li>
            </ul>
            <ul id="mani">
                <li><a href="./buyer_wait_request.php?uwid=<?php echo $_SESSION['uwid'] ?>" data-ajax='false'>待反馈</a></li>
                <li><a href="#" class="ui-btn-active" data-ajax='false'>待付款</a></li>
                <li><a href="./buyer_wait_deliver.php" data-ajax='false'>待发货</a></li>
                <li><a href="./buyer_get_deliver.php" data-ajax='false'>已发货</a></li>
            </ul>
            <ul>
                <li id="warning">近期微信支付不稳定，如果不能成功使用“微信支付”，建议选择“其他支付方式-信用卡/储蓄卡”进行付款。</li>
            </ul>
        </div>
    </div>
    <!-- /header -->
    <div class="ui-content" role="main">
        <div id="item-wrapper">
            <?php
            $mmc = memcache_init();
            if ($mmc == false) {
                echo "mc init failed\n";
            }
            for($i= $count2-1; $i >= 0; $i--) {
                $item_pack = memcache_get($mmc, $out2[$i]['num_iid']."_info");
                ?>
                <div class="ui-grid-b">
                    <div class="ui-block-a ui-block-img">
                        <a href='<?php echo $item_pack['detail_url'] ?>'>
                            <img
                                src='<?php echo $item_pack['pic_thumb_url'] ?>'
                                style="width:80px;max-height: 80px;">
                        </a>
                    </div>
                    <div class="ui-block-b ui-block-title">
                        <p><?php echo utf_substr($out2[$i]['item_info'],90)?></p>
                    </div>
                    <div class="ui-block-c ui-block-mani">
                        <a href="<?php echo $item_pack['detail_url'] ?>" class="ui-btn ui-corner-all mani-pay">付款</a>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>