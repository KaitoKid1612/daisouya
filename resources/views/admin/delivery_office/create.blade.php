@extends('layouts.admin.app')

@section('title')
  依頼者 作成
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
          <h2>依頼者 作成</h2>
        </div>
      </div>

      <div class="bl_create_inner_content">
        <section class="bl_create_inner_content_data">
          <form action="{{ route('admin.delivery_office.store') }}" method="POST" class="js_confirm">
            @csrf

            <div class="bl_create_inner_content_data_form">
              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="delivery_company_id">配送会社</label>
                <div class="c_form_select">
                  <select name="delivery_company_id" id="delivery_company_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    <option value="None" {{ 'None' == old('delivery_company_id', '9999') ? 'selected' : '' }}>
                      所属なし
                    </option>
                    @foreach ($company_list as $company)
                      <option value="{{ $company->id }}"
                        {{ old('delivery_company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name  ?? '' }}
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

              <div class="c_form_item bl_create_inner_content_data_form_item js_form_delivery_company_name">
                <label for="delivery_company_name">会社名</label>
                <input type="text" name='delivery_company_name' id='delivery_company_name' value="{{ old('delivery_company_name') }}">
                <p class="el_error_msg">
                  @error('delivery_company_name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="name">営業所名・デポ名</label>
                <input type="text" name='name' id='name' value="{{ old('name') }}">
                <p class="el_error_msg">
                  @error('name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="manager_name_sei">担当者 姓</label>
                <input type="text" name='manager_name_sei' id='manager_name_sei' value="{{ old('manager_name_sei') }}">
                <p class="el_error_msg">
                  @error('manager_name_sei')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="manager_name_mei">担当者 名</label>
                <input type="text" name='manager_name_mei' id='manager_name_mei' value="{{ old('manager_name_mei') }}">
                <p class="el_error_msg">
                  @error('manager_name_mei')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="manager_name_sei_kana">担当者 姓(カナ)</label>
                <input type="text" name='manager_name_sei_kana' id='manager_name_sei_kana'
                  value="{{ old('manager_name_sei_kana') }}">
                <p class="el_error_msg">
                  @error('manager_name_sei_kana')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="manager_name_mei_kana">担当者 名(カナ)</label>
                <input type="text" name='manager_name_mei_kana' id='manager_name_mei_kana'
                  value="{{ old('manager_name_mei_kana') }}">
                <p class="el_error_msg">
                  @error('manager_name_mei_kana')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="email">メールアドレス</label>
                <input type="text" name='email' id="email" value="{{ old('email') }}">
                <p class="el_error_msg">
                  @error('email')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="password">パスワード</label>
                <input type="text" name='password' id='password' value="{{ old('') }}">
                <p class="el_error_msg">
                  @error('password')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="password_confirmation">パスワード確認用</label>
                <input type="text" name='password_confirmation' id='password_confirmation'
                  value="{{ old('password_confirmation') }}">
                <p class="el_error_msg">
                  @error('password_confirmation')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="manager_tel">担当者電話番号</label>
                <input type="text" name='manager_tel' id='manager_tel' value="{{ old('manager_tel') }}">
                <p class="el_error_msg">
                  @error('manager_tel')
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
                      <option
                        value="{{ $prefecture->id }}" {{ old('addr1_id') == $prefecture->id ? 'selected' : '' }}>
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
                <label for="addr2">市区町村</label>
                <input type="text" name='addr2' id='addr2' value="{{ old('') }}">
                <p class="el_error_msg">
                  @error('addr2')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="addr3">丁目 番地 号</label>
                <input type="text" name='addr3' id='addr3' value="{{ old('') }}">
                <p class="el_error_msg">
                  @error('addr3')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="addr4">建物名 部屋番号</label>
                <input type="text" name='addr4' id='addr4' value="{{ old('') }}">
                <p class="el_error_msg">
                  @error('addr4')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="charge_user_type_id">請求に関するユーザの種類</label>
                <div class="c_form_select">
                  <select name="charge_user_type_id" id="charge_user_type_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($charge_user_type_list as $charge_user_type)
                      <option
                        value="{{ $charge_user_type->id }}"{{ $charge_user_type->id == old('charge_user_type_id') ? 'selected' : '' }}>
                        {{ $charge_user_type->name ?? '' }}
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

              <div class="c_form_item bl_create_inner_content_data_form_item el_submit">
                <input type="submit" value="作成" class='c_btn'>
              </div>

              <div class="bl_create_inner_content_data_form_advice">
                <p>※こちらの操作を行うとユーザーへメールが送られます。</p>
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
        $name = document.getElementById('name');

        $name_sei = document.getElementById('manager_name_sei');
        $name_mei = document.getElementById('manager_name_mei');
        $name_sei_kana = document.getElementById('manager_name_sei_kana');
        $name_mei_kana = document.getElementById('manager_name_mei_kana');
        $email = document.getElementById('email');
        $password = document.getElementById('password');
        $password_confirmation = document.getElementById('password_confirmation');
        $delivery_company = document.getElementById("delivery_company_id");
        $post_code1 = document.getElementById('post_code1');
        $post_code2 = document.getElementById('post_code2');
        $addr1_id = document.getElementById('addr1_id');
        $addr2 = document.getElementById('addr2');
        $addr3 = document.getElementById('addr3');
        $addr4 = document.getElementById('addr4');
        $tel = document.getElementById('manager_tel');

        $name_sei.value = 'test';
        $name_mei.value = 'test';
        $name_sei_kana.value = 'test';
        $name_mei_kana.value = 'test';
        $name.value = 'Hello営業所';
        $email.value = 'hello@amazon.com';
        $password.value = 'test1234';
        $password_confirmation.value = 'test1234';
        $delivery_company.options[1].selected = true;
        $post_code1.value = '123';
        $post_code2.value = '1234';
        $addr1_id.options[10].selected = true;
        $addr2.value = 'ぐんまー市';
        $addr3.value = '1丁目 1番地 1号';
        $addr4.value = 'マンション101';
        $tel.value = '1029384756';
      });
    </script>
  @endif
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
