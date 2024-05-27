@extends('layouts.driver.app')

@section('title')
  アカウント編集
@endsection

@section('content')
  <div class="bl_registerRequestEdit">
    <div class="bl_registerRequestEdit_inner">
      <div class="bl_registerRequestEdit_inner_head">
        <div class="bl_registerRequestEdit_inner_head_ttl">
          <h2>ドライバー登録申請 パスワード登録<span>/ retister password</span></h2>
        </div>
      </div>
      <div class="bl_registerRequestEdit_inner_content">
        @if ($is_register_request)
          <form action="{{ route('driver.register_request.update') }}" method="POST" class="js_confirm">
            @csrf
            <input type="hidden" name="register_request_token" value="{{ $_GET['token'] ?? 'トークンなし' }}">

            <section class="bl_registerRequestEdit_inner_content_form">

              <div class="bl_registerRequestEdit_inner_content_form_item c_form_item">
                <label for="password">メールアドレス:</label>
                <input type="text" name="email" value="{{ $email }}" id="email" style="pointer-events: none">
                @error('email')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class="bl_registerRequestEdit_inner_content_form_item c_form_item">
                <label for="password">パスワード:</label>
                <input type="password" name="password" value="" id="password">
                @error('password')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>
              <div class="bl_registerRequestEdit_inner_content_form_item c_form_item">
                <label for="password_confirmation">確認用パスワード:</label>
                <input type="password" name="password_confirmation" value="" id="password_confirmation">
              </div>
              <div class="bl_registerRequestEdit_inner_content_form_item_submit c_form_submit">
                <input type="submit" value="パスワード登録">
              </div>
            </section>
          </form>
        @else
          パスワード登録が無効です。期限切れの可能性があります。
        @endif
      </div>
    </div>
  </div>


  <script>
    /* テストコード */
    // document.addEventListener('DOMContentLoaded', function() {

    //   $email = document.getElementById('email');
    //   $gender_id = document.getElementById('gender_id');
    //   $birthday = document.getElementById('birthday');
    //   $post1 = document.getElementById('post_code1');
    //   $post2 = document.getElementById('post_code2');
    //   $addr1_id = document.getElementById('addr1_id');
    //   $addr2 = document.getElementById('addr2');
    //   $addr3 = document.getElementById('addr3');
    //   $tel = document.getElementById('tel');
    //   $career = document.getElementById('career');
    //   $introduction = document.getElementById('introduction');

    //   $email.value = 'hello@test.test';
    //   $gender_id.options[2].selected = true;
    //   $birthday.value = '2000-01-01';
    //   $post1.value = 123;
    //   $post2.value = 4567;
    //   $addr1_id.options[10].selected = true;
    //   $addr2.value = 'ぐんまー市';
    //   $addr3.value = '1丁目 1番地 マンション101';
    //   $tel.value = '1029384756';
    //   $career.value = 'キャリアcareer経歴けいれき';
    //   $introduction.value = '紹介introductionしょうかいショウカイ';
    // });
  </script>
@endsection
@section('script_bottom')
  <script src="{{ asset('./js/libs/Functions/driver_task.js') }}"></script>
@endsection
