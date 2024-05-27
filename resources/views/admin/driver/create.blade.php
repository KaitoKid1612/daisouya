@extends('layouts.admin.app')

@section('title')
  ドライバー 作成
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
          <h2>ドライバー 作成</h2>
        </div>
      </div>

      <div class="bl_create_inner_content">
        <section class="bl_create_inner_content_data">
          <form action="{{ route('admin.driver.store') }}" method="POST" enctype="multipart/form-data" class="js_confirm">
            @csrf

            <div class="bl_create_inner_content_data_form">

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="">ドライバープラン</label>
                <div class="c_form_select">
                  <select name="driver_plan_id" id="driver_plan_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($driver_plan_list as $driver_plan)
                      <option value="{{ $driver_plan->id }}" {{ old('driver_plan_id') == $driver_plan->id ? 'selected' : '' }}>
                        {{ $driver_plan->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <p class="el_error_msg">
                  @error('driver_plan_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_file bl_create_inner_content_data_form_item">
                <label for="icon_img">アイコン画像</label>
                <input type="file" name="icon_img" accept="image/*" id="icon_img" class="js_icon_input">
                <img src="" alt="" id="js_file_icon_img">
              </div>

              <script>
                /* アイコン画像が設置されたとき、プレビューする */
                let $file_icon_img = document.getElementById('js_file_icon_img'); // 画像
                let $icon_input = document.querySelector('.js_icon_input'); //input

                $icon_input.addEventListener('change', (e) => {
                  const file = e.target.files;
                  const reader = new FileReader();
                  reader.readAsDataURL(file[0]);
                  reader.onload = () => {
                    $file_icon_img.src = reader.result;
                  };
                });
              </script>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="name_sei">姓</label>
                <input type="text" name='name_sei' id='name_sei' value="{{ old('name_sei') }}">
                <p class="el_error_msg">
                  @error('name_sei')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="name_mei">名</label>
                <input type="text" name='name_mei' id='name_mei' value="{{ old('name_mei') }}">
                <p class="el_error_msg">
                  @error('name_mei')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="name_sei_kana">姓 (カナ)</label>
                <input type="text" name='name_sei_kana' id='name_sei_kana' value="{{ old('name_sei_kana') }}">
                <p class="el_error_msg">
                  @error('name_sei_kana')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="name_mei_kana">名 (カナ)</label>
                <input type="text" name='name_mei_kana' id='name_mei_kana' value="{{ old('name_mei_kana') }}">
                <p class="el_error_msg">
                  @error('name_mei_kana')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="email">メールアドレス</label>
                <input type="text" name='email' id='email' value="{{ old('email') }}">
                <p class="el_error_msg">
                  @error('email')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="password">パスワード</label>
                <input type="password" name='password' id='password' value="{{ old('password') }}">
                <p class="el_error_msg">
                  @error('password')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="password_confirmation">パスワード確認用</label>
                <input type="password" name='password_confirmation' id='password_confirmation'
                  value="{{ old('password_confirmation') }}">
                <p class="el_error_msg">
                  @error('password_confirmation')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="">性別</label>
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


              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="birthday">生年月日</label>
                <input type="date" name='birthday' id='birthday' value="{{ old('birthday') }}">
                <p class="el_error_msg">
                  @error('birthday')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="post_code1">郵便番号</label>
                <input type="text" name="post_code1" value="{{ old('post_code1') }}" id="post_code1"
                  class="el_width12rem">

                <span>-</span>

                <input type="text" name="post_code2" value="{{ old('post_code2') }}" id="post_code2"
                  class="el_width12rem">
                <p class="el_error_msg">
                  @error('post_code1')
                    {{ $message }}
                  @enderror
                  @error('post_code2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="addr1_id">都道府県</label>
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

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="addr2">住所(市区町村)</label>
                <input type="text" name='addr2' id='addr2' value="{{ old('addr2') }}">
                <p class="el_error_msg">
                  @error('addr2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="addr3">住所(丁目 番地 号)</label>
                <input type="text" name='addr3' id='addr3' value="{{ old('addr3') }}">
                <p class="el_error_msg">
                  @error('addr3')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="addr4">住所(建物名 部屋番号)</label>
                <input type="text" name='addr4' id='addr4' value="{{ old('addr4') }}">
                <p class="el_error_msg">
                  @error('addr4')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="tel">電話番号</label>
                <input type="text" name='tel' id='tel' value="{{ old('tel') }}">
                <p class="el_error_msg">
                  @error('tel')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="avatar">顔写真<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='avatar' id='avatar' class="input_custom">
                <img id="avatar-preview" width="250" />
                <p class="el_error_msg">
                  @error('avatar')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="bank">支払い先の口座情報<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='bank' id='bank' class="input_custom">
                <img id="bank-preview" width="250" />
                <p class="el_error_msg">
                  @error('bank')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="driving_license_front">運転免許証の表<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='driving_license_front' id='driving_license_front' class="input_custom">
                <img id="driving_license_front-preview" width="250" />
                <p class="el_error_msg">
                  @error('driving_license_front')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="driving_license_back">運転免許証の裏<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='driving_license_back' id='driving_license_back' class="input_custom">
                <img id="driving_license_back-preview" width="250" />
                <p class="el_error_msg">
                  @error('driving_license_back')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="auto_insurance">自賠責保険<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='auto_insurance' id='auto_insurance' class="input_custom">
                <img id="auto_insurance-preview" width="250" />
                <p class="el_error_msg">
                  @error('auto_insurance')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="voluntary_insurance">任意保険<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='voluntary_insurance' id='voluntary_insurance' class="input_custom">
                <img id="voluntary_insurance-preview" width="250" />
                <p class="el_error_msg">
                  @error('voluntary_insurance')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="inspection_certificate">車検証<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='inspection_certificate' id='inspection_certificate' class="input_custom">
                <img id="inspection_certificate-preview" width="250" />
                <p class="el_error_msg">
                  @error('inspection_certificate')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="license_plate_front">ナンバープレートを含めた自動車の画像(前方)<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='license_plate_front' id='license_plate_front' class="input_custom">
                <img id="license_plate_front-preview" width="250" />
                <p class="el_error_msg">
                  @error('license_plate_front')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_width_margin">
                <label for="license_plate_back">ナンバープレートを含めた自動車の画像(後方)<span class="u_red">*</span></label>
                <input type="file" accept=".jpeg,.png,.jpg" name='license_plate_back' id='license_plate_back' class="input_custom">
                <img id="license_plate_back-preview" width="250" />
                <p class="el_error_msg">
                  @error('license_plate_back')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="career">経歴</label>
                <textarea name="career" id="career">{{ old('career') }}</textarea>
                <p class="el_error_msg">
                  @error('career')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="introduction">紹介文</label>
                <textarea name="introduction" id="introduction">{{ old('introduction') }}</textarea>
                <p class="el_error_msg">
                  @error('introduction')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item el_submit">
                <input type="submit" value="作成" class='c_btn'>
              </div>

              <div class="bl_create_inner_content_data_form_advice">
                <p>※こちらの操作を行うとユーザーへメールが送られます。</p>
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

        const nowDate = Date.now();

        $driver_plan_id = document.getElementById('driver_plan_id');
        $name_sei = document.getElementById('name_sei');
        $name_mei = document.getElementById('name_mei');
        $name_sei_kana = document.getElementById('name_sei_kana');
        $name_mei_kana = document.getElementById('name_mei_kana');
        $email = document.getElementById('email');
        $password = document.getElementById('password');
        $password_confirmation = document.getElementById('password_confirmation');
        $gender_id = document.getElementById('gender_id');
        $birthday = document.getElementById('birthday');
        $post_code1 = document.getElementById('post_code1');
        $post_code2 = document.getElementById('post_code2');
        $addr1_id = document.getElementById('addr1_id');
        $addr2 = document.getElementById('addr2');
        $addr3 = document.getElementById('addr3');
        $tel = document.getElementById('tel');
        $career = document.getElementById('career');
        $introduction = document.getElementById('introduction');

        $driver_plan_id.value = 2;
        $name_sei.value = 'test';
        $name_mei.value = 'test';
        $name_sei_kana.value = 'テスト';
        $name_mei_kana.value = 'テスト';
        $email.value = `test${nowDate}@amazon.com`;
        $password.value = 'test1234';
        $password_confirmation.value = 'test1234';
        $gender_id.options[2].selected = true;
        $birthday.value = '2000-01-01';
        $post_code1.value = '123';
        $post_code2.value = '1234';
        $addr1_id.options[10].selected = true;
        $addr2.value = 'ぐんまー市';
        $addr3.value = '1丁目 1番地 マンション101';
        $tel.value = '1029384756';
        $career.value = 'キャリアcareer経歴けいれき';
        $introduction.value = '紹介introductionしょうかいショウカイ';
      });
    </script>
  @endif
@endsection

@section('script_bottom')
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
