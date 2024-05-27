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
          <h2>お問い合わせ 確認</h2>
        </div>
      </div>

      <div class="bl_contactCreate_inner_content">
        <section class="bl_contactCreate_inner_content_data">
          <form action="{{ route('guest.web_contact.store') }}" method="POST" class="js_confirm">
            @csrf
            <div class="bl_contactCreate_inner_content_data_form">
              {{-- ・アカウントタイプ
              依頼者かドライバーか非会員 --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width30rem">
                <label for="user_type_id">ユーザータイプ</label>
                <div class="c_form_select">
                  <select name="user_type_id" id="user_type_id" class="el_readOnly">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($user_type_list as $user_type)
                      <option value="{{ $user_type->id }}"
                        {{ $request['user_type_id'] == $user_type->id ? 'selected' : '' }}>
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
                <input type="text" name="name_sei" id="name_sei" value="{{ $request['name_sei'] }}" class="el_readOnly">
                @error('name_sei')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width_half el_inline_block">
                <label for="name_mei">名:</label>
                <input type="text" name="name_mei" id="name_mei" value="{{ $request['name_mei'] }}" class="el_readOnly">
                @error('name_mei')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width_half el_inline_block">
                <label for="name_sei_kana">姓(カナ):</label>
                <input type="text" name="name_sei_kana" id="name_sei_kana" value="{{ $request['name_sei_kana'] }}" class="el_readOnly">
                @error('name_sei_kana')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width_half el_inline_block">
                <label for="name_mei_kana">名(カナ):</label>
                <input type="text" name="name_mei_kana" id="name_mei_kana" value="{{ $request['name_mei_kana'] }}" class="el_readOnly">
                @error('name_mei_kana')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              {{-- ・メールアドレス --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width30rem">
                <label for="email">メールアドレス(会員の方は登録しているメールアドレス):</label>
                <input type="text" name="email" id="email" value="{{ $request['email'] }}" class="el_readOnly">
                @error('email')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width30rem">
                <label for="tel">電話番号:</label>
                <input type="text" name="tel" id="tel" value="{{ $request['tel'] }}" class="el_readOnly">
                @error('tel')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              {{-- ・要件
              質問要望、報告 --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_width30rem">
                <label for="web_contact_type_id">用件</label>
                <div class="c_form_select">
                  <select name="web_contact_type_id" id="web_contact_type_id" class="el_readOnly">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($web_contact_type_list as $web_contact_type)
                      <option value="{{ $web_contact_type->id }}"
                        {{ $request['web_contact_type_id'] == $web_contact_type->id ? 'selected' : '' }}>
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
                <input type="text" name="title" id="title" value="{{ $request['title'] }}" class="el_readOnly">
                @error('title')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              {{-- ・内容 --}}
              <div class="c_form_item bl_contactCreate_inner_content_data_form_item">
                <label for="text">内容:</label>
                <textarea name="text" id="text" class="el_readOnly">{{ $request['text'] }}</textarea>
                @error('text')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_confirm">
                <div class="c_form_checkbox">
                  <input type="checkbox" name="" id="confirm" class='js_check'>
                  <label for="confirm">上記の内容を確認しました。</label>
                </div>
              </div>


              <div class="c_form_item bl_contactCreate_inner_content_data_form_item el_submit">
                <input type="submit" value="送信" class='c_btn js_submit'>
              </div>

            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script>
    /**
     * サブミットするのに、確認チェックを必要とする。
     */
    $e_confirm = document.querySelector('.js_confirm'); // フォーム
    $e_check = document.querySelector('.js_check'); // サブミット前確認チェックボックス
    $e_submit = document.querySelector('.js_submit'); // サブミット


    $e_submit.classList.add('js_eventNone'); // サブミット無効

    // チェックの状態でサブミットを可否。
    $e_check.addEventListener('change', (e) => {
      if ($e_check.checked) {
        $flg_check = $e_check.checked;
        $e_submit.classList.remove('js_eventNone');
      } else {
        $e_submit.classList.add('js_eventNone');
      }
    });

    // チェックしていなければ、サブミットしない。
    $e_confirm.addEventListener('submit', (event) => {
      if (!$e_check.checked) {
        event.preventDefault();
      }
    });
  </script>
  </style>
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
