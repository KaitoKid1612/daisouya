@extends('layouts.delivery_office.app')

@section('title')
  登録申請
@endsection

@section('content')
  <div class="bl_registerRequestCreate">
    <div class="bl_registerRequestCreate_inner">
      <div class="bl_registerRequestCreate_inner_head">
        <div class="bl_registerRequestCreate_inner_head_ttl">
          <h2>依頼者 登録申請<span>/ register request driver </span></h2>
        </div>
      </div>
      <div class="bl_registerRequestCreate_inner_content">
        <section class="bl_registerRequestCreate_inner_content_data">
          <form action="{{ route('delivery_office.register_request.store') }}" method="POST" class="js_form">
            @csrf
            <div class="bl_registerRequestCreate_inner_content_data_form">

              <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item el_select el_width_half'>
                <label for="delivery_company_id">配送会社<span class="u_red">*</span></label>
                <div class="c_form_select">
                  <select name="delivery_company_id" id="delivery_company_id">
                    <option disabled selected value="">
                      選択してください。
                    </option>
                    <option value="None" {{ 'None' == old('delivery_company_id') ? 'selected' : '' }}>
                      所属なし
                    </option>
                    @foreach ($company_list as $company)
                      <option value="{{ $company->id }}"
                        {{ $company->id == old('delivery_company_id') ? 'selected' : '' }}>
                        {{ $company->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                @error('delivery_company_id')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item js_form_delivery_company_name">
                <label for="delivery_company_name">会社名<span class="u_red">*</span></label>
                <input type="text" name='delivery_company_name' id='delivery_company_name' value="{{ old('delivery_company_name', $office->delivery_company_name ?? '') }}">
                <p class="el_error_msg">
                  @error('delivery_company_name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item'>
                <label for="">営業所名・デポ名<span class="u_red">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}">
                @error('name')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_registerRequestCreate_inner_content_data_form_caption'>
                <h3>営業所住所</h3>
              </div>

              <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item'>
                <label for="">郵便番号<span class="u_red">*</span></label>
                <input type="text" name="post_code1" id="post_code1" value="{{ old('post_code1') }}" class='el_width6rem'>

                <span>-</span>

                <input type="text" name="post_code2" id="post_code2" value="{{ old('post_code2') }}" class='el_width6rem'>

                <p class="el_error_msg">
                  @error('post_code1')
                    {{ $message }}
                  @enderror
                </p>
                <p class="el_error_msg">
                  @error('post_code2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half">
                <label for="addr1_id">都道府県<span class="u_red">*</span></label>
                <div class="c_form_select">
                  <select name="addr1_id" id="addr1_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($prefecture_list as $prefecture)
                      <option value="{{ $prefecture->id }}" {{ old('addr1_id') == $prefecture->id ? 'selected' : '' }}>
                        {{ $prefecture->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <p class="el_error_msg">
                  @error('addr1_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item'>
                <label for="addr2">市区町村<span class="u_red">*</span></label>
                <input type="text" name="addr2" id="addr2" value="{{ old('addr2') }}">
                @error('addr2')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item'>
                <label for="addr3">丁目 番地 号<span class="u_red">*</span></label>
                <input type="text" name="addr3" id="addr3" value="{{ old('addr3') }}">
                @error('addr3')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item'>
                <label for="addr4">建物名 部屋番号</label>
                <input type="text" name="addr4" id="addr4" value="{{ old('addr4') }}">
                @error('addr4')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class='bl_registerRequestCreate_inner_content_data_form_caption'>
                <h3>担当者 情報</h3>
              </div>

              <div class="c_form_flex">
                <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half'>
                  <label for="manager_name_sei">担当者名 姓<span class="u_red">*</span></label>
                  <input type="text" name="manager_name_sei" id="manager_name_sei" value="{{ old('manager_name_sei') }}">
                  @error('manager_name_sei')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half'>
                  <label for="manager_name_mei">担当者名 名<span class="u_red">*</span></label>
                  <input type="text" name="manager_name_mei" id="manager_name_mei" value="{{ old('manager_name_mei') }}">
                  @error('manager_name_mei')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>
              </div>

              <div class="c_form_flex">
                <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half'>
                  <label for="">担当者名 姓 (カナ)<span class="u_red">*</span></label>
                  <input type="text" name="manager_name_sei_kana" id="manager_name_sei_kana" value="{{ old('manager_name_sei_kana') }}">
                  @error('manager_name_sei_kana')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>

                <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half'>
                  <label for="">担当者名 名 (カナ)<span class="u_red">*</span></label>
                  <input type="text" name="manager_name_mei_kana" id="manager_name_mei_kana" value="{{ old('manager_name_mei_kana') }}">
                  @error('manager_name_mei_kana')
                    <p class="el_error_msg">{{ $message }}</p>
                  @enderror
                </div>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="email">担当者 メールアドレス<span class="u_red">*</span></label>
                <input type="text" name='email' id='email' value="{{ old('email') }}">
                <p class="el_error_msg">
                  @error('email')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class='c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half'>
                <label for="">担当者 電話番号</label>
                <input type="number" name="manager_tel" id="manager_tel" value="{{ old('manager_tel') }}">
                @error('manager_tel')
                  <p class="el_error_msg">{{ $message }}</p>
                @enderror
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="message">その他メッセージ</label>
                <textarea name="message" id="message">{{ old('message') }}</textarea>
                <p class="el_error_msg">
                  @error('message')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="bl_registerRequestCreate_inner_content_data_form_link">
                <a href="{{ route('guest.web_terms_service.index', [
                    'type' => 'office',
                ]) }}">利用規約</a>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_terms_service">
                <div class="c_form_checkbox">
                  <input type="checkbox" name="terms_service" id="terms_service">
                  <label for="terms_service">利用規約に同意</label>
                </div>
                <p class="el_error_msg">
                  @error('terms_service')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_submit">
                <input type="submit" value="申請する" class='c_btn js_submit'>
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

        $delivery_company_id = document.getElementById('delivery_company_id');
        $name = document.getElementById('name');
        $name_sei = document.getElementById('manager_name_sei');
        $name_mei = document.getElementById('manager_name_mei');
        $name_sei_kana = document.getElementById('manager_name_sei_kana');
        $name_mei_kana = document.getElementById('manager_name_mei_kana');
        $email = document.getElementById('email');
        $post_code1 = document.getElementById('post_code1');
        $post_code2 = document.getElementById('post_code2');
        $addr1_id = document.getElementById('addr1_id');
        $addr2 = document.getElementById('addr2');
        $addr3 = document.getElementById('addr3');
        $addr4 = document.getElementById('addr4');
        $tel = document.getElementById('manager_tel');

        $delivery_company_id.value = '1';
        $name.value = 'test';
        $name_sei.value = 'test';
        $name_mei.value = 'test';
        $name_sei_kana.value = 'カナ';
        $name_mei_kana.value = 'カナ';
        $email.value = 'hieuprofun@gmail.com';
        $post_code1.value = '123';
        $post_code2.value = '1234';
        $addr1_id.options[10].selected = true;
        $addr2.value = 'ぐんまー市';
        $addr3.value = '1丁目 1番地';
        $addr4.value = 'マンション101';
        $tel.value = '1029384756';
      });
    </script>
  @endif
@endsection

@section('script_bottom')
  <script>
    document.addEventListener('DOMContentLoaded', function() {

      /**
       * サブミットするのに利用規約の同期を必須にする。
       */
      let $form = document.querySelector('.js_form');
      let $terms_service = document.getElementById('terms_service'); // 利用規約
      let $submit = document.querySelector('.js_submit'); // サブミット

      is_cheked_terms_service() // 初期化

      // チェックボックスに変更があれば実行
      $terms_service.addEventListener('change', () => {
        is_cheked_terms_service();
      });

      // 利用規約の状態によってサブミットの状態を切り替え
      function is_cheked_terms_service() {
        if ($terms_service.checked) {
          $submit.classList.remove('js_submit_disable')
        } else {
          $submit.classList.add('js_submit_disable')
        }
      }

      // 利用規約に同意がなければ、サブミットを無効化
      $form.addEventListener('submit', (e) => {
        if (!$terms_service.checked) {
          e.preventDefault();
          console.log('submit無効');
        }
      });
    });
  </script>
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
