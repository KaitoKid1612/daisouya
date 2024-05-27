@extends('layouts.guest.app')

@section('title')
  お問い合わせ
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_contactCreate">
    <div class="bl_contactCreate_inner">
      <div class="bl_contactCreate_inner_head">
        <div class="bl_contactCreate_inner_head_ttl">
          <h2>お問い合わせ</h2>
        </div>
      </div>

      <div class="bl_contactCreate_inner_content">
        <section class="bl_contactCreate_inner_content_data">
          <form action="{{ route('guest.web_contact.store') }}" method="POST" class="js_confirm">
            @csrf

            <input type="hidden" name="type" value="confirm">

            <div class="bl_contactCreate_inner_content_data_form">
              {{-- ・アカウントタイプ
              依頼者かドライバーか非会員 --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width30rem">
                <label for="user_type_id">ユーザータイプ</label>
                <div class="c_form_select">
                  <select name="user_type_id" id="user_type_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($user_type_list as $user_type)
                      <option value="{{ $user_type->id }}"
                        {{ old('user_type_id') == $user_type->id ? 'selected' : '' }}>
                        {{ $user_type->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                @error('user_type_id')
                  <p class="el_error_msg">
                    {{ $message }}
                  </p>
                @enderror
              </div>

              {{-- ・名前 --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width_half el_inline_block">
                <label for="name_sei">姓:</label>
                <input type="text" name="name_sei" id="name_sei" value="{{ old('name_sei') }}">
                @error('name_sei')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width_half el_inline_block">
                <label for="name_mei">名:</label>
                <input type="text" name="name_mei" id="name_mei" value="{{ old('name_mei') }}">
                @error('name_mei')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width_half el_inline_block">
                <label for="name_sei_kana">姓(カナ):</label>
                <input type="text" name="name_sei_kana" id="name_sei_kana" value="{{ old('name_sei_kana') }}">
                @error('name_sei_kana')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width_half el_inline_block">
                <label for="name_mei_kana">名(カナ):</label>
                <input type="text" name="name_mei_kana" id="name_mei_kana" value="{{ old('name_mei_kana') }}">
                @error('name_mei_kana')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              {{-- ・メールアドレス --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width30rem">
                <label for="email">メールアドレス(会員の方は登録しているメールアドレス):</label>
                <input type="text" name="email" id="email" value="{{ old('email') }}">
                @error('email')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width30rem">
                <label for="tel">電話番号:</label>
                <input type="text" name="tel" id="tel" value="{{ old('tel') }}">
                @error('tel')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              {{-- ・要件
              質問要望、報告 --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width30rem">
                <label for="web_contact_type_id">用件</label>
                <div class="c_form_select">
                  <select name="web_contact_type_id" id="web_contact_type_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($web_contact_type_list as $web_contact_type)
                      <option value="{{ $web_contact_type->id }}"
                        {{ old('web_contact_type_id') == $web_contact_type->id ? 'selected' : '' }}>
                        {{ $web_contact_type->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                @error('web_contact_type_id')
                  <p class="el_error_msg">
                    {{ $message }}
                  </p>
                @enderror
              </div>


              {{-- ・題目 --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item ">
                <label for="title">タイトル:</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}">
                @error('title')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              {{-- ・内容 --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item">
                <label for="text">内容:</label>
                <textarea name="text" id="text">{{ old('text') }}</textarea>
                @error('text')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>


              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_submit">
                <input type="submit" value="確認" class='c_btn'>
              </div>

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
      document.addEventListener('DOMContentLoaded', function() {
        $user_type_id = document.getElementById('user_type_id');
        $name_sei = document.getElementById('name_sei');
        $name_mei = document.getElementById('name_mei');
        $name_sei_kana = document.getElementById('name_sei_kana');
        $name_mei_kana = document.getElementById('name_mei_kana');

        $email = document.getElementById('email');
        $tel = document.getElementById('tel');
        $web_contact_type_id = document.getElementById('web_contact_type_id');
        $title = document.getElementById('title');
        $text = document.getElementById('text');

        $user_type_id.value = 2;
        $name_sei.value = 'Tsei';
        $name_mei.value = 'Tmei';
        $name_sei_kana.value = 'Tsei_kana';
        $name_mei_kana.value = 'Tmei_kana';
        $email.value = 'yamada@waocon.com';
        $tel.value = '0123456789';
        $web_contact_type_id.value = 1;
        $title.value = 'hello';
        $text.value = 'testtesttesttesttesttesttesttesttest';

      });
    </script>
  @endif
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
