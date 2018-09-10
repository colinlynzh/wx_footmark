<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        {{--  <meta name="viewport" content="width=device-width, initial-scale=1">  --}}
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>VIP充值</title>
        <!-- 使用rem，需另外引入ydui.flexible.js自适应类库 -->
        {{--  <link rel="stylesheet" href="/css/ydui.rem.css">  --}}
        {{--  <script src="/js/ydui.flexible.js"></script>  --}}
        <!-- 引入组件库 -->
        <script src="/js/ydui.rem.js"></script>
        
        <style>
            html, body, #app {
                height: 100%;
                width: 100%;
            }
            #shareit {  
            -webkit-user-select: none;  
            position: absolute;  
            width: 100%;  
            height: 3000px;  
            background: rgba(0,0,0,0.85);  
            text-align: center;  
            top: 0;  
            left: 0;  
            z-index: 105;  
          }  
          #shareit img {  
            max-width: 100%;  
          }  
          .arrow {  
            position: absolute;  
            right: 10%;  
            top: 5%;  
          }  
          #share-text {  
            margin-top: 150px;  
          }  
        </style>
    </head>
    <body>
        <div id="app" class="container">
           
            <div class="vip">
                <ul>    
                <li><img src="/images/vip_header.jpg"></li>
                <li><img src="/images/vip_1.jpg"></li>
                <li><img src="/images/vip_2.jpg"></li>
                <li><img src="/images/vip_3.jpg"></li>
                <li><img src="/images/vip_4.jpg"></li>
                <li><img src="/images/vip_5.jpg"></li>
                <li><img src="/images/vip_bottom.jpg"></li>
                </ul>
            </div>

            <div id="vip_contact" class="contact">
                    <main>
                      <section>
                        <div class="content">
                          {{-- <div class="ite">
                                <span><b>永久终身会员</b></span>
                                <span><s>888</s>&nbsp;<b style="color:red;">518</b>元</span>
                                <button class="pay_vip" vip_type="1">开通</button>
                          </div> --}}
                          <div class="ite">
                                <span><b>一年钻石会员</b></span>
                                <span><s>100</s>&nbsp;<b style="color:red;">38</b>元</span>
                                <button class="pay_vip" vip_type="2">开通</button>
                          </div>
                          
                        </div>
                
                        <div class="title"><p>加我微信</p></div>
                      </section>
                    </main>

                    
            </div>

            <div class="copyright">
                Copyright © 2018 Lingensiwei.com. All Rights Reserved.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <br/>
                粤ICP备18016347号
            </div>
            
        </div>
        @if (isset($_GET['fromuid']) && $_GET['fromuid']>0)
        <div id="shareit">
        {{-- <img class="arrow" src="/images/share-it.png"> --}}
        <a href="#" id="follow">
            <img id="share-text" src="/images/share-text.png">
        </a>
        </div>
        @endif
    </body>
    <link rel="stylesheet" href="/css/lesw.css">
    <script src="/js/jweixin-1.2.0.js"></script>
    <script src="/js/axios.min.js"></script>
    <script src="/js/jquery-1.7.1.min.js"></script>
    <script>
        @if (isset($_GET['fromuid']) && $_GET['fromuid']>0)
        $("#shareit").on("click", function(){  
            $("#shareit").hide();   
        });  
        @endif
        
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: '{{ $wxjs_config['appId'] }}', // 必填，公众号的唯一标识
            timestamp: {{ $wxjs_config['timestamp'] }}, // 必填，生成签名的时间戳
            nonceStr: '{{ $wxjs_config['nonceStr'] }}', // 必填，生成签名的随机串
            signature: '{{ $wxjs_config['signature'] }}',// 必填，签名
            jsApiList: {!! $wxjs_config['jsApiList'] !!} // 必填，需要使用的JS接口列表
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        wx.ready(function () {
            $(".pay_vip").click(function(){
                var vip_type = $(this).attr('vip_type');
                var recommend = '{{ $recommend }}';
                $.post('/api/wxpay-config', {'data':{'vip_type':vip_type, 'recommend_uid':recommend }},function(data){
                    if (data.status) {
                        data = data.data;
                        wx.chooseWXPay({
                            timestamp: data.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                            nonceStr: data.nonceStr, // 支付签名随机串，不长于 32 位
                            package: data.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
                            signType: data.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                            paySign: data.paySign, // 支付签名
                            success: function (res) {
                                // 支付成功后的回调函数
                                if (res) {
                                    alert('支付成功！恭喜你已经成为会员！');
                                    window.location.href="/ucenter";
                                }
                            }
                        });
                    } else {
                        alert(data.message);
                        return false;
                    }
                });
            }); 
            wx.onMenuShareTimeline({
                title: '{{ $user['name']."邀请您成为灵恩思维VIP，一起探索未知的人脉圈！" }}', // 分享标题
                link:  '{{ "http://".$_SERVER['HTTP_HOST']."/vip/recommend/".session('user_id') }}', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{  "http://".$_SERVER['HTTP_HOST']."/images/vip_1.jpg" }}', // 分享图标
                success: function () {
                    
                },
                cancel: function () {

                }
            });
            wx.onMenuShareAppMessage({
                title: '{{ $user['name']."邀请您成为灵恩思维，一起探索未知的人脉圈！" }}', // 分享标题
                link:  '{{ "http://".$_SERVER['HTTP_HOST']."/vip/recommend/".session('user_id') }}', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{  "http://".$_SERVER['HTTP_HOST']."/images/lesw.png" }}', // 分享图标
                success: function () {
                    
                },
                cancel: function () {

                }
            });
            
        });

    </script>
</html>
