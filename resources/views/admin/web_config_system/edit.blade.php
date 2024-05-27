@extends('layouts.admin.app')

@section('title')
  システム設定 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  <div class="bl_edit">
    <div class="bl_edit_inner">
      <div class="bl_edit_inner_head">
        <div class="bl_edit_inner_head_ttl">
          <h2>システム設定 編集</h2>
        </div>
      </div>

      <div class="bl_edit_inner_content">
        <section class="bl_edit_inner_content_data">
          <form action="{{ route('admin.web_config_system.update') }}" method="POST" class="js_confirm">
            @csrf
            <div class="bl_edit_inner_content_data_form">

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='email_notice'>通知を受け取るメールアドレス</label>
                <input type="text" name="email_notice" value="{{ old('email_notice', $config_system->email_notice) }}"
                  id="email_notice">
                <p class="el_error_msg">
                  @error('email_notice')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='email_from'>送信用メールアドレス(from) *メールサーバーからユーザーに送信するときに使われる</label>
                <input type="text" name="email_from" value="{{ old('email_from', $config_system->email_from ?? '') }}" id="email_from">
                <p class="el_error_msg">
                  @error('email_from')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='email_reply_to'>返信受付メールアドレス(reply-to)</label>
                <input type="text" name="email_reply_to" value="{{ old('email_reply_to', $config_system->email_reply_to ?? '') }}"
                  id="email_reply_to">
                <p class="el_error_msg">
                  @error('email_reply_to')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='email_no_reply'>返信不可メールアドレス(no-reply) *返信を受け付けない架空のfromメールアドレス</label>
                <input type="text" name="email_no_reply" value="{{ old('email_no_reply', $config_system->email_no_reply ?? '') }}"
                  id="email_no_reply">
                <p class="el_error_msg">
                  @error('email_no_reply')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='create_task_time_limit_from'>稼働依頼時、設定できる稼働日の範囲 何日後から何日後まで</label>
                <input type="text" name="create_task_time_limit_from"
                  value="{{ old('create_task_time_limit_from', $config_system->create_task_time_limit_from ?? '') }}" id="create_task_time_limit_from"
                  class="el_width12rem">

                <span>-</span>

                <input type="text" name="create_task_time_limit_to"
                  value="{{ old('create_task_time_limit_to', $config_system->create_task_time_limit_to ?? '') }}"
                  id="create_task_time_limit_to" class="el_width12rem">
                <p class="el_error_msg">
                  @error('create_task_time_limit_from')
                    {{ $message }}
                  @enderror
                </p>
                <p class="el_error_msg">
                  @error('create_task_time_limit_to')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='create_task_hour_limit'>登録できる稼働依頼の日付範囲に時間の指定。 何時まで登録可能か。</label>
                <input type="text" name="create_task_hour_limit"
                  value="{{ old('create_task_hour_limit', $config_system->create_task_hour_limit ?? '') }}"
                  id="create_task_hour_limit">
                <p class="el_error_msg">
                  @error('create_task_hour_limit')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='task_time_out_later'>稼稼働日の何日前に「新規」の稼働を「時間切れ」にするか</label>
                <input type="text" name="task_time_out_later"
                  value="{{ old('task_time_out_later', $config_system->task_time_out_later ?? '') }}"
                  id="task_time_out_later">
                <p class="el_error_msg">
                  @error('task_time_out_later')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='register_request_token_time_limit'>登録申請トークンの有効期限(許可が出てから何時間後まで会員登録が有効か)</label>
                <input type="text" name="register_request_token_time_limit"
                  value="{{ old('register_request_token_time_limit', $config_system->register_request_token_time_limit ?? '') }}"
                  id="register_request_token_time_limit">
                <p class="el_error_msg">
                  @error('register_request_token_time_limit')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='default_price'>既定のシステム利用料金</label>
                <input type="number" name="default_price"
                  value="{{ old('default_price', $config_system->default_price ?? '') }}"
                  id="default_price">
                <p class="el_error_msg">
                  @error('default_price')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='default_emergency_price'>既定の緊急依頼料金</label>
                <input type="number" name="default_emergency_price"
                  value="{{ old('default_emergency_price', $config_system->default_emergency_price ?? '') }}"
                  id="default_emergency_price">
                <p class="el_error_msg">
                  @error('default_emergency_price')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='default_tax_rate'>既定の消費税率</label>
                <input type="text" name="default_tax_rate"
                  value="{{ old('default_tax_rate', $config_system->default_tax_rate ?? '') }}"
                  id="default_tax_rate">
                <p class="el_error_msg">
                  @error('default_tax_rate')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='default_stripe_payment_fee_rate'>既定のstripe決済手数料率</label>
                <input type="text" name="default_stripe_payment_fee_rate"
                  value="{{ old('default_stripe_payment_fee_rate', $config_system->default_stripe_payment_fee_rate ?? '') }}"
                  id="default_stripe_payment_fee_rate">
                <p class="el_error_msg">
                  @error('default_stripe_payment_fee_rate')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for='soon_price_time_limit_from'>緊急依頼が適用される時間の範囲 稼働日0時0分の±何時から何時</label>
                <input type="number" name="soon_price_time_limit_from"
                  value="{{ old('soon_price_time_limit_from', $config_system->soon_price_time_limit_from ?? '') }}" id="soon_price_time_limit_from"
                  class="el_width12rem">

                <span>-</span>

                <input type="number" name="soon_price_time_limit_to"
                  value="{{ old('soon_price_time_limit_to', $config_system->soon_price_time_limit_to ?? '') }}"
                  id="soon_price_time_limit_to" class="el_width12rem">
                <p class="el_error_msg">
                  @error('soon_price_time_limit_from')
                    {{ $message }}
                  @enderror
                </p>
                <p class="el_error_msg">
                  @error('soon_price_time_limit_to')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                <input type="submit" value="編集" class='c_btn'>
              </div>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
