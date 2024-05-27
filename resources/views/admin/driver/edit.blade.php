@extends('layouts.admin.app')

@section('title')
  ドライバー 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach


  @if ($driver)
    <div class="bl_edit">
      <div class="bl_edit_inner">
        <div class="bl_edit_inner_head">
          <div class="bl_edit_inner_head_ttl">
            <h2>ドライバー 編集</h2>
          </div>
        </div>

        <div class="bl_edit_inner_content">
          <section class="bl_edit_inner_content_data">
            <form action="{{ route('admin.driver.update', ['driver_id' => $driver->id]) }}" method="POST" enctype="multipart/form-data" class="js_confirm">
              @csrf
              <div class="bl_edit_inner_content_data_form">

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="">ドライバープラン</label>
                  <div class="c_form_select">
                    <select name="driver_plan_id" id="driver_plan_id">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @foreach ($driver_plan_list as $driver_plan)
                        <option value="{{ $driver_plan->id }}" {{ $driver_plan->id == old('driver_plan_id', $driver->driver_plan_id) ? 'selected' : '' }}>
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

                <div class="c_form_file bl_edit_inner_content_data_form_item">
                  <label for="icon_img">アイコン画像</label>
                  <input type="file" name="icon_img" accept="image/*" id="icon_img" class="js_icon_input">
                  <img src="{{ route('storage_file.show', ['path' => $driver->icon_img]) }}" alt="" id="js_file_icon_img">
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

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="name_sei">姓</label>
                  <input type="text" name='name_sei' id='name_sei' value="{{ old('name_sei', $driver->name_sei ?? '') }}">
                  <p class="el_error_msg">
                    @error('name_sei')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="name_mei">名</label>
                  <input type="text" name='name_mei' id='name_mei' value="{{ old('name_mei', $driver->name_mei ?? '') }}">
                  <p class="el_error_msg">
                    @error('name_mei')
                      {{ $message }}
                    @enderror
                  </p>

                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="name_sei_kana">姓 (カナ)</label>
                  <input type="text" name='name_sei_kana' id='name_sei_kana'
                    value="{{ old('name_sei_kana', $driver->name_sei_kana ?? '') }}">
                  <p class="el_error_msg">
                    @error('name_sei_kana')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="name_mei_kana">名 (カナ)</label>
                  <input type="text" name='name_mei_kana' id='name_mei_kana'
                    value="{{ old('name_mei_kana', $driver->name_mei_kana ?? '') }}">
                  <p class="el_error_msg">
                    @error('name_mei_kana')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="email">メールアドレス</label>
                  <input type="text" name='email' id='email' value="{{ old('email', $driver->email ?? '') }}">
                  <p class="el_error_msg">
                    @error('email')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                @if ($driver && !$driver->deleted_at)
                  <div class="c_form_item bl_edit_inner_content_data_form_item">
                    <label for="password">パスワード</label>
                    <input type="password" name='password' id='password'>
                    <p class="el_error_msg">
                      @error('password')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>


                  <div class="c_form_item bl_edit_inner_content_data_form_item">
                    <label for="password_confirmation">パスワード確認用</label>
                    <input type="password" name='password_confirmation' id='password_confirmation'>
                    <p class="el_error_msg">
                      @error('password_confirmation')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                @endif


                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="">性別</label>
                  <div class="c_form_select">
                    <select name="gender_id" id="gender_id">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @foreach ($gender_list as $gender)
                        <option value="{{ $gender->id }}" {{ $gender->id == old('gender_id', $driver->gender_id) ? 'selected' : '' }}>
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


                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="birthday">生年月日</label>
                  <input type="date" name='birthday' id='birthday' value="{{ old('birthday', $driver->birthday ?? '') }}">
                  <p class="el_error_msg">
                    @error('birthday')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="post_code1">郵便番号</label>
                  <input type="text" name="post_code1" value="{{ old('post_code1', $driver->post_code1 ?? '') }}" id="post_code1"
                    class="el_width12rem">

                  <span>-</span>

                  <input type="text" name="post_code2" value="{{ old('post_code2', $driver->post_code2 ?? '') }}" id="post_code2"
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

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="addr1_id">都道府県</label>
                  <div class="c_form_select">
                    <select name="addr1_id" id="addr1_id" value="{{ old('addr1_id', $driver->addr1_id ?? '') }}">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @foreach ($prefecture_list as $prefecture)
                        <option value="{{ $prefecture->id }}"
                          {{ $prefecture->id == old('addr1_id', $driver->addr1_id) ? 'selected' : '' }}>
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

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="addr2">住所(市区町村)</label>
                  <input type="text" name='addr2' id='addr2' value="{{ old('addr2', $driver->addr2 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr2')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="addr3">住所(丁目 番地 号)</label>
                  <input type="text" name='addr3' id='addr3' value="{{ old('addr3', $driver->addr3 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr3')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="addr4">住所(建物名 部屋番号)</label>
                  <input type="text" name='addr4' id='addr4' value="{{ old('addr4', $driver->addr4 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr4')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="tel">電話番号</label>
                  <input type="text" name='tel' id='tel' value="{{ old('tel', $driver->tel ?? '') }}">
                  <p class="el_error_msg">
                    @error('tel')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="career">経歴</label>
                  <textarea name="career" id="career">{{ old('career', $driver->career ?? '') }}</textarea>
                  <p class="el_error_msg">
                    @error('career')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="introduction">紹介文</label>
                  <textarea name="introduction" id="introduction">{{ old('introduction', $driver->introduction ?? '') }}</textarea>
                  <p class="el_error_msg">
                    @error('introduction')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                  <input type="submit" value="取消し" class='c_btn remove'>

                  @if ($driver->deleted_at)
                    <input type="submit" value="退会" class='c_btn unsubscribe' style="margin-left: 10px">
                  @endif
                </div>

            </form>

            <div class="bl_officeEdit_popup" id="bl_officeEdit_popup_1">
              <p class="bl_officeEdit_popup_heading">退会前確認</p>

              <p class="bl_officeEdit_popup_question">退会してもよろしいですか？<br> この処理は取り消せません。</p>
              <p class="bl_officeEdit_popup_warning"></p>

              <div class="bl_officeEdit_popup_checkbox">
                <input
                  type="checkbox"
                  name="bl_officeEdit_popup_checkbox"
                  value="1"
                  id="bl_officeEdit_popup_input_1"
                >
                <label for="bl_officeEdit_popup_input_1">確認しました</label>
              </div>
              <div class="bl_officeEdit_popup_footer">
                <button class="bl_officeEdit_popup_footer_cancel bl_officeEdit_popup_footer_cancel_1">キャンセル</button>
                <button class="bl_officeEdit_popup_footer_ok bl_officeEdit_popup_footer_ok_1">OK</button>
              </div>
            </div>
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

        //   $email = document.getElementById('email');
        //   $password = document.getElementById('password');
        //   $password_confirmation = document.getElementById('password_confirmation');
        //   $gender_id = document.getElementById('gender_id');
        //   $birthday = document.getElementById('birthday');
        //   $addr1_id = document.getElementById('addr1_id');
        //   $addr2 = document.getElementById('addr2');
        //   $addr3 = document.getElementById('addr3');
        //   $tel = document.getElementById('tel');
        //   $career = document.getElementById('career');
        //   $introduction = document.getElementById('introduction');


        //   $email.value = 'hello@test.test';
        //   $password.value = 'test1234';
        //   $password_confirmation.value = 'test1234';
        //   $gender_id.options[2].selected = true;
        //   $birthday.value = '2000-01-01';
        //   $addr1_id.options[10].selected = true;
        //   $addr2.value = 'ぐんまー市';
        //   $addr3.value = '1丁目 1番地 マンション101';
        //   $tel.value = '1029384756';
        //   $career.value = 'キャリアcareer経歴けいれき';
        //   $introduction.value = '紹介introductionしょうかいショウカイ';
        // });
      </script>
    @endif
  @else
    <section class="bl_noData">
      <div class="bl_noData_inner">
        <p>
          このページは存在しません。
        </p>
      </div>
    </section>
  @endif
@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>

  @if ($driver && $driver->deleted_at)
    <script>
        // Elements
        const buttonUnsubscribe = document.querySelector(".unsubscribe");
        const buttonRemove = document.querySelector(".remove");

        const form = document.querySelector('.js_confirm');
        const popup = document.querySelector('.bl_officeEdit_popup');
        const buttonCreate = document.querySelector('.bl_officeEdit_inner_content_form_submit .create');
        const buttonClose = document.querySelector('.bl_officeEdit_popup_footer_cancel')
        const buttonConfirm = document.querySelector('.bl_officeEdit_popup_footer_ok')
        const checkbox = document.querySelector(`#bl_officeEdit_popup_input_1`);
        const headingPopup = document.querySelector('.bl_officeEdit_popup_heading')
        const questionPopup = document.querySelector('.bl_officeEdit_popup_question');

        let formAction = '';

        buttonRemove.addEventListener('click', (e) => {
          e.preventDefault();
          formAction = 'remove';
          questionPopup.innerHTML = ' 退会申請を取り消します。よろしいですか？';
          popup.classList.add('show');
        })

        buttonUnsubscribe?.addEventListener('click', (e) => {
            e.preventDefault();
            formAction = 'unsubscribe';
            popup.classList.add('show');
        });

        if (!checkbox.checked) {
          buttonConfirm.classList.add('disabled');
        }

        // Handle checkbox
        checkbox.addEventListener("change", (e) => {
          if (!checkbox.checked) {
            buttonConfirm.classList.add("disabled");
          } else {
            buttonConfirm.classList.remove("disabled");
          }
        });

        // Close popup
        buttonClose.addEventListener('click', (e) => {
          e.preventDefault();

          popup.classList.remove('show');
          buttonConfirm.classList.add("disabled");
          checkbox.checked = false;
        });

        buttonConfirm.addEventListener('click', (e) => {
          e.preventDefault();
          if (!checkbox.checked) return;

            let action = form.getAttribute('action');
            action = updateQueryStringParameter(action, 'action', formAction);
            form.setAttribute('action', action);
            form.submit();
        })

        function updateQueryStringParameter(uri, key, value) {
            const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            const separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            }
            else {
                return uri + separator + key + "=" + value;
            }
        }
    </script>
  @endif
@endsection
