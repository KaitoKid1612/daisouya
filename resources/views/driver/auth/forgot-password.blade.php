@extends('layouts.driver.app')

@section('title')
  パスワード忘れ
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class='bl_login'>
    <div class='bl_login_inner'>
      <form method="POST" action="{{ route('driver.password.email') }}">
        @csrf

        <div class="bl_login_inner_form_item">
          <label for="email">メールアドレス</label>
          <input type="text" value="{{ old('email') }}" name="email">
          @error('email')
            <p class="el_error_msg">{{ $message }}</p>
          @enderror
        </div>

        <div class="bl_login_inner_form_submit">
          <input type="submit" value="パスワード再設定メール送信">
        </div>
      </form>
    </div>
  </div>
@endsection
