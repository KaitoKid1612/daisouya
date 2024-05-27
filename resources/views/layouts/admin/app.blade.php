<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('images/favicons/favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/favicons/apple-touch-icon-180x180.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('images/favicons/android-chrome-192x192.png') }}">
  <title>代走屋 管理画面 @yield('title')</title>
  <meta name="viewport" content="width=device-width" initial-scale=1>
  <link rel="stylesheet" href="{{ asset('css/app_admin.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
  @yield('script_head')
</head>

<body>
  <header class="bl_header">
    <div class="bl_header_inner">

      <div class="bl_header_inner_ttl">
        <a href="{{ route('admin.dashboard.index') }}">
          <img src="{{ asset('images/common/logo.png') }}" alt="代走屋">
        </a>
      </div>

      <div class="bl_header_inner_userBtn">
        @if (Auth::guard('admins')->check())
          <a href="{{ route('admin.user.index') }}">
            {{ Auth::guard('admins')->user()->name ?? '' }}
          </a>
        @else
          <a href="{{ route('admin.login') }}">
            ログイン
          </a>
          {{-- <a href="{{ route('admin.register') }}">
            登録
          </a> --}}
        @endif
      </div>

      @if (Auth::guard('admins')->check())
        <div class="bl_header_inner_menuBtn">
          <button class='js_menuBtn'>
            <i class="fa-solid fa-bars"></i>
          </button>
        </div>
      @endif

      @if (Auth::guard('admins')->check())
        <div class="bl_header_inner_menu js_menu">
          <div class="bl_header_inner_menu_head">
            <h2>メニュー<br><span>menu</span></h2>
            <div class="bl_header_inner_head_userBtn">
              <a href="{{ route('admin.user.index') }}">
                {{ Auth::guard('admins')->user()->name ?? '' }}
              </a>
            </div>
          </div>
          <nav>

            @php
              $menu_list = [
                  ['text' => 'ダッシュボード', 'href' => route('admin.dashboard.index'), 'path' => 'admin/dashboard', 'permission' => []],
                  ['text' => '稼働依頼', 'href' => route('admin.driver_task.index'), 'path' => 'admin/driver-task', 'permission_list' => []],
                  ['text' => '配送会社', 'href' => route('admin.delivery_company.index'), 'path' => 'admin/delivery-company', 'permission_list' => []],
                  ['text' => '依頼者', 'href' => route('admin.delivery_office.index'), 'path' => 'admin/delivery-office', 'permission_list' => []],
                  ['text' => 'ドライバー', 'href' => route('admin.driver.index'), 'path' => 'admin/driver', 'permission_list' => []],
                  ['text' => 'ドライバースケジュール', 'href' => route('admin.driver_schedule.index'), 'path' => 'admin/driver-schedule', 'permission_list' => []],
                  ['text' => 'ドライバー登録営業所', 'href' => route('admin.driver_register_delivery_office.index'), 'path' => 'admin/driver-register-delivery-office', 'permission_list' => []],
                  ['text' => 'ドライバー登録営業所メモ', 'href' => route('admin.driver_register_delivery_office_memo.index'), 'path' => 'admin/driver-register-delivery-office-memo', 'permission_list' => []],
                  ['text' => '依頼者 集荷先住所', 'href' => route('admin.delivery_pickup_addr.index'), 'path' => 'admin/delivery-pickup-addr', 'permission_list' => []],
                  ['text' => 'レビュー', 'href' => route('admin.driver_task_review.index'), 'path' => 'admin/driver-task-review', 'permission_list' => [], 'is_child' => true, 'child_list' => [['text' => '依頼者', 'href' => route('admin.delivery_office_task_review.index'), 'path' => 'admin/driver-task-review/delivery-office', 'permission_list' => []], ['text' => 'ドライバー', 'href' => route('admin.driver_task_review.index'), 'path' => 'admin/driver-task-review/driver', 'permission_list' => []]]],
                  ['text' => '請求書', 'href' => route('admin.pdf_invoice.create'), 'path' => 'admin/pdf-invoice', 'permission_list' => []],
                  ['text' => '登録申請', 'href' => '', 'path' => 'admin/register-request', 'permission_list' => [], 'is_child' => true, 'child_list' => [['text' => '営業所', 'href' => route('admin.register_request_delivery_office.index'), 'path' => 'admin/register-request/delivery_office', 'permission_list' => []], ['text' => 'ドライバー', 'href' => route('admin.register_request_driver.index'), 'path' => 'admin/register-request/driver', 'permission_list' => []]]],
                  ['text' => 'お問い合わせ', 'href' => route('admin.web_contact.index'), 'path' => 'admin/contact', 'permission_list' => []],
                  ['text' => '管理者アカウント', 'href' => route('admin.user.index'), 'path' => 'admin/user', 'permission_list' => []],
                  ['text' => '基本設定', 'href' => route('admin.web_config_base.index'), 'path' => 'admin/base-config', 'permission_list' => [], 'is_child' => true, 'child_list' => [['text' => '基本設定(メイン)', 'href' => route('admin.web_config_base.index'), 'path' => 'admin/base-config', 'permission_list' => []], ['text' => '繁忙期カレンダー', 'href' => route('admin.web_busy_season.index'), 'path' => 'admin/base-config/busy-season', 'permission_list' => []]]],
                  ['text' => 'システム設定', 'href' => route('admin.web_config_system.index'), 'path' => 'admin/system-config', 'permission_list' => [], 'is_child' => true, 'child_list' => [['text' => 'システム設定(メイン)', 'href' => route('admin.web_config_system.index'), 'path' => 'admin/system-config', 'permission_list' => []], ['text' => '稼働依頼プラン', 'href' => route('admin.driver_task_plan.index'), 'path' => 'admin/system-config/driver-task-plan', 'permission_list' => []], ['text' => 'ドライバープラン', 'href' => route('admin.driver_plan.index'), 'path' => 'admin/system-config/driver-plan', 'permission_list' => []], ['text' => '稼働プランで対応可能ドライバープラン', 'href' => route('admin.driver_task_plan_allow_driver.index'), 'path' => 'admin/system-config/driver-task-plan-allow-driver', 'permission_list' => []]]],
                  ['text' => 'システム情報', 'href' => '', 'path' => 'admin/system-info', 'permission_list' => [], 'is_child' => true, 'child_list' => [['text' => 'システム情報', 'href' => route('admin.web_system_info.index'), 'path' => 'admin/system-info/phpinfo', 'permission_list' => []], ['text' => '決済ログ', 'href' => route('admin.web_payment_log.index'), 'path' => 'admin/system-info/log/payment', 'permission_list' => []], ['text' => '通知ログ', 'href' => route('admin.web_notice_log.index'), 'path' => 'admin/system-info/log/notice', 'permission_list' => []], ['text' => 'Redis', 'href' => route('admin.web_redis.index'), 'path' => 'admin/system-info/redis', 'permission_list' => []], ['text' => 'Job失敗ログ', 'href' => route('admin.web_failed_job.index'), 'path' => 'admin/system-info/failed-job', 'permission_list' => []]]],
              ];
            @endphp

            <ul class="bl_header_inner_menu_parent">
              @foreach ($menu_list as $menu)
                @if (isset($menu['is_child']) && $menu['is_child'] === true)
                  <li class='bl_header_inner_menu_parent_li js_menu_child'>
                    <button class='js_show_child_btn el_menu_link el_accordionBtn {{ preg_match('|^\/' . preg_quote($menu['path'], '/') . '|', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ? 'active' : '' }}'>{{ $menu['text'] }}</button>
                    <ul class="bl_header_inner_menu_parent_li_child js_menu_child_ul">
                      @foreach ($menu['child_list'] as $child)
                        <li class='js_menu_child_li'>
                          <a href="{{ $child['href'] }}" class="{{ preg_match('|^\/' . preg_quote($child['path'], '/') . "$|", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ? 'active' : '' }} el_menu_link">
                            {{ $child['text'] }}
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  </li>
                @else
                  <li class='bl_header_inner_menu_parent_li'>
                    <a href="{{ $menu['href'] }}" class="{{ preg_match('|^\/' . preg_quote($menu['path'], '/') . "(/.*)?$|", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ? 'active' : '' }} el_menu_link">
                      {{ $menu['text'] }}
                    </a>
                  </li>
                @endif
              @endforeach
              <li class="bl_header_inner_menu_parent_li">
                <form action="/admin/logout" method="POST" class="js_logout">
                  @csrf
                  <button type="submit" class="el_menu_link">ログアウト</button>
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
      <div class="bl_footer_inner_copyright">
        <p>Copyright &copy; 2022{{ date('Y') == '2022' ? '' : '-' . date('Y') }} 株式会社ケイズリング, All Rights Reserved.</p>
      </div>
    </div>
  </footer>
  <script src="{{ asset('./js/app_admin.js') }}"></script>
  <script src="{{ asset('./js/libs/Functions/sidebar.js') }}"></script>



  <script>
    // document.addEventListener("DOMContentLoaded", function() {
    // メニューのアコーディオン開閉
    (function() {
      let $show_child_btn_list = document.querySelectorAll('.js_menu_child'); // 子を含むメニューリスト
      $show_child_btn_list.forEach(($show_child_btn) => {
        let $child_btn = $show_child_btn.querySelector('.js_show_child_btn'); // 開閉テキストボタン
        let $menu_child_ul = $show_child_btn.querySelector('.js_menu_child_ul'); // 子のul要素

        $child_btn.addEventListener('click', () => {
          if ($menu_child_ul.classList.contains("js_active")) {
            $menu_child_ul.classList.remove("js_active");
            $child_btn.classList.remove("js_active");
          } else {
            $menu_child_ul.classList.add("js_active");
            $child_btn.classList.add("js_active");
          }
        }, true);
      });
    })();
    // });
  </script>
  @yield('script_bottom')
</body>

</html>
