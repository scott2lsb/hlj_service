<?php
/**
 * Created by PhpStorm.
 * User: PegionAndLion
 * Date: 14/12/26
 * Time: 上午10:25
 */
// 用以截取短信字符的函数
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

require_once __DIR__ . '/libs/KdtApiClient.php';
require_once('saeDao.php');
require_once('mail.php');
$appId = '15db6704596966a91b';
$appSecret = '7e6254589d51c55c4b52f7578806e82c';
$client = new KdtApiClient($appId, $appSecret);
$method = 'kdt.logistics.online.confirm';
$dc = explode(",",$_REQUEST['out_stype']);
$params = array(
    "tid" => $_REQUEST['tid'],
    "is_no_express" => 0,
    "out_stype" => $dc[0],
    "out_sid" => $_REQUEST['out_sid']
);
$a = $client->post($method,$params);
$arr = array("state" => $a['response']['shipping']['is_success']);
if($a['response']['shipping']['is_success']) {
    $params = array("state" => "WAIT_BUYER_CONFIRM_GOODS");
    $where = array("tid" => $_REQUEST['tid']);
    update($params,$where,"auction");
    $param2 = array("deliverycompany" => $dc[1], "deliverycode" => $_REQUEST['out_sid'] );
    update($param2,$where,"logistic");

    // 邮件
    $mmc = memcache_init();
    if ($mmc == false) {
        echo "mc init failed\n";
    }
    $info_pack = memcache_get($mmc, $_REQUEST['tid']."_info");
    $arr['realName'] = $info_pack['receiver_name'];
    $arr['mobile'] =  $info_pack['receiver_mobile'];
    $out = get(array("tid" => $_REQUEST['tid']),"auction");
    $arr['item'] =  utf_substr($out[0]['item_info'],16);
    $wechat = $out[0]['wechat_id'];
    if($wechat) {
        $user = get(array("wechat_id"=>$wechat),"user_info");
        $mail = $user[0]['email'];
        $addressString = $info_pack['receiver_state'].' '.
            $info_pack['receiver_address'].' 邮编：'. $info_pack['receiver_zip'];
        $itemName = utf_substr($out[0]['item_info'],16);
        $mailcontent = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title></title>
</head>
<body>

<p>亲爱的{$info_pack['receiver_name']}：</p>
<p>你购买的商品，红领巾已发货。欢迎进入【红领巾】订阅号查询快递跟踪信息。</p>
<br />
<pre>订单信息
商品信息：{$itemName}，{$info_pack['num']}件
收货信息：{$info_pack['receiver_name']}，{$info_pack['receiver_mobile']}，{$addressString}</pre>
<br />
<pre>如何查物流？
STEP1：在微信中添加朋友“hlj_service”，关注【红领巾】订阅号。
STEP2：在【红领巾】订阅号中，输入“我的订单”。
STEP3：在“查物流”列表中，查看物流信息。</pre>

<img src='http://testhlj.qiniudn.com/hlj_weixin_logo.jpg' alt='logo'/>
</body>
</html>";

        send_mail_lazypeople($mail,
            "红领巾通知",
            $mailcontent);
    }

}
$result = json_encode($arr);
$callback=$_GET['callback'];
echo $callback."($result)";


?>