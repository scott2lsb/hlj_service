<?php
session_start();
$_SESSION['we_chat']=$_POST['we_chat'];
$_SESSION['mobile']=$_POST['mobile'];
$_SESSION['email']=$_POST['email'];





?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>红领巾小助手</title>
    <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport">
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.css"/>
    <link rel="stylesheet" href="http://7u2les.com1.z0.glb.clouddn.com/cust.css"/>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery-2.0.3.min.js"></script>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery.mobile.min.js"></script>
    <script src="http://7u2les.com1.z0.glb.clouddn.com/jquery.validate.min.js"></script>
</head>
<body>
<div data-role="page" id="home">
    <div data-role="header" data-position="fixed">
        <h1>收货信息</h1>
    </div>
    <div class="ui-body ui-body-a content-it">
        <?php
            if($_SESSION['preDefault']  == "True" ) {
                ?>
                <div data-role="collapsible" data-collapsed="false">
                    <h1>默认地址</h1>
                    <p><?php echo $_SESSION['preAddress'] ?> 邮编：<?php echo $_SESSION['preZipcode']?></p>
                    <p><?php echo $_SESSION['preReceiver'] ?></p>
                    <p><?php echo $_SESSION['prePhone'] ?></p>
                    <a href="reg4.php?default=1" data-icon="arrow-r" data-role="button">使用默认地址</a>
                </div>
        <?php
            }
        ?>

            <form method="post" action="reg4.php">
            <ul data-role="listview">
                <li>
                    <label for="receiver">收货人姓名</label>
                    <input type="text" name="receiver" id="receiver" value="" data-clear-btn="true"> 

                    <label for="phone">收货人电话</label>
                    <input type="text" name="phone" id="phone" value="" data-clear-btn="true">

                    <label for="address">收货地址</label>
                    <textarea name="address" id="address"data-clear-btn="true"></textarea>

                    <label for="zipcode">邮政编码</label>
                    <input type="text" name="zipcode" id="zipcode" value="" data-clear-btn="true"> 
                </li>
                <li>
                    <input type="submit" value="下一步" data-transition="slideup"  data-theme="b">
                </li>
            </ul>
        </form>
    </div>
    <div data-role="footer" data-position="fixed">
        <h4>红领巾海外代购</h4>
    </div>
</div>
</body>
</html>