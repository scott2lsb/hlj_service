<?php
/**
 * Created by PhpStorm.
 * User: PegionAndLion
 * Date: 15/1/4
 * Time: 下午6:57
 */
// 取得所有下架商品
require_once('saeDao.php');
$uwid = $_REQUEST['uwid'];
$where = array("uwid" => $uwid,"shon_off" => 0);
$out = get($where,"pub");

$where2 = array("seller_wechat_id"=>$uwid,"state"=>"WAIT_SELLER_SEND_GOODS");
$out2 = get($where2,"auction");
$waitCount = count($out2);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>我的发布</title>
    <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport">
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.css"/>
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/cust.css"/>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery-2.0.3.min.js"></script>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.js"></script>
    <style>
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

        #status {
            z-index: 1000 !important;
        }

        .ui-block-title {
            font-size: 0.8em;
            right: 0;
            width: 50% !important;
            padding: 5px;
            padding-left: 9px;
        }

        .mani-button {
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
    </style>
</head>
<body>
<div data-role="page" id="manager_on">
    <div data-role="header" data-position="fixed">
        <div data-role="navbar">
            <ul data-role="listview" data-count-theme="b" id="status">
                <li></li>
                <li></li>
                <li><span class="ui-li-count"><?php echo $waitCount ?></span></li>
                <li></li>
            </ul>
            <ul id="mani">
                <li><a href="./seller_manager_off.php?uwid=<?php echo $uwid ?>" data-ajax='false'>在售</a></li>
                <li><a href="#"  class="ui-btn-active">已删除</a></li>
                <li><a href="./seller_manager_deliver.php?uwid=<?php echo $uwid ?>" data-ajax='false'>待发货</a></li>
                <li><a href="./seller_manager_delivered.php?uwid=<?php echo $uwid ?>" data-ajax='false'>已发货</a></li>
            </ul>
        </div>
    </div>
    <!-- /header -->
    <div class="ui-content" role="main">
        <div id="item-wrapper-on">
            <?php
            $mmc = memcache_init();
            if ($mmc == false) {
                echo "mc init failed\n";
            }
            for($i= count($out)-1; $i >= 0; $i--) {
                if($out[$i]['num_iid'] != '') {
                    $item_pack = memcache_get($mmc, $out[$i]['num_iid']."_info");
                    ?>
                    <div class="ui-grid-b">
                        <div class="ui-block-a ui-block-img">
                            <a href='<?php echo $out[$i]['buyAddress'] ?>'>
                                <img
                                    src='<?php echo $item_pack['pic_thumb_url'] ?>'
                                    style="width:80px;max-height: 80px;">
                            </a>
                        </div>
                        <div class="ui-block-b ui-block-title"><p><?php echo $out[$i]['title'] ?></p></div>
                        <div class="ui-block-c ui-block-mani">
                            <button class='mani-button' id='<?php echo $out[$i]['num_iid'] ?>'>恢复</button>
                        </div>
                    </div>
                <?php
                }
            }
            ?>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#item-wrapper-on').on('click', 'button',function (event) {
                var id = $(this).attr('id');
                var that = $(this);
                $.ajax({
                    url: "./get_goods_on.php",
                    type: "POST",
                    dataType: 'jsonp',
                    jsonp: 'callback',
                    data: 'id='+ id,
                    timeout: 5000,
                    success: function (json) {//客户端jquery预先定义好的callback函数,成功获取跨域服务器上的json数据后,会动态执行这个callback函数
                        if (json['state'] === true) {
                            alert("恢复成功");
                            that.parent().parent().fadeOut("slow");
                        }else {
                            alert("恢复失败");
                        }

                    }
                })
            });
        })
    </script>
</div>

</body>
</html>