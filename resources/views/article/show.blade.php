<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        {{--  <meta name="viewport" content="width=device-width, initial-scale=1">  --}}
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $article['title'] }}</title>
    </head>
    <body>
        <div id="app" class="container">
            {!!$content !!}


            <div class="contact">
                    <main>
                      <section>
                        <div class="content">
                          <img src="{{ $article['owner']['figureurl'] or '/images/default.jpg' }}" alt="Profile Image">
                
                          <aside>
                            <h1>{{ $article['owner']['name'] }}</h1>
                            <p>灵恩思维推荐代理人</p>
                          </aside>
                          
                          <button id="wx">
                            <span>加我微信</span>
                            <svg xmlns="{{ $article['owner']['xq_code'] }}" width="48" height="48" viewBox="0 0 48 48"> <g class="nc-icon-wrapper" fill="#444444"> <path d="M14.83 30.83L24 21.66l9.17 9.17L36 28 24 16 12 28z"></path> </g> </svg>
                          </button>

                          <button id="bx">
                            <span>免费定制保险</span>
                            <svg xmlns="{{ $article['owner']['xq_code'] }}" width="48" height="48" viewBox="0 0 48 48"> <g class="nc-icon-wrapper" fill="#444444"> <path d="M14.83 30.83L24 21.66l9.17 9.17L36 28 24 16 12 28z"></path> </g> </svg>
                          </button>
                        </div>
                
                        <div class="title"><p>加我微信</p></div>
                      </section>
                    </main>

                    <nav >
                            <a href="#" class="gmail">
                              <div class="icon">
                                <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M16 3v10c0 .567-.433 1-1 1h-1V4.925L8 9.233 2 4.925V14H1c-.567 0-1-.433-1-1V3c0-.283.108-.533.287-.712C.467 2.107.718 2 1 2h.333L8 6.833 14.667 2H15c.283 0 .533.108.713.288.179.179.287.429.287.712z" fill-rule="evenodd"></path></svg>
                              </div>
                      
                              <div class="content">
                                <h1>Email</h1>
                                <span>Riccavallo@gmail.com</span>
                              </div>
                              
                              <svg class="arrow" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"> <g class="nc-icon-wrapper" fill="#444444"> <path d="M17.17 32.92l9.17-9.17-9.17-9.17L20 11.75l12 12-12 12z"></path> </g> </svg>
                            </a>
                          </nav>
            </div>

            <div class="copyright">
                Copyright © 2018 Lingensiwei.com. All Rights Reserved.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <br/>
                粤ICP备18016347号
            </div>
        
        </div>
    </body>
    <link rel="stylesheet" href="/css/lesw.css">
    <style>
        .contact main section .content img {
            /* width: 25%; */
        }
    </style>
    <script src="/js/jweixin-1.2.0.js"></script>
    <script src="/js/axios.min.js"></script>
    <script src="/js/jquery-1.7.1.min.js"></script>
    <script>
        function unfold(){  
            var doc =  document;     
            var wrap=doc.querySelector("#js_article");  
            var unfoldField=doc.querySelector("#btn");  
            unfoldField.onclick=function(){  
                this.parentNode.removeChild(this);  
                wrap.style.maxHeight="100%";   
            }  
            document.onreadystatechange = function () { //当内容中有图片时，立即获取无法获取到实际高度，需要用 onreadystatechange  
                if (document.readyState === "complete") {  
                    var wrapH=doc.querySelector("#js_article").offsetHeight;  
                    var contentH=doc.querySelector("#page-content").offsetHeight;  
                    if(contentH <= wrapH){  // 如果实际高度大于我们设置的默认高度就把超出的部分隐藏。  
                        unfoldField.style.display="none";  
                    }   
                }  
            }  
        }  
        unfold();
        // var btn = document.getElementById('btn');  
        // var obj = document.getElementById('js_content');  
        // var total_height =  obj.scrollHeight;//文章总高度  
        // var show_height = 200;//定义原始显示高度  
        // if(total_height>show_height){  
        //     alert(total_height);
        //     btn.style.display = 'block';  
        //     btn.onclick = function(){  
        //         obj.style.height = '100%';  
        //         btn.style.display = 'none';  
        //     }  
            
        // }  
    </script> 
    
    <script>
        // $('button').click(function(){
        // $('button').toggleClass('active');
        // $('.title').toggleClass('active');
        // $('nav').toggleClass('active');
        // });
        $('#wx').click(function(){
            var xq_code = "{{ $article['owner']['xq_code'] }}";
            if (xq_code) {
                window.location.href=xq_code;
            } else {
                alert('未设置微信名片！');
            }
        });
        $('#bx').click(function(){
            var xq_code = "{{ $article['owner']['xq_code'] }}";
            if (xq_code) {
                window.location.href=xq_code;
            } else {
                alert('未设置微信名片！');
            }
        });
        axios.defaults.headers.post['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: '{{ $wxjs_config['appId'] }}', // 必填，公众号的唯一标识
            timestamp: {{ $wxjs_config['timestamp'] }}, // 必填，生成签名的时间戳
            nonceStr: '{{ $wxjs_config['nonceStr'] }}', // 必填，生成签名的随机串
            signature: '{{ $wxjs_config['signature'] }}',// 必填，签名
            jsApiList: {!! $wxjs_config['jsApiList'] !!} // 必填，需要使用的JS接口列表
        });
        wx.ready(function () {
            wx.onMenuShareTimeline({
                title: '{{ $article['title'] OR "灵恩思维" }}', // 分享标题
                link:  '{{ "http://".$_SERVER['HTTP_HOST'].Request::getPathInfo()."?fromuid=".session('user_id') }}', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{ $article['thumbnail'] }}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    axios.post('/api/share', {'article_id':{{ $article['id'] }}, 'fromuid':{{ $_GET['fromuid'] OR 0 }}, 'type':1 }).then(response => {
                    
                    });
                },
                cancel: function () {

                }
            });
            wx.onMenuShareAppMessage({
                title: '{{ $article['title'] }}', // 分享标题
                desc: '{{ $article['title'] }}', // 分享描述
                link: '{{ "http://".$_SERVER['HTTP_HOST'].Request::getPathInfo()."?fromuid=".session('user_id')}}', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{ $article['thumbnail'] }}', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                    axios.post('/api/share', {'article_id':{{ $article['id'] }}, 'fromuid':{{ $_GET['fromuid'] OR 0 }}, 'type':2 }).then(response => {
                    
                    });
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        })

    </script>
</html>
