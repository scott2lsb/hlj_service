<?php
/**
 * Created by PhpStorm.
 * User: PegionAndLion
 * Date: 15/1/4
 * Time: 下午6:57
 */
// 取得所有上架商品
require_once('../saeDao.php');
$where = array("admin_payed" => 1, "state"=>"WAIT_BUYER_CONFIRM_GOODS");
$out = get($where,"auction");
$count = count($out);
$mmc = memcache_init();
if ($mmc == false) {
    echo "mc init failed\n";
}
function utf_substr($str,$len)
{
    for($i=0;$i<$len;$i++)
    {
        $temp_str=substr($str,0,1);
        if(ord($temp_str) > 127)
        {
            $i++;
            if($i<$len)
            {
                $new_str[]=substr($str,0,3);
                $str=substr($str,3);
            }
        }
        else
        {
            $new_str[]=substr($str,0,1);
            $str=substr($str,1);
        }
    }
    return join($new_str,'');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>红领巾砖姐后台</title>
    <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport">
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.css"/>
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/another.css"/>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery-2.0.3.min.js"></script>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.js"></script>
    <style>
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

        .mani-pay, .mani-detail {
            height: 30px;
            font-size: 0.8em;
            top: 5px;
        }

        .ui-block-img {
            max-width: 25%;
        }

        .ui-block-mani {
            max-width: 25%;
        }
        .ui-grid-b {
            margin-top: 10px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            padding-top: 5px;
            border-bottom: 1px solid #a5a5a5;
        }
        #dFloat {
            position: fixed;
            left: 10px;
            right: 10px;
            background-color: #efeff5;
            height: 500px;
            top: 45px;
            border-radius: 8px;
            text-align: center;
            z-index: 1000;
            visibility: hidden;
        }
        #closeDetail {
            position: absolute;
            left: 2.5%;
            top: 5px;
        }

    </style>
</head>
<body>
<div id="deliverFloatBottom">

</div>
<div data-role="page" id="testpage">
    <div data-role="header" data-position="fixed">
        <div data-role="navbar">
            <ul id="mani">
                <li><a href="./admin_wait_request.php" data-ajax='false'>待分配</a></li>
                <li><a href="./admin_requested.php" data-ajax='false'>已分配</a></li>
                <li><a href="./admin_create_pay.php" data-ajax='false'>待打款</a></li>
                <li><a href="#" class="ui-btn-active" data-ajax='false'>已打款</a></li>
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
            for($i= $count-1; $i >= 0; $i--) {
                if($out[$i]['tid'] != '') {
                    $info_pack = memcache_get($mmc, $out[$i]['tid']."_info");
                    $item_pack = memcache_get($mmc, $info_pack['num_iid']."_info");
                    ?>
                    <div class="ui-grid-b">
                        <div class="ui-block-a ui-block-img">
                            <a href='<?php echo $item_pack['detail_url']."&from=singlemessage&isappinstalled=1" ?>'>
                                <img
                                    src='<?php echo $info_pack['pic_thumb_path'] ?>'
                                    style="width:80px;max-height: 80px;">
                            </a>
                        </div>
                        <div class="ui-block-b ui-block-title"><p><?php echo utf_substr($out[$i]['item_info'],18)."..."?></p>
                            <p><?php echo $info_pack['receiver_name'] ?>,<?php echo $info_pack['receiver_mobile'].",".$info_pack['receiver_state'].$info_pack['receiver_city'].$info_pack['receiver_district']."..."?></p>
                        </div>
                        <div class="ui-block-c ui-block-mani">
                            <button class='mani-pay' id='<?php echo $out[$i]['tid'] ?>'>查物流</button>
                            <button class='mani-detail' id='<?php echo $out[$i]['tid'] ?>'>详情</button>
                        </div>
                    </div>
                <?php
                }
            }
            ?>
        </div>
    </div>
    <div id="dFloat">
        <p class="pay_alert">订单详情</p>
        <a href="#" class="ui-btn ui-icon-delete ui-btn-icon-notext ui-corner-all" id="closeDetail">No text</a>
        <ul data-role="listview" id="detailUl">
            <li><span class="dt">订单编号:</span><span id="dtid"></span></li>
            <li><span class="dt">商品信息:</span><span id="dinfo"></span></li>
            <li><span class="dt">商品总价:</span><span id="dtotalPrice"></span></li>
            <li><span class="dt">备注:</span><span id="dps"></span></li>
            <li><span class="dt">收货信息:</span><span id="daddress"></span></li>
            <li><button data-role="button" id='cancelDetail' data-theme="b">我知道了</button></li>
        </ul>
    </div>
    <script>
        $(document).ready(function(){
            var tid = null;
            $('.mani-pay').click(function(){
                tid = $(this).attr('id');
                $.ajax({
                    url: "../getDC.php",
                    type: "POST",
                    dataType: 'jsonp',
                    jsonp: 'callback',
                    data: 'tid='+ tid,
                    timeout: 5000,
                    success: function (json) {
                        if (json['deliverycompany'] != '') {
                            window.location.href="http://m.kuaidi100.com/index_all.html?type=" + json['deliverycompany'] +
                            "&postid=" + json['deliverycode'] + "&callbackurl=http://wap.koudaitong.com/v2/showcase/homepage?kdt_id=642404";

                        }else {
                            alert("获取失败请重试");
                        }

                    }
                })
            });
        });
    </script>
    <script>
        // For Detail
        $(document).ready(function(){
            var tid = null;
            $('.mani-detail').click(function(){
                $('#dFloat').css('visibility','visible');
                $('#deliverFloatBottom').css('visibility','visible');
                tid = $(this).attr('id');
                $.ajax({
                    url: "./getTDetailAndSeller.php",
                    type: "POST",
                    dataType: 'jsonp',
                    jsonp: 'callback',
                    data: 'tid='+ tid,
                    timeout: 5000,
                    success: function (json) {
                        if (json['tid'] != null) {
                            $("#dtid").text(json['tid']+"   买手:"+json['sellerName']).css('white-space','pre-wrap');
                            $("#dinfo").text("\n" + json['info']).css('white-space','pre-wrap');
                            $("#dtotalPrice").text("\n" + json['price']+ " * " + json['num'] + "(件) + " + json['post_fee'] + "(邮费) =" +
                            json['total_fee']).css('white-space','pre-wrap');
                            $("#dps").text(json['dps']).css('white-space','pre-wrap');
                            $("#daddress").text("\n" + json['receiver_name'] + "," + json['receiver_mobile'] + "\n" +
                            json['receiver_state'] + json['receiver_city'] + json['receiver_district'] + "\n" +
                            json['receiver_address'] + "\n邮编:" + json['receiver_zip']).css('white-space','pre-wrap');
                        }else {
                            alert("获取失败请重试");
                        }

                    }
                })
            });
            $('#cancelDetail').click(function(){
                $('#dFloat').css('visibility','hidden');
                $('#deliverFloatBottom').css('visibility','hidden');
            });
            $('#closeDetail').click(function(){
                $('#dFloat').css('visibility','hidden');
                $('#deliverFloatBottom').css('visibility','hidden');
            });
        });
    </script>
</div>
</body>
</html>