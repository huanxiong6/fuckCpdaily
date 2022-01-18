<?php
include_once("config.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './src/Exception.php';
require './src/PHPMailer.php';
require './src/SMTP.php';

  
function _postSubmit($remote_server, $post_string,$_cookie) {  
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
    $_header = array ('Content-Type: application/json;','Cookie: '.$_cookie);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);                
    return $data;  
}  

function _postSubmitForm($remote_server, $post_string,$_cookie,$_extension) {  
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
    $_header = array ('Content-Type: application/json; charset=utf-8','Cookie: '.$_cookie,'Cpdaily-Extension:'.$_extension);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);                
    return $data;  
}

function _post($remote_server, $post_string) {  
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
    $_header = array ('Content-Type:application/x-www-form-urlencoded');
    curl_setopt($ch, CURLOPT_HTTPHEADER,$_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);                
    return $data;  
}

//获取姓名2021.9.6
function _getUserName(){
    $_cookie = $_POST['cookie'];
    $_userNameInforJson = _postSubmit('https://tyut.campusphere.net/portal/desktop/userDesktopInfo','',$_cookie);
    $_POST['userName'] = json_decode($_userNameInforJson,true)['datas']['userName'];
    return $_POST['userName'];
}

//生成extension 
function _creatExtension(){
    //$_itude = _randItude();
    $_deviceId = _creatUuid();
    $_itude = $_POST['itude'];
    $_extension = array('appVersion' => '9.0.12', 'systemName' => 'android',  'model' => 'MI 6',
                'lon' => $_itude['longitude'], 'systemVersion' => '8.0.1', 'deviceId' => $_deviceId, 'lat' => $_itude['latitude']);
    $_extension = _DESEncrypt(json_encode($_extension),'b3L26XNL');
    //$_extension = _DESEncrypt(json_encode($_extension),'XCE927==');
    //$_extension = _DESEncrypt(json_encode($_extension),'ST83=@XV');
    return $_extension;
}

function ip()//生成随机ip
{
    $ip_long = array(
        array('607649792', '608174079'), // 36.56.0.0-36.63.255.255
        array('1038614528', '1039007743'), // 61.232.0.0-61.237.255.255
        array('1783627776', '1784676351'), // 106.80.0.0-106.95.255.255
        array('2035023872', '2035154943'), // 121.76.0.0-121.77.255.255
        array('2078801920', '2079064063'), // 123.232.0.0-123.235.255.255
        array('-1950089216', '-1948778497'), // 139.196.0.0-139.215.255.255
        array('-1425539072', '-1425014785'), // 171.8.0.0-171.15.255.255
        array('-1236271104', '-1235419137'), // 182.80.0.0-182.92.255.255
        array('-770113536', '-768606209'), // 210.25.0.0-210.47.255.255
        array('-569376768', '-564133889'), // 222.16.0.0-222.95.255.255
        );
    $rand_key = mt_rand(0, 9);
    return $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
}

function recv_byte($user,$pass,$softid,$imgdata)//打码模块
{
	$http = curl_init();
	curl_setopt($http,CURLOPT_URL,'http://api2.sz789.net:88/RecvByte.ashx');
	curl_setopt($http,CURLOPT_RETURNTRANSFER,1); 
	$postData = 'username='.$user.'&password='.$pass.'&softId='.$softid.'&imgdata='.$imgdata;
	curl_setopt($http,CURLOPT_POSTFIELDS,$postData);
	$data = curl_exec($http);
	curl_close($http);
	return $data;
}

//读取关键词
function _strstr($_str){
    for($i=0;$i<count($_POST['config']['fillForm']['optionKeyword']);$i++){
        if(stristr($_str,$_POST['config']['fillForm']['optionKeyword'][$i])){
            return true;
            break;
        }
    }
}

//生成体温
function _randTemp(){
    $_middleTemp = $_POST['config']['fillForm']['temperature'] * 10;
    $_tempOffset = $_POST['config']['fillForm']['tempOffset'] * 10;
    $_temp = rand($_middleTemp - $_tempOffset,$_middleTemp + $_tempOffset) / 10;
    return $_temp;
}

//生成经纬度
function _randItude(){
    $_middleLongitude = $_POST['config']['location']['longitude'] * 1000000;
    $_middleLatitude = $_POST['config']['location']['latitude'] * 1000000;
    $_itudeOffset = $_POST['config']['location']['itudeOffset'];
    $_longitude = rand($_middleLongitude - $_itudeOffset,$_middleLongitude + $_itudeOffset) / 1000000;
    $_latitude = rand($_middleLatitude - $_itudeOffset,$_middleLatitude + $_itudeOffset) / 1000000;
    $_returnArray = array('latitude'=>$_latitude,'longitude'=>$_longitude);
    return $_returnArray;
}

//DES加密函数
function _DESEncrypt($text, $key = 'b3L26XNL'){
    $iv = "\x01\x02\x03\x04\x05\x06\x07\x08";//初始向量
    $pad = 8 - (strlen($text) % 8);
    $text =$text . str_repeat(chr($pad), $pad);//PKCS5填充
    $res = openssl_encrypt($text, 'DES-CBC', $key, OPENSSL_NO_PADDING, $iv);
    $resBase64 = base64_encode($res);
    $_POST['desResult'] = $resBase64;
    return $resBase64;
}

//AES加密
function _AESEncrypt($text, $key){
    $iv = "\x01\x02\x03\x04\x05\x06\x07\x08\t\x01\x02\x03\x04\x05\x06\x07";//初始向量
    //$text = bin2hex(random_bytes(32)).$text;//加密明文前需要64位随机字符串
    $pad = 16 - (strlen($text) % 16);
    $text = $text . str_repeat(chr($pad), $pad);//PKCS5填充
    $res = openssl_encrypt($text, 'AES-128-CBC', $key, OPENSSL_NO_PADDING, $iv);//加密
    return base64_encode($res);//base64编码
    //return $res;
}

//生成随机数，任务唯一标识符(伪随机)
function _creatUuid($prefix=""){
    $userId = $_POST['config']['system']['userId'];
    $chars = md5($userId."shualeme");
    $uuid = substr ($chars, 0, 16)."XiaomiMi6";
    return $prefix.$uuid;
}

//取中间内容
function strm($str, $left, $right) {
	$start = strpos($str, $left) + strlen($left);
	$len = strpos($str, $right, $start) - $start;
    $str = substr($str,$start,$len);
    return $str;
}

//发送邮件
function _mail(){
    $email = $_POST['config']['push']['email'];
    $userName = $_POST['userName'];
    $signInfor = $_POST['signInfor'];
    if ($email != ''){
    
            $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
             //服务器配置
            $mail->CharSet ="UTF-8";                     //设定邮件编码
            $mail->SMTPDebug = 0;                        // 调试模式输出
            $mail->isSMTP();                             // 使用SMTP
            $mail->Host = $MailConfig['host'];                // SMTP服务器
            $mail->SMTPAuth = true;                      // 允许 SMTP 认证
            $mail->Username = $MailConfig['username'];                // SMTP 用户名  即邮箱的用户名
            $mail->Password = $MailConfig['password'];             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
            $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
            $mail->Port = $MailConfig['port'];                            // 服务器端口 25 或者465 具体要看邮箱服务器支持
            $mail->setFrom($MailConfig['username'], $MailConfig['senderName']);  //发件人
            $mail->addAddress($email, $userName);  // 收件人
            $mail->addReplyTo('DonaldTrump@email.cn', '签到通知服务'); //回复的时候回复给哪个邮箱 建议和发件人一致
            $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
            $mail->Subject = '签到情况推送：'.$signInfor;
            $mail->Body = '<div style="background-color:#f2f2f2;margin:0;padding:20px ;width:100%;font-family:Microsoft YaHei;">
            <div style="width:700px;background-color:#fff;margin:0 auto;">
                <div style="height:64px;margin:0;padding:0;width:100%;background-color:#3B97F6;">
                    <a style="display:block;padding-left:40px;padding-top:20px;" rel="noopener" target="_blank">刷了么签到服务
                    </a>
                </div>
                <div style="padding:50px;margin:0;"><div style="font-size:32px;color:#87BD29;line-border-bottom:1px dotted #E3E3E3;"></div>
                    <p>您好,时间'.date('Y-m-d').' '.$userName.' 的签到返回信息'.$signInfor.'

</p>
                    <p style="font-size:14px;color:#333;">——如果您未在刷了么使用签到服务,您可以直接忽略这封邮件</p>
                    <p style="font-size:12px;color:#999;border-top:1px dotted #E3E3E3;margin-top:30px;padding-top:30px;">
                        本邮件为系统邮件不用回复，请勿回复。
                    </p>
                </div>
            </div>
        </div>';
            $mail->AltBody = '签到返回信息：'.$signInfor.date('Y-m-d H:i:s');

            $mail->send();
            $mailInfor = '邮件发送成功，如未收到请检查邮箱的垃圾箱，并将DonaldTrump@email.cn移除（标记为非垃圾）';
        } catch (Exception $e) {
            $mailInfor = '邮件发送失败';
        }
    }else{
        $mailInfor = '未配置邮箱推送';
    }
    return $mailInfor;
}

//pushplus推送
function _pushplus(){
    $_token = $_POST['config']['push']['token'];
    $_userName = $_POST['userName'];
    $_signInfor = $_POST['signInfor'];
    
    if($_token != ''){
        $_content = '您好,时间'.date('Y-m-d').' '.$_userName.' 的签到返回信息'.$_signInfor;
        $_title = '今日校园打卡推送';
        //$_sever = 'https://www.pushplus.plus/send';
        $_sever = 'https://pushplus.hxtrip.com/send';
        $_pushInforJson = _post($_sever,'token='.$_token.'&title='.urlencode($_title).'&content='.urlencode($_content));
        //$_pushInfor = json_decode($_pushInforJson,true);
        $_pushInfor['code'] = strm($_pushInforJson, '<code>', '</code>');
        if ($_pushInfor['code'] == 200){
            $_print = '推送成功！';
        }else{
            $_print = $_pushInfor['msg'];
        }
    }else{
        $_print = '未配置pushplus';
    }
    return $_print;
}
