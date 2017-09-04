<?php

namespace app\controllers;

use yii\web\Controller;

class BaseController extends Controller
{
    protected function sendMsg($url,$method='get',$post_data=null)
    {
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if ($method=='post')
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        }
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    protected function jsonToArray($json)
    {
        return json_decode($json,true);
    }
}
