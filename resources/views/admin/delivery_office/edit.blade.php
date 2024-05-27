@extends('layouts.admin.app')

@section('title')
  依頼者 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach


  @if ($office)
    <div class="bl_edit">
      <div class="bl_edit_inner">
        <div class="bl_edit_inner_head">
          <div class="bl_edit_inner_head_ttl">
            <h2>依頼者 編集</h2>
          </div>
        </div>

        <div class="bl_edit_inner_content">
          <section class="bl_edit_inner_content_data">
            <form action="{{ route('admin.delivery_office.update', ['office_id' => $office->id]) }}"
              method="POST"class="js_confirm">
              @csrf
              <div class="bl_edit_inner_content_data_form">

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="delivery_company_id">配送会社</label>
                  <div class="c_form_select">
                    <select name="delivery_company_id" id="delivery_company_id">
                      <option disabled selected value='FALSE'>
                        選択してください。
                      </option>
                      <option value="None" {{ NULL == $office->delivery_company_id ? 'selected' : '' }}>
                        所属なし
                      </option>
                      @foreach ($company_list as $company)
                        <option value="{{ $company->id }}"
                          {{ $company->id == old('delivery_company_id', $office->delivery_company_id) ? 'selected' : '' }}>
                          {{ $company->name ?? '' }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <p class="el_error_msg">
                    @error('delivery_company_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item js_form_delivery_company_name">
                  <label for="delivery_company_name">会社名</label>
                  <input type="text" name='delivery_company_name' id='delivery_company_name' value="{{ old('delivery_company_name', $office->delivery_company_name ?? '') }}">
                  <p class="el_error_msg">
                    @error('delivery_company_name')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="name">営業所名・デポ名</label>
                  <input type="text" name='name' id='name' value="{{ old('name', $office->name ?? '') }}">
                  <p class="el_error_msg">
                    @error('name')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="manager_name_sei">担当者 姓</label>
                  <input type="text" name='manager_name_sei' id='manager_name_sei'
                    value="{{ old('manager_name_sei', $office->manager_name_sei ?? '') }}">
                  <p class="el_error_msg">
                    @error('manager_name_sei')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="manager_name_mei">担当者 名</label>
                  <input type="text" name='manager_name_mei' id='manager_name_mei'
                    value="{{ old('manager_name_mei', $office->manager_name_mei ?? '') }}">
                  <p class="el_error_msg">
                    @error('manager_name_mei')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="manager_name_sei_kana">担当者 姓(カナ)</label>
                  <input type="text" name='manager_name_sei_kana' id='manager_name_sei_kana'
                    value="{{ old('manager_name_sei_kana', $office->manager_name_sei_kana ?? '') }}">
                  <p class="el_error_msg">
                    @error('manager_name_sei_kana')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="manager_name_mei_kana">担当者 名(カナ)</label>
                  <input type="text" name='manager_name_mei_kana' id='manager_name_mei_kana'
                    value="{{ old('manager_name_mei_kana', $office->manager_name_mei_kana ?? '') }}">
                  <p class="el_error_msg">
                    @error('manager_name_mei_kana')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="email">メールアドレス</label>
                  <input type="text" name='email' id="email" value="{{ old('email', $office->email ?? '') }}">
                  <p class="el_error_msg">
                    @error('email')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                @if ($office && !$office->deleted_at)
                  <div class="c_form_item bl_edit_inner_content_data_form_item">
                    <label for="password">パスワード</label>
                    <input type="text" name='password' id='password'>
                    <p class="el_error_msg">
                      @error('password')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>

                  <div class="c_form_item bl_edit_inner_content_data_form_item">
                    <label for="password_confirmation">パスワード確認用</label>
                    <input type="text" name='password_confirmation' id='password_confirmation'>
                    <p class="el_error_msg">
                      @error('password_confirmation')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                @endif

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="manager_tel">担当者電話番号</label>
                  <input type="text" name='manager_tel' id='manager_tel' value="{{ old('manager_tel', $office->manager_tel ?? '') }}">
                  <p class="el_error_msg">
                    @error('manager_tel')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="post_code1">郵便番号</label>
                  <input type="text" name="post_code1" value="{{ old('post_code1', $office->post_code1 ?? '') }}" id="post_code1"
                    class="el_width12rem">

                  <span>-</span>

                  <input type="text" name="post_code2" value="{{ old('post_code2', $office->post_code2 ?? '') }}" id="post_code2"
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
                    <select name="addr1_id" id="addr1_id">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @foreach ($prefecture_list as $prefecture)
                        <option
                          value="{{ $prefecture->id }}"{{ $prefecture->id == old('addr1_id', $office->addr1_id) ? 'selected' : '' }}>
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
                  <label for="addr2">市区町村</label>
                  <input type="text" name='addr2' id='addr2' value="{{ old('addr2', $office->addr2 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr2')
                      {{ $message }}
                    @enderror
                  </p>
                </div>


                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="addr3">丁目 番地 号</label>
                  <input type="text" name='addr3' id='addr3' value="{{ old('addr3', $office->addr3 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr3')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="addr4">建物名 部屋番号</label>
                  <input type="text" name='addr4' id='addr4' value="{{ old('addr4', $office->addr4 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr4')
                      {{ $message }}
                    @enderror
                  </p>
                </div>


                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="charge_user_type_id">請求に関するユーザの種類</label>
                  <div class="c_form_select">
                    <select name="charge_user_type_id" id="charge_user_type_id">
                      <option disabled selected>
                        選択してください。
                      </option>
                      @foreach ($charge_user_type_list as $charge_user_type)
                        <option
                          value="{{ $charge_user_type->id }}"{{ $charge_user_type->id == old('charge_user_type_id', $office->charge_user_type_id) ? 'selected' : '' }}>
                          {{ $charge_user_type->name  ?? '' }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <p class="el_error_msg">
                    @error('charge_user_type_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>


                <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                  <input type="submit" value="編集" class='c_btn remove'>

                  @if ($office->deleted_at)
                    <input type="submit" value="退会" class='c_btn unsubscribe' style="margin-left: 10px">
                  @endif
                </div>


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
         *
         * */
        // document.addEventListener('DOMContentLoaded', function() {
        //   $name = document.getElementById('name');
        //   $email = document.getElementById('email');
        //   $password = document.getElementById('password');
        //   $password_confirmation = document.getElementById('password_confirmation');
        //   $delivery_company = document.getElementById("delivery_company_id");
        //   $addr1_id = document.getElementById('addr1_id');
        //   $addr2 = document.getElementById('addr2');
        //   $addr3 = document.getElementById('addr3');
        //   $tel = document.getElementById('manager_tel');

        //   $name.value = 'Hello営業所';

        //   $email.value = 'hello@test.test';
        //   $password.value = 'test1234';
        //   $password_confirmation.value = 'test1234';
        //   $delivery_company.options[1].selected = true;
        //   $addr1_id.options[10].selected = true;
        //   $addr2.value = 'ぐんまー市';
        //   $addr3.value = '1丁目 1番地 マンション101';
        //   $tel.value = '1029384756';
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

  @if ($office && $office->deleted_at)
    <script>
        // Elements
        const buttonUnsubscribe = document.querySelector(".unsubscribe");
        const buttonRemove = document.querySelector(".remove");

        const form = document.querySelector('.js_confirm');
        const popup = document.querySelector('.bl_officeEdit_popup');
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
