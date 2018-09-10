<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        {{--  <meta name="viewport" content="width=device-width, initial-scale=1">  --}}
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <!-- 使用rem，需另外引入ydui.flexible.js自适应类库 -->
        {{--  <link rel="stylesheet" href="/css/ydui.rem.css">  --}}
        {{--  <script src="/js/ydui.flexible.js"></script>  --}}

        <!-- 引入Vue2.x -->
        <script src="/js/vue.min.js"></script>
        <!-- 引入组件库 -->
        <script src="/js/ydui.rem.js"></script>
        
    </head>
    <body>
        <div id="app" class="container">
            <router-view></router-view>
            {{--  @yield('content')  --}}
            <tabbar-component></tabbar-component>
        </div>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
    <link rel="stylesheet" href="/css/lesw.css">
</html>