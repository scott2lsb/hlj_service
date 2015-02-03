<?php
session_start();

$_SESSION['uwid'] = $_REQUEST['uwid'];
// 获取图片数组
$mmc = memcache_init();
$picURLArray = memcache_get($mmc, $_SESSION['uwid']."_pic");
$picCount = count($picURLArray);

$_SESSION['ok'] = 0;

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>红领巾海外代购</title>
    <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport">
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.css"/>
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/cust.css"/>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery-2.0.3.min.js"></script>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.js"></script>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function(){
            $(".picdiv").css("text-align","center");
            $(".itemPic").css('max-width','290px');
            $(".itemPic").css('margin-top','5px');
            $(".itemPic").css('margin-bottom','5px');
        });
    </script>
    <script>
        $(document).ready(function(){
            var lastId = 1;
            $('#skuArea').on('click','a.ui-icon-plus',function(event){
                event.preventDefault();
                $insert = $(this).parent().clone();
                // Modify Sku
                var c = $insert.find(".ui-block-b input")[0];
                $(c).attr('name', ( parseInt(lastId) + 1 ) + '_sku').val('');

                // Modify Price
                var e = $insert.find(".ui-block-b input")[1];
                $(e).attr('name', ( parseInt(lastId) + 1 ) + '_q_type_price').val('');

                // add Minus
                var f = $insert.find("a");
                $(f).removeClass("ui-icon-plus").addClass("ui-icon-minus");
                $(this).parent().parent().append($insert);
                lastId += 1;
            });
            $('#skuArea').on('click','a.ui-icon-minus',function(event){
                event.preventDefault();
                $kill = $(this).parent();
                $kill.remove();
                // Modify Sku
            });
            $('#sub').on('click',function(event){
                event.preventDefault();
                var cate = $('#q_cat').val();
                var location = $('#q_country').val();
                var deliverLocation = $('#q_deliver_method').val();
                var title = $('#q_title').val();
                var price = true;
                var sku = true;
                var ti = true;
                if(deliverLocation == 0) {
                    $('#q_deliver_method').prev().css('color','red');
                    $('#q_deliver_method').focus();
                }
                if(location == 0) {
                    $('#q_country').prev().css('color','red');
                    $('#q_country').focus();
                }
                if(cate == 0) {
                    $('#q_cat').prev().css('color','red');
                    $('#q_cat').focus();
                }
                $(".q_type_price").each(function(index,element){
                    if($(element).val() == '' || isNaN(parseInt($(element).val()))) {
                        $(element).parent().parent().parent().find('.ui-block-a span').css('color','red');
                        price = false;
                        $(element).focus();
                    }else {
                        $(element).parent().parent().parent().find('.ui-block-a span').css('color','black');
                    }
                });
                $(".q_type_sku").each(function(index,element){
                    if($(element).val() == '') {
                        $(element).parent().parent().parent().find('.ui-block-a span').css('color','red');
                        sku = false;
                        $(element).focus();
                    }else {
                        $(element).parent().parent().parent().find('.ui-block-a span').css('color','black');
                    }
                });
                if(title == '') {
                    $('#q_title').parent().parent().parent().find('label').css('color','red').focus();
                    ti = false;
                }else {
                    $('#q_title').parent().parent().parent().find('label').css('color','black');
                }
                if(cate != 0 && location != 0 && deliverLocation != 0 && price && sku && ti) {
                    $('#uploadForm').submit();
                }
                $("#sub").parent().removeClass("ui-btn-active");
            });

        });
    </script>
    <style>
        .skus {
            position: relative;
        }
        .ui-grid-b {
            height: 50px;
            width: 100%;
        }
        .ui-block-a {
            line-height: 50px;
            max-width: 15%;
        }
        .ui-block-b {
            line-height: 50px;
            min-width: 70%;
        }
        .ui-block-c {
            line-height: 50px;
            max-width: 15%;
        }

        .skuButton {
            max-width: 40px;
            max-height: 40px;
            color: blue;
            position: absolute;
            top: 30px;
            right: 10px;
        }
        textarea {
            min-height: 80px;
            padding-bottom: 20px;
        }
    </style>
</head>
<body>

<div data-role="page" id="home">
    <div data-role="header" data-position="fixed">
        <h1>发布商品</h1>
    </div>
    <div class="ui-body ui-body-a content-it">
        <form method="post" action="sellerSucess.php" id="uploadForm" class="ui-field-contain">
            <ul data-role="listview">
                <li>
                    <div class="clearfix">
                        <label for="q_title">商品标题</label>
                        <input type="text" name="q_title" id="q_title" value="" placeholder="请完整描述商品信息" data-clear-btn="true"> 
                    </div>
                </li>
                <li id="skuArea">
                    <div class="skus clearfix" id="1_sku">
                        <div class="ui-grid-b">
                            <div class="ui-block-a"><span>规格</span></div>
                            <div class="ui-block-b"><input type="text" name="1_sku" class="q_type_sku" value="" placeholder="不超过15个字"></div>
                        </div>
                        <div class="ui-grid-b">
                            <div class="ui-block-a"><span>价格</span></div>
                            <div class="ui-block-b"><input type="number" name="1_q_type_price" class="q_type_price" value="" placeholder="含邮费的价格"></div>
                        </div>
                        <a href="#" class="ui-btn ui-icon-plus ui-btn-icon-notext ui-corner-all skuButton"></a>
                    </div>
                </li>
                <li>
                    <select name="q_cat" id="q_cat" data-native-menu="true">
                            <option value="0">点击选择商品分类</option>
                            <option value="美容护肤">美容护肤</option>
                            <option value="母婴用品">母婴用品</option>
                        <option value="数码产品">数码产品</option>
                            <option value="服装鞋包">服装鞋包</option>
                            <option value="其他">其他分类</option>
                    </select>
                    <select name="q_country" id="q_country" data-native-menu="true">
                            <option value="0">点击选择采购地区</option>
                            <option value="日本">日本</option>
                            <option value="香港">香港</option>
                            <option value="德国">德国</option>
                            <option value="美国">美国</option>
                            <option value="法国">法国</option>
                            <option value="其他">其他</option>
                    </select>
                    <div class="clearfix">
                        <select name="q_deliver_method" id="q_deliver_method" data-native-menu="true">
                                <option value="0">点击选择发货地区</option>
                                <option value="国外发货">国外发货</option>
                                <option value="国内发货">国内发货</option>
                        </select>
                    </div>
                </li>
                <li>
                    <?php
                    for($i=0; $i<$picCount; $i++) {
                        ?>
                        <div class="picdiv">
                            <img src="<?php echo $picURLArray[$i] ?>" alt="商品图片" class="itemPic"/>
                        </div>
                        <textarea cols="40" rows="8" name="<?php echo 'q_des'.($i+1); ?>" id="<?php echo 'q_des'.($i+1); ?>" value="" placeholder="请填写商品描述(非必填)"></textarea>
                    <?php
                    }
                    ?>
                     
                </li>
            </ul>
            <input type="submit" value="提交" data-transition="slideup"  data-theme="b" id="sub">
        </form>
    </div>
    <div data-role="footer" data-position="fixed">
        <h4>红领巾海外代购</h4>
    </div>
</div>
</body>
</html>