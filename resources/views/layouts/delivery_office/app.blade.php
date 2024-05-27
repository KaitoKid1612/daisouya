<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('images/favicons/favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/favicons/apple-touch-icon-180x180.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('images/favicons/android-chrome-192x192.png') }}">
  <title>代走屋 依頼者 @yield('title')</title>
  <meta name="viewport" content="width=device-width" initial-scale=1>
  <link rel="stylesheet" href="{{ asset('css/app_delivery_office.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
  @yield('script_head')
</head>

<body>
  <header class="bl_header">
    <div class="bl_header_inner">

      <div class="bl_header_inner_ttl">
        <a href="{{ route('delivery_office.dashboard.index') }}">
          <img src="{{ asset('images/common/logo.png') }}" alt="代走屋">
        </a>
      </div>
      <div class="bl_header_inner_userBtn">
        @if (Auth::guard('delivery_offices')->check())
          <a href="{{ route('delivery_office.user.index') }}">
            {{ Auth::guard('delivery_offices')->user()->name ?? '' }}
            {{ Auth::guard('delivery_offices')->user()->charge_user_type_id == 2 ? '【' . Auth::guard('delivery_offices')->user()->joinChargeUserType->name . '】' : '' }}
          </a>
        @else
          <a href="{{ route('delivery_office.login') }}">
            ログイン
          </a>
          {{-- <a href="{{ route('delivery_office.register') }}">
            登録
          </a> --}}
        @endif
      </div>

      @if (Auth::guard('delivery_offices')->check())
        <div class="bl_header_inner_menuBtn">
          <button class='js_menuBtn'>
            <i class="fa-solid fa-bars"></i>
          </button>
        </div>
      @endif

      @if (Auth::guard('delivery_offices')->check())
        <div class="bl_header_inner_menu js_menu">
          <div class="bl_header_inner_menu_head">
            <h2>メニュー<br><span>menu</span></h2>
            <div class="bl_header_inner_head_userBtn">
              <a href="{{ route('delivery_office.user.index') }}">
                {{ Auth::guard('delivery_offices')->user()->name ?? '' }}
              </a>
            </div>
          </div>
          <nav>
            <ul>
              @php
                $menu_list = [
                    ['text' => 'ダッシュボード', 'href' => route('delivery_office.dashboard.index'), 'path' => 'delivery-office/dashboard', 'access_list' => []], //
                    ['text' => 'ドライバー検索', 'href' => route('delivery_office.driver.index'), 'path' => 'delivery-office/driver', 'access_list' => []], //
                    ['text' => '稼働依頼登録', 'href' => route('delivery_office.driver_task.create'), 'path' => 'delivery-office/driver-task/create', 'access_list' => []], //
                    ['text' => 'ご依頼履歴', 'href' => route('delivery_office.driver_task.index'), 'path' => 'delivery-office/driver-task-list', 'access_list' => []], //
                    ['text' => '集荷先住所登録', 'href' => route('delivery_office.delivery_pickup_addr.index'), 'path' => 'delivery-office/delivery-pickup-addr', 'access_list' => []], //
                    ['text' => 'レビュー', 'href' => route('delivery_office.delivery_office_task_review.index'), 'path' => 'delivery-office/delivery-office-task-review', 'access_list' => []], //
                    ['text' => 'アカウント情報', 'href' => route('delivery_office.user.index'), 'path' => 'delivery-office\/user', 'access_list' => []], //
                ];
              @endphp

              @foreach ($menu_list as $menu)
                <li>
                  <a href="{{ $menu['href'] }}" class="{{ preg_match('|^\/' . preg_quote($menu['path'], '/') . "(/.*)?$|", $_SERVER['REQUEST_URI']) ? 'active' : '' }}">
                    {!! $menu['text'] !!}
                  </a>
                </li>
              @endforeach
              <li>
                <form action="/delivery-office/logout" method="POST" class="js_logout">
                  @csrf
                  <button type="submit">ログアウト<span></span></button>
                </form>
              </li>
            </ul>
          </nav>

        </div>
      @endif
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
          <li><a href="{{ route('guest.web_terms_service.index', ['type' => 'office']) }}">利用規約</a></li>
          <li><a href="{{ route('guest.web_commerce_law.index', ['type' => 'office']) }}">特定商取引法に基づく表記</a></li>
          <li><a href="{{ route('guest.web_privacy_policy.index', ['type' => 'office']) }}">プライバシーポリシー</a></li>
          <li><a href="{{ route('storage_file.show', ['path' => 'web/config_base/user_guide_delivery_office.pdf']) }}" target="_blank">ユーザーガイド</a></li>
          <li><a href="{{ route('guest.web_contact.create') }}">お問い合わせ</a></li>

        </ul>
      </div>
      <div class="bl_footer_inner_copyright">
        <p>Copyright &copy; 2022{{ date('Y') == '2022' ? '' : '-' . date('Y') }} 株式会社ケイズリング, All Rights Reserved.</p>
      </div>
    </div>
  </footer>
  <script src="{{ asset('./js/app_delivery_office.js') }}"></script>
  <script src="{{ asset('./js/libs/Functions/sidebar.js') }}"></script>

  @yield('script_bottom')
</body>

</html>
