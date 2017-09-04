<?php

namespace app\controllers;
use WxPayApi;
use WxPayConfig;
use WxPayException;
use WxPayJsApiPay;
use WxPayUnifiedOrder;
use Yii;
use yii\helpers\Url;

class IndexController extends WechatController
{

    public $layout='frontend';
    public function actionIndex()
    {
        header("content-type:text/html;charset=utf-8");
        ini_set('date.timezone','Asia/Shanghai');

        if (!empty(Yii::$app->request->get('code')))
        {
            $openid=$this->getUserOpenId(Yii::$app->request->get('code'));
            if (!$openid){
                return $this->redirect(Url::to(['wechat/wx-code']));
            }
        }else{
            return $this->redirect(Url::to(['wechat/wx-code']));
        }
        require_once "../wxpay/lib/WxPay.Data.php";
        require_once "../wxpay/lib/WxPay.Api.php";

        $input = new WxPayUnifiedOrder();
        $input->SetBody("进群二维码");
//        $input->SetAttach("wsh");
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1000");
        $input->SetTime_start(date("YmdHis"));
//        $input->SetTime_expire(date("YmdHis", time() + 1000));
//        $input->SetGoods_tag("test");
        $input->SetNotify_url(Url::to(['wechat/callback']));
        $input->SetTrade_type("JSAPI");
        if (!empty($openid)) {
            $input->SetOpenid($openid);
        }
        $order = WxPayApi::unifiedOrder($input);

        $jsApiParameters = $this->GetJsApiParameters($order);

        return $this->render('index',[
            'jsApiParameters'=>$jsApiParameters,
        ]);
    }

    public function GetJsApiParameters($UnifiedOrderResult)
    {
        require_once "../wxpay/lib/WxPay.Data.php";
        require_once "../wxpay/lib/WxPay.Api.php";
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }
        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }



}
