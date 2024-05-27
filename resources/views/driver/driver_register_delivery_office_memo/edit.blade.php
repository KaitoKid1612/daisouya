@extends('layouts.driver.app')

@section('title')
  ドライバー登録営業所メモ 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  <div class="bl_RegisterOfficeMemoEdit">
    <div class="bl_RegisterOfficeMemoEdit_inner">
      <div class="bl_RegisterOfficeMemoEdit_inner_head">
        <div class="bl_RegisterOfficeMemoEdit_inner_head_ttl">
          <h2>ドライバー登録営業所メモ 編集</h2>
        </div>
      </div>

      <div class="bl_RegisterOfficeMemoEdit_inner_content">
        <section class="bl_RegisterOfficeMemoEdit_inner_content_data">
          <form
            action="{{ route('driver.driver_register_delivery_office_memo.update', [
                'register_office_memo_id' => $register_office_memo->id ?? '',
            ]) }}"method="POST" class="js_confirm">
            @csrf

            <div class="bl_RegisterOfficeMemoEdit_inner_content_data_form">

              <div class="c_form_item bl_RegisterOfficeMemoEdit_inner_content_data_form_item">
                <label for="delivery_company_id">配送会社</label>
                <div class="c_form_select">
                  <select name="delivery_company_id" id="delivery_company_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($company_list as $company)
                      <option value="{{ $company->id }}"
                        {{ old('delivery_company_id', $register_office_memo->delivery_company_id ?? '') == $company->id ? 'selected' : '' }}>
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

              <div class="c_form_item bl_RegisterOfficeMemoEdit_inner_content_data_form_item">
                <label for="delivery_office_name">営業所名・デポ名</label>
                <input type="text" name='delivery_office_name' id='delivery_office_name' value="{{ old('delivery_office_name', $register_office_memo->delivery_office_name ?? '') }}">
                <p class="el_error_msg">
                  @error('delivery_office_name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_RegisterOfficeMemoEdit_inner_content_data_form_item">
                <label for="post_code1">郵便番号</label>
                <input type="text" name="post_code1" value="{{ old('post_code1', $register_office_memo->post_code1 ?? '') }}" id="post_code1"
                  class="el_width12rem">

                <span>-</span>

                <input type="text" name="post_code2" value="{{ old('post_code2', $register_office_memo->post_code2 ?? '') }}" id="post_code2"
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

              <div class="c_form_item bl_RegisterOfficeMemoEdit_inner_content_data_form_item">
                <label for="addr1_id">都道府県</label>
                <div class="c_form_select">
                  <select name="addr1_id" id="addr1_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($prefecture_list as $prefecture)
                      <option
                        value="{{ $prefecture->id }}" {{ old('addr1_id', $register_office_memo->addr1_id ?? '') == $prefecture->id ? 'selected' : '' }}>
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

              <div class="c_form_item bl_RegisterOfficeMemoEdit_inner_content_data_form_item">
                <label for="addr2">市区町村</label>
                <input type="text" name='addr2' id='addr2' value="{{ old('addr2', $register_office_memo->addr2 ?? '') }}">
                <p class="el_error_msg">
                  @error('addr2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_RegisterOfficeMemoEdit_inner_content_data_form_item">
                <label for="addr3">丁目 番地</label>
                <input type="text" name='addr3' id='addr3' value="{{ old('addr3', $register_office_memo->addr3 ?? '') }}">
                <p class="el_error_msg">
                  @error('addr3')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_RegisterOfficeMemoEdit_inner_content_data_form_item">
                <label for="addr4">建物名 部屋番号</label>
                <input type="text" name='addr4' id='addr4' value="{{ old('addr4', $register_office_memo->addr4 ?? '') }}">
                <p class="el_error_msg">
                  @error('addr4')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_RegisterOfficeMemoEdit_inner_content_data_form_item el_submit">
                <input type="submit" value="編集" class='c_btn'>
              </div>

            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
