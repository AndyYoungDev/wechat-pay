<?php
/* @var $this yii\web\View */
?>
<video id="video-player" src="video/test.mp4" autoplay="autoplay">
    您的浏览器不支持 video 标签。
</video>
<div class="container">
    <button class="btn btn-lg btn-info center-block" onclick="callpay()">打赏10元进群</button>
</div>
<script type="text/javascript">
    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?=$jsApiParameters; ?>,
            function(res){
                WeixinJSBridge.log(res.err_msg);
                alert(res.err_code+res.err_desc+res.err_msg);
            }
        );
    }

    function callpay()
    {
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
</script>

<?php
$this->registerJs("
    $('#video-player').attr('width',$(window).width());
");
?>
