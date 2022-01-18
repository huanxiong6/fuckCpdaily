<?php
$user = ''; //超人云账号
$pass = ''; //超人云密码
$softid = '';//缺省为0,作者必填自己的软件id,以保证分成收入.

include_once("basicFunction.php");
//curl请求模块
function SendRequest($url, $headers=array(), $data='', $method='POST', $type=0){//请求方法
    //$ip = ip();
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, $type);
    $ip = ip();
    $headers[] = 'CLIENT-IP: '.$ip;
    $headers[] = 'X-FORWARDED-FOR:'.$ip;
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    
    //$proxy = file_get_contents("http://pandavip.xiongmaodaili.com/xiongmao-web/apiPlus/vgl?secret=729d62e2ac64c9f147d8715f003734fe&orderNo=VGL20210929120934tgsPrOEJ&count=1&isTxt=1&proxyType=1&validTime=0&removal=0&cityIds=");
    //curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    //curl_setopt($curl, CURLOPT_PROXY, $proxy);//TG bot需要使用代理，请自备梯子
    if($method == 'POST'){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);//填写POST请求参数
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    }
    $result = curl_exec($curl);
    if($type == 1){
        $headers = curl_getinfo($curl, CURLINFO_HEADER_SIZE);//获取响应头
        $_POST['headers'] = substr($result, 0, $headers);//截取响应头
    }
    curl_close($curl);
    return $result;
}

//正则匹配响应头
function PregMatchMsg($responsehead, $start, $end, $flag=false) {
    $preg = '/'.$start.'(.*?)'.$end.'/m';
    preg_match_all($preg, $responsehead, $text);
    if($flag)
        return $text[1][0];//只返回第一次匹配的信息
    else
        return implode(';', $text[1]);//返回全部匹配的信息，使用;分隔
}

//模拟登录请求头
function DoLoginHeader($referer){
    $header = array( 'Accept: application/json, text/plain, */*', 'Accept-Language: zh-CN,zh;q=0.8', 'Connection: keep-alive',
                'Referer: '.$referer, 'Cookie: '.$_COOKIE, 'Content-Type:application/x-www-form-urlencoded'/*,'CLIENT-IP:219.226.120.16','X-FORWARDED-FOR:219.226.120.16'*/);
    return $header;
}

function CheckFinallCookie($response){
    if(is_numeric(strpos($response, 'MOD_AUTH_CAS')))//判断是否存在获取MOD_AUTH_CAS
    {
        $_COOKIE = PregMatchMsg($response, 'Set-Cookie:', ';');//正则匹配cookie;
    }else{
        $_COOKIE = '登陆失败';//返回错误信息
    }
    return $_COOKIE;
}

function CheckCastgc($response){

    $_aaa = PregMatchMsg($response, 'Set-Cookie:', ';');//正则匹配cookie;
    return $_aaa;
}

function IapSchoolLogin($name, $pwd){
    $headers = SendRequest('https://tyut.campusphere.net/iap/login?service=https://tyut.campusphere.net/portal/login', array(), '', 'GET', 1);//获取CONVERSATION
    $_COOKIE = PregMatchMsg($_POST['headers'], 'Set-Cookie:', ';');//正则匹配cookie
    $lt = substr(explode("_2lBepC=", $_POST['headers'])[1], 0, -4);//提取响应头中的lt
    $form = [ 'password'=>$pwd,'captcha'=> '', 'mobile'=>'','lt'=>$lt,//构造参数
                'rememberMe'=>'false', 'username'=>$name, 'dllt'=>''];
    $url = 'https://tyut.campusphere.net/iap/doLogin';//登录url
    $header = DoLoginHeader($url);//登录请求头
    $res = SendRequest($url, $header, http_build_query($form), 'POST', 1);//获取MOD_AUTH_CAS
    $aaa = CheckFinallCookie($res);//检查有无最终的cookie MOD_AUTH_CAS并获取
    return $aaa;
}