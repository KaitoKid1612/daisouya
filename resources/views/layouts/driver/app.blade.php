<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('images/favicons/favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/favicons/apple-touch-icon-180x180.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('images/favicons/android-chrome-192x192.png') }}">
  <title>代走屋 ドライバー @yield('title')</title>
  <meta name="viewport" content="width=device-width" initial-scale=1>
  <link rel="stylesheet" href="{{ asset('css/app_driver.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
  @yield('script_head')
</head>

<body>
  <header class="bl_header">
    <div class="bl_header_inner">

      <div class="bl_header_inner_ttl">
        <a href="{{ route('driver.dashboard.index') }}">
          <img src="{{ asset('images/common/logo.png') }}" alt="代走屋">
        </a>
      </div>

      <div class="bl_header_inner_userBtn">
        @if (Auth::guard('drivers')->check())
          @if (checkDriverAccessFilter(route('driver.user.show', ['driver_id' => Auth::guard('drivers')->id()])))
            <a href="{{ route('driver.user.show', ['driver_id' => Auth::guard('drivers')->id()]) }}">
              {{ Auth::guard('drivers')->user()->name_sei }}
            </a>
          @endif
        @else
          <a href="{{ route('driver.login') }}">
            ログイン
          </a>
          {{-- <a href="{{ route('driver.register') }}">
            登録
          </a> --}}
        @endif
      </div>

      @if (Auth::guard('drivers')->check())
        <div class="bl_header_inner_menuBtn">
          <button class='js_menuBtn'>
            <i class="fa-solid fa-bars"></i>
          </button>
        </div>
      @endif

      @if (Auth::guard('drivers')->check())
        <div class="bl_header_inner_menu js_menu">
          <div class="bl_header_inner_menu_head">
            <h2>メニュー<br><span>menu</span></h2>
            @if (checkDriverAccessFilter(route('driver.user.show', ['driver_id' => Auth::guard('drivers')->id()])))
              <div class="bl_header_inner_head_userBtn">
                <a href="{{ route('driver.user.show', ['driver_id' => Auth::guard('drivers')->id()]) }}">
                  {{ Auth::guard('drivers')->user()->name_sei }}
                </a>
              </div>
            @endif
          </div>
          <nav>

            @php
              $menu_list = [
                  ['text' => 'ダッシュボード', 'href' => route('driver.dashboard.index'), 'path' => 'driver/dashboard', 'access_list' => []], //
                  ['text' => '稼働依頼一覧', 'href' => route('driver.driver_task.index', ['task_status_id' => [1]]), 'path' => 'driver/driver-task?task_status_id%5B0%5D=1', 'access_list' => []], //
                  ['text' => 'My稼働依頼一覧', 'href' => route('driver.driver_task.index', ['who' => 'myself']), 'path' => 'driver/driver-task?who=myself', 'access_list' => []], //
                  ['text' => 'スケジュール', 'href' => route('driver.driver_schedule.index'), 'path' => 'driver/driver-schedule', 'access_list' => []], //
                  ['text' => 'レビュー', 'href' => route('driver.driver_task_review.index'), 'path' => 'driver/driver-task-review', 'access_list' => []], //
                  ['text' => 'アカウント情報', 'href' => route('driver.user.show', ['driver_id' => Auth::guard('drivers')->id()]), 'path' => 'driver\/user', 'access_list' => []], //
              ];
            @endphp

            <ul>
              @foreach ($menu_list as $menu)
                @if (checkDriverAccessFilter($menu['href']))
                  <li>
                    <a href="{{ $menu['href'] }}" class="{{ preg_match('|^\/' . preg_quote($menu['path'], '/') . "(/.*)?$|", $_SERVER['REQUEST_URI']) ? 'active' : '' }}">
                      {!! $menu['text'] !!}
                    </a>
                  </li>
                @endif
              @endforeach
              <li>
                <form action="/driver/logout" method="POST" class="js_logout">
                  @csrf
                  <button type="submit">ログアウト</button>
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
  <script src="{{ asset('./js/app_driver.js') }}"></script>
  <script src="{{ asset('./js/libs/Functions/sidebar.js') }}"></script>

  @yield('script_bottom')
</body>

</html>
