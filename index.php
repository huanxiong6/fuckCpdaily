<?php
include_once('loginModular.php');//加载登录函数
include_once('basicFunction.php');//加载基本函数
//include_once('signFormModular.php');//加载表格模块
include_once('signModular.php');//加载签到模块


$userId = $_GET['userId'];
$_POST['config'] = json_decode(file_get_contents("./config/".$userId.".json"),true);
$_POST['config']['fillForm']['optionKeyword'] = explode(",",$_POST['config']['fillForm']['optionKeyword']);
$_POST['deviceId'] = _creatUuid();
$_POST['itude'] = _randItude();


if($_POST['config']['system']['delay'] != '' && $_POST['config']['system']['delay'] != 0){
    sleep((int)$_POST['config']['system']['delay']);
}
if ($_POST['config']['system']['userId'] != '' && $_POST['config']['system']['passWord'] != ''){
    $_POST['cookie'] = IapSchoolLogin($_POST['config']['system']['userId'],$_POST['config']['system']['passWord']);
    if($_POST['cookie'] != '登陆失败'){

            echo $_POST['signInfor'] = _sign();
            $_POST['userName'] = _getUserName();
            echo '<br>';
            echo $mailInfor = _mail();
            echo '<br>';
            echo $pushInfor = _pushplus();


    }else{
        echo '账号密码错误或不是太原理工大学学号+密码。<br>要么就是你尝试次数太多了';
    }
}else{
    echo '账号或密码为空！';
}


