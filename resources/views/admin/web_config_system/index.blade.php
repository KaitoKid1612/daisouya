@extends('layouts.admin.app')

@section('title')
  システム設定
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif


  <div class="bl_show">
    <div class="bl_show_inner">
      <div class="bl_show_inner_head">
        <div class="bl_show_inner_head_ttl">
          <h2>システム設定</h2>
        </div>
      </div>

      <div class="bl_show_inner_content">
        <div class="bl_show_inner_content_handle">
          <div class="bl_show_inner_content_handle_item">
            <a href="{{ route('admin.web_config_system.edit') }}" class="c_btn">編集</a>
          </div>
        </div>
        <section class="bl_show_inner_content_data">
          <dl>
            <dt>通知を受け取るメールアドレス</dt>
            <dd>{{ $config_system->email_notice ?? '' }}</dd>
          </dl>

          <dl>
            <dt>送信用メールアドレス(from) *メールサーバーからユーザーに送信するときに使われる</dt>
            <dd>{{ $config_system->email_from ?? '' }}</dd>
          </dl>

          <dl>
            <dt>返信受付メールアドレス(reply-to)</dt>
            <dd>{{ $config_system->email_reply_to ?? '' }}</dd>
          </dl>

          <dl>
            <dt>返信不可メールアドレス(no-reply) *返信を受け付けない架空のfromメールアドレス</dt>
            <dd>{{ $config_system->email_no_reply ?? '' }}</dd>
          </dl>

          <dl>
            <dt>稼働依頼時、設定できる稼働日の範囲</dt>
            <dd>{{ $config_system->create_task_time_limit_from ?? '' }}日後から</dd>
            <dd>{{ $config_system->create_task_time_limit_to ?? '' }}日後まで</dd>
          </dl>

          <dl>
            <dt>設定できる稼働日に時間指定。当日何時まで登録可能か。</dt>
            <dd>{{ $config_system->create_task_hour_limit ?? '' }}時</dd>
          </dl>
          <dl>
            <dt>稼稼働日の何日前に「新規」の稼働を「時間切れ」にするか</dt>
            <dd>{{ $config_system->task_time_out_later ?? '' }}</dd>
          </dl>

          <dl>
            <dt>登録申請トークンの有効期限(許可が出てから何時間後まで会員登録が有効か)</dt>
            <dd>{{ $config_system->register_request_token_time_limit ?? '' }}時間後まで</dd>
          </dl>

          <dl>
            <dt>既定の税率</dt>
            <dd>{{ $config_system->default_tax_rate ?? '' }}%</dd>
          </dl>

          <dl>
            <dt>既定のシステム利用料金</dt>
            <dd>{{ $config_system->default_price ?? '' }}円</dd>
          </dl>

          <dl>
            <dt>既定の緊急依頼料金</dt>
            <dd>&plus;{{ $config_system->default_emergency_price ?? '' }}円</dd>
          </dl>

          <dl>
            <dt>既定の決済手数料</dt>
            <dd>{{ $config_system->default_stripe_payment_fee_rate ?? '' }}%</dd>
          </dl>

          <dl>
            <dt>緊急依頼の範囲。 稼働日0時0分±何時~稼働日0時0分±何時まで &lt;24時間表記(稼働前日)&gt;</dt>
            <dd>{{ $config_system->soon_price_time_limit_from ?? '' }} &lt;{{24 + $config_system->soon_price_time_limit_from ?? '' }}時から&gt;</dd>
            <dd>{{ $config_system->soon_price_time_limit_to ?? '' }} &lt;{{ 24 + $config_system->soon_price_time_limit_to ?? '' }}時まで&gt;</dd>
          </dl>

          <dl>
            <dt>作成日</dt>
            <dd>{{ $config_system->created_at }}</dd>
          </dl>

          <dl>
            <dt>更新日</dt>
            <dd>{{ $config_system->updated_at }}</dd>
          </dl>
        </section>
      </div>
    </div>
  </div>
@endsection
