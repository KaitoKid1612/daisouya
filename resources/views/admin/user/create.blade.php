@extends('layouts.admin.app')

@section('title')
  管理者 作成
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  <div class="bl_create">
    <div class="bl_create_inner">
      <div class="bl_create_inner_head">
        <div class="bl_create_inner_head_ttl">
          <h2>管理者 作成</h2>
        </div>
      </div>

      <div class="bl_create_inner_content">
        <section class="bl_create_inner_content_data">
          <form action="{{ route('admin.user.store') }}" method="POST" class="js_confirm">
            @csrf
            <div class="bl_create_inner_content_data_form">

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="">名前</label>
                <input type="text" name='name' id='name' value='{{ old('name') }}'>
                <p class="el_error_msg">
                  @error('name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="email">メールアドレス</label>
                <input type="text" name='email' id='email' value='{{ old('email') }}'>
                <p class="el_error_msg">
                  @error('email')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="password">パスワード</label>
                <input type="password" name='password' id='password'>
                <p class="el_error_msg">
                  @error('password')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="password_confirmation">パスワード確認用</label>
                <input type="password" name='password_confirmation' id='password_confirmation'>
                <p class="el_error_msg">
                  @error('password_confirmation')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_edit_inner_content_data_form_item">
                <label for="admin_permission_group_id">アクセス権限グループ</label>
                <div class="c_form_select">
                  <select name="admin_permission_group_id" id="admin_permission_group_id">
                    <option disabled selected>選択</option>
                    @foreach ($admin_permission_group_list as $group)
                      <option value="{{ $group->id }}">{{ $group->name ?? '' }}</option>
                    @endforeach
                  </select>
                </div>
              </div>


              <div class="c_form_item bl_create_inner_content_data_form_item el_submit">
                <input type="submit" value="作成" class='c_btn'>
              </div>

          </form>
        </section>
      </div>
    </div>
  </div>

  @if (config('app.env') === 'local')
    <script>
      /**
       *  テスト用フォーム自動入力
       * */
      // document.addEventListener('DOMContentLoaded', function() {
      //   $name = document.getElementById('name');
      //   $email = document.getElementById('email');
      //   $password = document.getElementById('password');
      //   $password_confirmation = document.getElementById('password_confirmation');

      //   $name.value = 'test管理者だよ';
      //   $email.value = 'test2@test.test';
      //   $password.value = 'test1234';
      //   $password_confirmation.value = 'test1234';
      // });
    </script>
  @endif
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
