@extends('layouts.admin.app')

@section('title')
  ダッシュボード
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_dashboard">
    <div class="bl_dashboard_inner">
      <section class="bl_dashboard_inner_content">
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver_task.index') }}">稼働依頼</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.delivery_company.index') }}">配送会社</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.delivery_office.index') }}">依頼者</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver.index') }}">ドライバー</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver_schedule.index') }}">ドライバースケジュール</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver_register_delivery_office.index') }}">ドライバー登録営業所</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver_register_delivery_office_memo.index') }}">ドライバー登録営業所メモ</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.delivery_pickup_addr.index') }}">依頼者 集荷先住所</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.delivery_office_task_review.index') }}">依頼者レビュー</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver_task_review.index') }}">ドライバーレビュー</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.pdf_invoice.create') }}">請求書</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.register_request_delivery_office.index') }}">営業所 登録申請</a>
        </div>

        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.delivery_office.unsubscribe') }}">退会申請（依頼者）</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver.unsubscribe') }}">退会申請（ドライバー）</a>
        </div>

        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.register_request_driver.index') }}">ドライバー 登録申請</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_contact.index') }}">お問い合わせ</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.user.index') }}">管理者アカウント</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_config_base.index') }}">基本設定</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_busy_season.index') }}">繁忙期カレンダー</a>
        </div>

        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_config_system.index') }}">システム設定</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver_task_plan.index') }}">稼働依頼プラン</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver_plan.index') }}">ドライバープラン</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.driver_task_plan_allow_driver.index') }}">稼働依頼プランで対応可能なドライバープラン</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_system_info.index') }}">システム情報</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_payment_log.index') }}">決済ログ</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_notice_log.index') }}">通知ログ</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_redis.index') }}">Redisログ</a>
        </div>
        <div class="bl_dashboard_inner_content_link">
          <a href="{{ route('admin.web_failed_job.index') }}">Job失敗ログ</a>
        </div>
      </section>
    </div>
  </div>
@endsection
