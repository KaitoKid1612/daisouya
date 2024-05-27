@extends('layouts.driver.app')

@section('title')
  ログイン
@endsection

@section('content')
  {{-- メッセージ --}}
  @if (session('msg_new_password') || session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg_new_password') ?? '' }}
      </p>
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif


  <div class='bl_login'>
    <div class='bl_login_inner'>
      <h2>ドライバーログイン</h2>
      <form method="POST" action="{{ route('driver.login') }}">
        @csrf

        <div class="bl_login_inner_form_item">
          <label for="email">メールアドレス</label>
          <input type="text" value="{{ old('email') }}" name="email">
          @error('email')
            <p class="el_error_msg">{{ $message }}</p>
          @enderror
        </div>
        <div class="bl_login_inner_form_item">
          <label for="password">パスワード</label>
          <input type="password" name="password">
          @error('password')
            <p class="el_error_msg">{{ $message }}</p>
          @enderror
        </div>
        <div class="bl_login_inner_form_submit">
          <input type="submit" value="ログイン">
        </div>

        {{-- <label for="remember_me" class="inline-flex items-center">
          remember:
          <input id="remember_me" type="checkbox" name="remember">
        </label> --}}
      </form>
      <div class="bl_login_inner_link">
        <div class="bl_login_inner_link_item">
          <a href="{{ route('driver.register_request.create') }}">会員登録申請はこちら</a>
        </div>
        <div class="bl_login_inner_link_item">
          <a href="{{ route('driver.password.request') }}">パスワードを忘れた場合はこちら</a>
        </div>
        <div class="bl_login_inner_link_item">
          <a href="{{ route('delivery_office.login') }}">依頼者の方はこちら</a>
        </div>
      </div>
    </div>
  </div>
@endsection
