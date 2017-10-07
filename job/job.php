<?php
session_start();

$login = @$_SESSION["login"];//是否登陆
$sms_key = @$_SESSION["sms_key"];//取得短信KEY
if(empty($login) || empty($sms_key)){
    $k = @$_GET["k"];//短信KEY GET值
    if(empty($k)){
        ReturnAjax("fail","没有短信KEY","no key");
    }else{
        $_SESSION["login"] = 1;
        $_SESSION["sms_key"] = $k;
    }
}

exit($login."<=");

$act = @$_GET["c"];
if(empty($act)){
    ReturnAjax("fail","没有请求方法","No Action!");
}

switch ($act) {
    case 'sms':
        /*发送短信*/
        $job = @$_GET['job'];
        $time = @$_GET['time'];
        $mobile = @$_GET['mobile'];
        $tpl_id = @$_GET['tpl_id'];
        if(empty($job) || empty($time) || empty($mobile) || empty($tpl_id)){
            ReturnAjax("fail","参数不全","No Parameter!");
        }
        $r = send_sms($job,$time,$mobile,$tpl_id,SMS_KEY);
        $r = $r['content'];
        $r = json_decode($r,true);
        if(intval($r['error_code']) == 0){
            $retype = 'success';
            $reInfo = '发送成功';
            $reText = 'Send Success';
        }else{
            $retype = 'fail';
            $reInfo = '发送失败';
            $reText = 'Send Fail';
        }
        ReturnAjax($retype,$reInfo,$reText);
        break;
    
    default:
        ReturnAjax("fail","没有请求方法","No Action!");
        break;
}




function send_sms($job,$time,$mobile,$tpl_id,$sms_key){
    /*
    mobile  string  是   接收短信的手机号码
    tpl_id  int 是   短信模板ID，请参考个人中心短信模板设置
    tpl_value   string  是   变量名和变量值对，如：#code#=431515，整串值需要urlencode，比如正确结果为：%23code%23%3d431515。如果你的变量名或者变量值中带有#&=中的任意一个特殊符号，请先分别进行utf-8 urlencode编码后再传递，详细说明>
    key string  是   应用APPKEY(应用详细页查询)
    dtype   string  否   返回数据的格式,xml或json，默认json
    #code#=1234&#company#=聚合数据
    */
    $sms["mobile"] = $mobile;
    $sms["tpl_id"] = $tpl_id;
    $sms["key"] = $sms_key;
    $sms["dtype"] = "json";
    $sms["tpl_value"] = urlencode("#job#=".$job."&#time#=".$time);/* "#time#=1234&#company#=聚合数据"*/
}


function ReturnAjax($reType,$info,$text){
    switch ($reType) {
        case 'success':
            $status = 0;
            break;
        case 'fail':
            $status = 1;
            break;
    }
    $r["info"] = $info;
    $r["text"] = $text;
    $r["status"] = $status;
    $r['type'] = $reType;
    $f = json_encode($r);
    exit($f);
}
