@extends('layouts.driver.app')

@section('title')
  パスワード再設定
@endsection

@section('content')
  <div class='bl_login'>
    <div class='bl_login_inner'>
      <form method="POST" action="{{ route('driver.password.update') }}" class="js_confirm">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">


        <div class="bl_login_inner_form_item">
          <label for="email">メールアドレス</label>
          <input id="email" type="text" name="email" value="{{ old('email', $request->email) }}" />
          @error('email')
            <p class="el_error_msg">{{ $message }}</p>
          @enderror
        </div>

        <div class="bl_login_inner_form_item">
          <label for="">再設定パスワード</label>
          <input type="password" name="password">
          @error('password')
            <p class="el_error_msg">{{ $message }}</p>
          @enderror
        </div>

        <div class="bl_login_inner_form_item">
          <label for="">再設定パスワード 確認</label>
          <input id="password_confirmation"
            type="password"
            name="password_confirmation" />
        </div>

        <div class="bl_login_inner_form_submit">
          <input type="submit" value="パスワード再設定">
        </div>

    </div>
  </div>
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
