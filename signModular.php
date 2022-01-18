<?php
//include_once('loginModular.php');//加载登录函数
include_once('basicFunction.php');//加载基本函数

//判断是否有签到表单
function _isHaveSign(){
    $_cookie = $_POST['cookie'];
    $_getSignInstanceWidBody = '{}';
    $_resultJson = _postSubmit('https://tyut.campusphere.net/wec-counselor-sign-apps/stu/sign/getStuSignInfosInOneDay','{}',$_cookie);
    $_result = json_decode($_resultJson,true);
    if ($_result['datas']['unSignedTasks'] != ''){
        if (count($_result['datas']['unSignedTasks']) != 0){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
    return true;
}

//获取signInstanceWid
function _getSignInstanceWidAndSignWid(){
    $_cookie = $_POST['cookie'];
    $_getSignInstanceWidBody = '{}';
    $_resultJson = _postSubmit('https://tyut.campusphere.net/wec-counselor-sign-apps/stu/sign/getStuSignInfosInOneDay','{}',$_cookie);
    $_result = json_decode($_resultJson,true);
    $_signInstanceWid = $_result['datas']['unSignedTasks'][0]['signInstanceWid'];
    $_signWid = $_result['datas']['unSignedTasks'][0]['signWid'];
        
    //$_signInstanceWid = $_result['datas']['signedTasks'][0]['signInstanceWid'];
    //$_signWid = $_result['datas']['signedTasks'][0]['signWid'];
    
    $_returnData = array('signInstanceWid'=>$_signInstanceWid,'signWid'=>$_signWid);
    return $_returnData;
    //return $_resultJson;
}

//获取未看到的问题
function _getUnSeenQuestion($_signWidArray){
    $_cookie = $_POST['cookie'];
    $_signWidArrayJson = json_encode($_signWidArray);
    $_resultJson = _postSubmit('https://tyut.campusphere.net/wec-counselor-sign-apps/stu/sign/getUnSeenQuestion',$_signWidArrayJson,$_cookie);
    return 0;
}

//获取表单详细信息
function _getSignFormDetailedInfor($_signWidArray){
    $_cookie = $_POST['cookie'];
    $_getSignFormDetailedInforBody = json_encode($_signWidArray);
    $_resultJson = _postSubmit('https://tyut.campusphere.net/wec-counselor-sign-apps/stu/sign/detailSignInstance',$_getSignFormDetailedInforBody,$_cookie);
    //$_result = json_encode($_resultJson,true);
    return $_resultJson;
}

//获取人的名字
/*function _getUserName($_signFormInforJson){
    $_signFormInfor = json_decode($_signFormInforJson,true);
    $_userId = $_signFormInfor['datas']['signedStuInfo']['config']['system']['userId'];
    return $_userId;
    
}*/

//填充签到表单
function _fillSignForm($_signWidArray,$_signFormDetailedInforJson){
    //$_itude = _randItude();
    $_itude = $_POST['itude'];
    $_deviceId = $_POST['deviceId'];
    $_userId = $_POST['config']['system']['userId'];
    $_signFormDetailedInfor = json_decode($_signFormDetailedInforJson,true);
    $_extraField = $_signFormDetailedInfor['datas']['extraField'];
    $_extraFieldItems = array();
    for ($i=0;$i<count($_extraField);$i++){
        for ($j=0;$j<count($_extraField[$i]['extraFieldItems']);$j++){
           if (_strstr($_extraField[$i]['extraFieldItems'][$j]['content'],$_POST['config']['fillForm']['optionKeyword']) == true){
               $_extraFieldItems[] = array('extraFieldItemValue'=>$_extraField[$i]['extraFieldItems'][$j]['content'],'extraFieldItemWid'=>$_extraField[$i]['extraFieldItems'][$j]['wid']);
           } else if(stristr($_extraField[$i]['extraFieldItems'][$j]['content'],$_POST['config']['fillForm']['tempKeyword']) == true){
               $_randTemp = _randTemp();//生成体温
               $_extraFieldItems[] = array('extraFieldItemValue'=>$_randTemp,'extraFieldItemWid'=>$_extraField[$i]['extraFieldItems'][$j]['wid']);//填入随机体温
           }
        }
    }
    $_filledForm = array();
    $_filledForm['extraFieldItems'] = $_extraFieldItems;
    $_filledForm['signInstanceWid'] = $_signWidArray['signInstanceWid'];
    $_filledForm['longitude'] = $_itude['longitude'];
    $_filledForm['latitude'] = $_itude['latitude'];
    $_filledForm['isMalposition'] = _isMalposition($_signFormDetailedInfor);
    $_filledForm['abnormalReason'] = '';
    $_filledForm['position'] = $_POST['config']['location']['position'];
    $_filledForm['uaIsCpadaily'] = true;
    $_filledForm['signVersion'] = '1.0.0';
    $_filledForm['signPhotoUrl'] = '';//
    $_filledForm['isNeedExtra'] = 1;//
    $_filledFormJson = json_encode($_filledForm);
    $_trueForm = array();
    $_trueForm['version'] = 'first_v2';
    $_trueForm['calVersion'] = 'firstv';
    $_trueForm['bodyString'] = _AESEncrypt($_filledFormJson,'ytUQ7l2ZZu8mLvJZ');
    $_signArray = $_extension;
    $_signArray['bodyString'] = $_trueForm['bodyString'];
    $_trueForm['sign'] = md5(http_build_query($_filledForm).'&ytUQ7l2ZZu8mLvJZ');
    $_extension = array('appVersion' => '9.0.12', 'systemName' => 'android',  'model' => 'MI 6',
                'lon' => $_itude['longitude'], 'systemVersion' => '8.0.1', 'deviceId' => $_deviceId, 'lat' => $_itude['latitude'],'userId' => $_userId);
    $_trueForm = array_merge($_trueForm,$_extension);
    $_returnData = json_encode($_trueForm,JSON_UNESCAPED_UNICODE);
    return $_returnData;
}



//提交签到表单
function _sign(){
    $_cookie = $_POST['cookie'];
    $_userId = $_POST['config']['system']['userId'];
    $_itude = $_POST['itude'];
    $_deviceId = $_POST['deviceId'];
        if (_isHaveSign()){
            $_widArray = _getSignInstanceWidAndSignWid();
            $_formInfor = _getSignFormDetailedInfor($_widArray);
            $_submitData = _fillSignForm($_widArray,$_formInfor);
            $_extension = _creatExtension();
            $_resultJson = _postSubmitForm('https://tyut.campusphere.net/wec-counselor-sign-apps/stu/sign/submitSign',$_submitData,$_cookie,$_extension);
            $_formInfor = _getSignFormDetailedInfor($_widArray);//
            $_result = json_decode($_resultJson,true);
            if ($_result['message'] == 'SUCCESS'){
                $_print = '签到成功';
            }else{
                $_print = $_result['message'];
            }
        }else{
            $_print = '无未签到表单';
        }
    return $_print;
}
