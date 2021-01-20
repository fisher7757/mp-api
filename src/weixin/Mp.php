<?php
declare (strict_types = 1);

namespace fisherMpApi\weixin;

use fisherMpApi\MpClass;
use fisherMpApi\weixin\bizDataCrypt\WXBizDataCrypt;

class Mp
{
    /*
    * edit: fisher
    * date: 20201205
    * */
    public function __construct($appid='',$secret='')
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    /*
     * edit: fisher
     * date: 20201205
     * con: 获取小程序全局唯一后台接口调用凭据（access_token）。调用绝大多数后台接口时都需使用 access_token，开发者需要进行妥善保存。
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/access-token/auth.getAccessToken.html
     * 提醒：accessToken请缓存，本处不做缓存。
     * */
    public function getAccessToken(){
        $dao = new MpClass();
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=1{$this->appid}&secret={$this->secret}";
        $result = $dao->httpCurl($url);
        if(!$result)return ['code'=>500,'msg'=>'内部服务错误','data'=>[]];
        $resultData = get_object_vars(json_decode($result));
        if(!isset($resultData['errcode'])){
            return ['code'=>200,'msg'=>'ok','data'=>$resultData];
        }
        return ['code'=>$resultData['errcode'],'msg'=>$resultData['errmsg'],'data'=>[]];
    }

    /*
     * edit: fisher
     * date: 20201205
     * con: 登录凭证校验。通过 wx.login 接口获得临时登录凭证 code 后传到开发者服务器调用此接口完成登录流程。
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/login/auth.code2Session.html
     * */
    public function jscode2session($code=''){
        $dao = new MpClass();
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->appid}&secret={$this->secret}&js_code={$code}&grant_type=authorization_code";
        $result = $dao->httpCurl($url);
        if(!$result)return ['code'=>500,'msg'=>'内部服务错误','data'=>[]];
        $resultData = get_object_vars(json_decode($result));
        if(!isset($resultData['errcode'])){
            return ['code'=>200,'msg'=>'ok','data'=>$resultData];
        }
        return ['code'=>$resultData['errcode'],'msg'=>$resultData['errmsg'],'data'=>[]];
    }

    /*
    * edit: fisher
    * date: 20201205
    * con: 解密小程序消息
    * */
    public function decrypData($sessionKey='', $decryptData=[])
    {
        $res = new WXBizDataCrypt($this->appid, $sessionKey);

        $errCode = $res->decryptData($decryptData['encryptedData'], $decryptData['iv'], $data );
        if ($errCode == 0) return ['code'=>200,'msg'=>'ok','data'=>$data];
        else return ['code'=>$errCode,'msg'=>'fail','data'=>(object)[]];
    }

}

