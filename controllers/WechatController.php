<?php

namespace app\controllers;

use WxPayNotify;
use Yii;
use yii\helpers\Url;

class WechatController extends BaseController
{
    private $token;
    private function getToken()
    {
        $this->token= Yii::$app->params['wechat']['token'];
    }
    public function actionIndex()
    {
        $this->getAccessToken();
    }

    public function actionSignature($signature,$timestamp,$nonce,$echostr)
    {
        $this->getToken();
        $tmpArr=[$this->token,$timestamp,$nonce];
        sort($tmpArr,SORT_STRING);
        if ($signature===sha1(implode($tmpArr)))
        {
            return $echostr;
        }else{
            return false;
        }
    }
    private function requestAccessToken()
    {
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=";
        $url.= Yii::$app->params['wechat']['appId']."&secret=";
        $url.= Yii::$app->params['wechat']['appSecret'];
        $response=$this->sendMsg($url);
        $array=$this->jsonToArray($response);
        $cache= Yii::$app->cache;
        $cache->set('access_token',$array['access_token'],'7000');
        return $cache->get('access_token');
    }
    private function getAccessToken()
    {
        $cache= Yii::$app->cache;
        if ($access_token=$cache->get('access_token')){
            return $access_token;
        }else{
            return $this->requestAccessToken();
        }
    }
    public function actionWxCode()
    {
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=";
        $url.= Yii::$app->params['wechat']['appId']."&redirect_uri=";
        $url.=urlencode(Yii::app()->request->hostInfo)."&response_type=code&scope=";
        $url.="snsapi_base"."&state=STATE#wechat_redirect";
        $this->redirect($url);
    }
    public function getUserOpenId($code)
    {
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=";
        $url.= Yii::$app->params['wechat']['appId']."&secret=";
        $url.= Yii::$app->params['wechat']['appSecret']."&code=";
        $url.=$code."&grant_type=authorization_code";
        $response=$this->sendMsg($url);
        $array=json_decode($response,true);


        if (array_key_exists("openid",$array)){
            return $array['openid'];
        }else{
            return false;
        }

    }
    public function actionCallback()
    {
        $notify = new WxPayNotify();
        $notify->Handle(false);
    }

}
