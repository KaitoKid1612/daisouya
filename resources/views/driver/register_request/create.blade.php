@extends('layouts.driver.app')

@section('title')
  登録申請
@endsection

@section('content')
  <div class="bl_registerRequestCreate">
    <div class="bl_registerRequestCreate_inner">
      <div class="bl_registerRequestCreate_inner_head">
        <div class="bl_registerRequestCreate_inner_head_ttl">
          <h2>ドライバーアカウント 登録申請<span>/ register request driver </span></h2>
        </div>
      </div>
      <div class="bl_registerRequestCreate_inner_content">
        <section class="bl_registerRequestCreate_inner_content_data">
          <form action="{{ route('driver.register_request.store') }}" method="POST" enctype="multipart/form-data" class="js_confirm js_form">
            @csrf

            <div class="bl_registerRequestCreate_inner_content_data_form">

              <div class="c_form_flex">
                <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half">
                  <label for="name_sei">姓<span class="u_red">*</span></label>
                  <input type="text" name='name_sei' id='name_sei' value="{{ old('name_sei') }}">
                  <p class="el_error_msg">
                    @error('name_sei')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half">
                  <label for="name_mei">名<span class="u_red">*</span></label>
                  <input type="text" name='name_mei' id='name_mei' value="{{ old('name_mei') }}">
                  <p class="el_error_msg">
                    @error('name_mei')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_flex">
                <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half">
                  <label for="name_sei_kana">姓 (カナ)<span class="u_red">*</span></label>
                  <input type="text" name='name_sei_kana' id='name_sei_kana' value="{{ old('name_sei_kana') }}">
                  <p class="el_error_msg">
                    @error('name_sei_kana')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half">
                  <label for="name_mei_kana">名 (カナ)<span class="u_red">*</span></label>
                  <input type="text" name='name_mei_kana' id='name_mei_kana' value="{{ old('name_mei_kana') }}">
                  <p class="el_error_msg">
                    @error('name_mei_kana')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="email">メールアドレス<span class="u_red">*</span></label>
                <input type="text" name='email' id='email' value="{{ old('email') }}">
                <p class="el_error_msg">
                  @error('email')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half">
                <label for="">性別<span class="u_red">*</span></label>
                <div class="c_form_select">
                  <select name="gender_id" id="gender_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($gender_list as $gender)
                      <option value="{{ $gender->id }}" {{ old('gender_id') == $gender->id ? 'selected' : '' }}>
                        {{ $gender->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <p class="el_error_msg">
                  @error('gender_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half">
                <label for="birthday">生年月日<span class="u_red">*</span></label>
                <input type="date" name='birthday' id='birthday' value="{{ old('birthday') }}">
                <p class="el_error_msg">
                  @error('birthday')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="post_code1">郵便番号<span class="u_red">*</span></label>
                <input type="text" name="post_code1" value="{{ old('post_code1') }}" id="post_code1"
                  class="el_width6rem">

                <span>-</span>

                <input type="text" name="post_code2" value="{{ old('post_code2') }}" id="post_code2"
                  class="el_width6rem">
                <p class="el_error_msg">
                  @error('post_code1')
                    {{ $message }}
                  @enderror
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

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="addr2">住所(市区町村)<span class="u_red">*</span></label>
                <input type="text" name='addr2' id='addr2' value="{{ old('addr2') }}">
                <p class="el_error_msg">
                  @error('addr2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="addr3">住所(丁目 番地 号)<span class="u_red">*</span></label>
                <input type="text" name='addr3' id='addr3' value="{{ old('addr3') }}">
                <p class="el_error_msg">
                  @error('addr3')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="addr4">住所(建物名 部屋番号)</label>
                <input type="text" name='addr4' id='addr4' value="{{ old('addr4') }}">
                <p class="el_error_msg">
                  @error('addr4')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_half">
                <label for="tel">電話番号<span class="u_red">*</span></label>
                <input type="text" name='tel' id='tel' value="{{ old('tel') }}">
                <p class="el_error_msg">
                  @error('tel')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="avatar">顔写真<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='avatar' id='avatar' class="input_custom">
                <img id="avatar-preview" width="250" />
                <p class="el_error_msg">
                  @error('avatar')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="bank">支払い先の口座情報<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='bank' id='bank' class="input_custom">
                <img id="bank-preview" width="250" />
                <p class="el_error_msg">
                  @error('bank')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="driving_license_front">運転免許証の表<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='driving_license_front' id='driving_license_front' class="input_custom">
                <img id="driving_license_front-preview" width="250" />
                <p class="el_error_msg">
                  @error('driving_license_front')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="driving_license_back">運転免許証の裏<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='driving_license_back' id='driving_license_back' class="input_custom">
                <img id="driving_license_back-preview" width="250" />
                <p class="el_error_msg">
                  @error('driving_license_back')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="auto_insurance">自賠責保険<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='auto_insurance' id='auto_insurance' class="input_custom">
                <img id="auto_insurance-preview" width="250" />
                <p class="el_error_msg">
                  @error('auto_insurance')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="voluntary_insurance">任意保険<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='voluntary_insurance' id='voluntary_insurance' class="input_custom">
                <img id="voluntary_insurance-preview" width="250" />
                <p class="el_error_msg">
                  @error('voluntary_insurance')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="inspection_certificate">車検証<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='inspection_certificate' id='inspection_certificate' class="input_custom">
                <img id="inspection_certificate-preview" width="250" />
                <p class="el_error_msg">
                  @error('inspection_certificate')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="license_plate_front">ナンバープレートを含めた自動車の画像(前方)<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='license_plate_front' id='license_plate_front' class="input_custom">
                <img id="license_plate_front-preview" width="250" />
                <p class="el_error_msg">
                  @error('license_plate_front')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item el_width_margin">
                <label for="license_plate_back">ナンバープレートを含めた自動車の画像(後方)<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='license_plate_back' id='license_plate_back' class="input_custom">
                <img id="license_plate_back-preview" width="250" />
                <p class="el_error_msg">
                  @error('license_plate_back')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="career">経歴<span class="u_red">*</span></label>
                <textarea name="career" id="career">{{ old('career') }}</textarea>
                <p class="el_error_msg">
                  @error('career')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_registerRequestCreate_inner_content_data_form_item">
                <label for="introduction">
                  紹介文
                  <span class="u_red">*</span>
                  <span>30文字以上で入力してください。</span>
                </label>
                <textarea name="introduction" id="introduction">{{ old('introduction') }}</textarea>
                <p class="el_error_msg">
                  @error('introduction')
                    {{ $message }}
                  @enderror
                </p>
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
                    'type' => 'driver',
                ]) }}">ドライバー利用規約</a>
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
                <input type="submit" value="申請する" id="submit" class='c_btn'>
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

        $name_sei = document.getElementById('name_sei');
        $name_mei = document.getElementById('name_mei');
        $name_sei_kana = document.getElementById('name_sei_kana');
        $name_mei_kana = document.getElementById('name_mei_kana');
        $email = document.getElementById('email');
        $gender_id = document.getElementById('gender_id');
        $birthday = document.getElementById('birthday');
        $post_code1 = document.getElementById('post_code1');
        $post_code2 = document.getElementById('post_code2');
        $addr1_id = document.getElementById('addr1_id');
        $addr2 = document.getElementById('addr2');
        $addr3 = document.getElementById('addr3');
        $addr4 = document.getElementById('addr4');
        $tel = document.getElementById('tel');
        $career = document.getElementById('career');
        $introduction = document.getElementById('introduction');

        $name_sei.value = 'test';
        $name_mei.value = 'test';
        $name_sei_kana.value = 'カナ';
        $name_mei_kana.value = 'カナ';
        $email.value = 'hieuprofun@gmail.com';
        $gender_id.options[2].selected = true;
        $birthday.value = '2000-01-01';
        $post_code1.value = '123';
        $post_code2.value = '1234';
        $addr1_id.options[10].selected = true;
        $addr2.value = 'ぐんまー市';
        $addr3.value = '1丁目 1番地';
        $addr3.value = 'マンション101';
        $tel.value = '1029384756';
        $career.value = 'キャリアcareer経歴けいれき';
        $introduction.value = '紹介introductionしょうかいショウカイ 紹介introductionしょうかいショウカイ';
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
      let $submit = document.getElementById('submit'); // サブミット

      is_cheked_terms_service() // 初期化
      // console.log($terms_service.checked);

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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Common function
      function renderImage(file, preview) {
        const previewElement = document.querySelector(`#${preview}`)

        console.log(preview, file);

        const reader = new FileReader();
        reader.onload = function(e) {
          previewElement.setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(file);
      }
      
      // Elements
      const inputs = document.querySelectorAll('.input_custom');

      inputs.forEach((item) => {
        const field = item.getAttribute('name');

        item.addEventListener('change', (e) => {
          const file = e.target.files[0];
          renderImage(file, `${field}-preview`);
        })
      })
    })
  </script>
@endsection
