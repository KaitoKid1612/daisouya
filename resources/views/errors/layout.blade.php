<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('images/favicons/favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/favicons/apple-touch-icon-180x180.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('images/favicons/android-chrome-192x192.png') }}">
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width" initial-scale=1>
  <link rel="stylesheet" href="{{ asset('css/app_driver.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
  @yield('script_head')
</head>

<body>
  <header class="bl_header">
    <div class="bl_header_inner">

        @php
            $url = explode('/', $_SERVER['REQUEST_URI'])[1];
        @endphp

      <div class="bl_header_inner_ttl">
        <a href="/{{$url}}">
          <img src="{{ asset('images/common/logo.png') }}" alt="代走屋">
        </a>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <div class='bl_overlay js_overlay'></div>

  <div class="js_loading">
    <div class="js_loading_circle"></div>
  </div>

  <footer class="bl_footer">
    <div class="bl_footer_inner">
      <div class="bl_footer_inner_menu">
        <div class="bl_footer_inner_menu_img">
          <a href="{{ route('driver.dashboard.index') }}">
            <img src="{{ asset('images/common/logo.png') }}" alt="代走屋">
          </a>
        </div>
        <ul>
          <li><a href="{{ route('guest.web_terms_service.index', ['type' => 'driver']) }}">利用規約</a></li>
          <li><a href="{{ route('guest.web_commerce_law.index', ['type' => 'driver']) }}">特定商取引法に基づく表記</a></li>
          <li><a href="{{ route('guest.web_privacy_policy.index', ['type' => 'driver']) }}">プライバシーポリシー</a></li>
          <li><a href="{{ route('storage_file.show', ['path' => 'web/config_base/user_guide_driver.pdf']) }}" target="_blank">ユーザーガイド</a></li>

          <li><a href="{{ route('guest.web_contact.create') }}">お問い合わせ</a></li>
        </ul>
      </div>
      <div class="bl_footer_inner_copyright">
        <p>Copyright &copy; 2022{{ date('Y') == '2022' ? '' : '-' . date('Y') }} 株式会社ケイズリング, All Rights Reserved.</p>
      </div>
    </div>
  </footer>
</body>

</html>
