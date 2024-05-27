@extends('layouts.delivery_office.app')

@section('title')
  ダッシュボード
@endsection

@section('content')

  <div class="bl_dashboard">
    <div class="bl_dashboard_inner">
      <div class="bl_dashboard_inner_link">
        <a href="{{ route('delivery_office.driver.index') }}">ドライバー一覧</a>
      </div>
      <div class="bl_dashboard_inner_link">
        <a href="{{ route('delivery_office.driver_task.create') }}">日付から配送を依頼する</a>
      </div>
      <div class="bl_dashboard_inner_link">
        <a href="{{ route('delivery_office.driver_task.index') }}">依頼履歴</a>
      </div>
      <div class="bl_dashboard_inner_link">
        <a href="{{ route('delivery_office.delivery_pickup_addr.index') }}">集荷先住所登録</a>
      </div>
      <div class="bl_dashboard_inner_link">
        <a href="{{ route('delivery_office.delivery_office_task_review.index') }}">レビュー</a>
      </div>
      <div class="bl_dashboard_inner_link">
        <a href="{{ route('delivery_office.user.index') }}">アカウント情報</a>
      </div>
    </div>
  </div>
@endsection
