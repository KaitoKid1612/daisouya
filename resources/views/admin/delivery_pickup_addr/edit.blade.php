@extends('layouts.admin.app')

@section('title')
  営業所 集荷先住所 編集
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  @if ($pickup_addr)
    <div class="bl_edit">
      <div class="bl_edit_inner">
        <div class="bl_edit_inner_head">
          <div class="bl_edit_inner_head_ttl">
            <h2>営業所 集荷先住所 編集</h2>
          </div>
        </div>

        <div class="bl_edit_inner_content">
          <section class="bl_edit_inner_content_data">
            <form action="{{ route('admin.delivery_pickup_addr.update', [
                'pickup_id' => $pickup_addr->id,
            ]) }}"
              method="POST" class="js_confirm">
              @csrf
              <div class="bl_edit_inner_content_data_form">

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="delivery_office_id">営業所ID <span
                      class='color_main'>{{ $pickup_addr->joinOffice->name ?? '' }}</span></label>
                  <input type="number" name='delivery_office_id' id='delivery_office_id'
                    value='{{ old('delivery_office_id', $pickup_addr->delivery_office_id) }}' readonly>
                  <p class="el_error_msg">
                    @error('delivery_office_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
                <div class="c_form_item bl_create_inner_content_data_form_item">
                  <label for="delivery_company_id">配送会社</label>
                  <div class="c_form_select">
                    <select name="delivery_company_id" id="delivery_company_id">
                      <option disabled selected value="">
                        選択してください。
                      </option>
                      @foreach ($company_list as $company)
                        <option
                          value="{{ $company->id }}" {{ old('delivery_company_id') == $company->id ? 'selected' : '' }}>
                          {{ $company->name ?? '' }}
                        </option>
                      @endforeach
                      <option value="None" {{ 'None' == old('delivery_company_id') ? 'selected' : '' }}>
                        その他
                      </option>
                    </select>
                  </div>
                  <p class="el_error_msg">
                    @error('delivery_company_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="delivery_company_name">配送会社名</label>
                  <input type="text" name='delivery_company_name' id='delivery_company_name'
                    value="{{ old('delivery_company_name', $pickup_addr->delivery_company_name ?? '') }}">
                  <p class="el_error_msg">
                    @error('delivery_company_name')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="delivery_office_name">営業所名・デポ名</label>
                  <input type="text" name='delivery_office_name' id='delivery_office_name'
                    value="{{ old('delivery_office_name', $pickup_addr->delivery_office_name ?? '') }}">
                  <p class="el_error_msg">
                    @error('delivery_office_name')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="email">メールアドレス</label>
                  <input type="text" name='email' id="email"
                    value="{{ old('email', $pickup_addr->email ?? '') }}">
                  <p class="el_error_msg">
                    @error('email')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="tel">電話番号</label>
                  <input type="text" name='tel' id='tel'
                    value="{{ old('tel', $pickup_addr->tel ?? '') }}">
                  <p class="el_error_msg">
                    @error('tel')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="post_code1">郵便番号</label>
                  <input type="text" name="post_code1"
                    value="{{ old('post_code1', $pickup_addr->post_code1 ?? '') }}" id="post_code1"
                    class="el_width12rem">

                  <span>-</span>

                  <input type="text" name="post_code2"
                    value="{{ old('post_code2', $pickup_addr->post_code2 ?? '') }}" id="post_code2"
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
                          value="{{ $prefecture->id }}"
                          {{ old('addr1_id', $pickup_addr->addr1_id ?? '') == $prefecture->id ? 'selected' : '' }}>
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
                  <input type="text" name='addr2' id='addr2'
                    value="{{ old('addr2', $pickup_addr->addr2 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr2')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="addr3">丁目 番地 号以降</label>
                  <input type="text" name='addr3' id='addr3'
                    value="{{ old('addr3', $pickup_addr->addr3 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr3')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item">
                  <label for="addr4">建物名 部屋番号</label>
                  <input type="text" name='addr4' id='addr4'
                    value="{{ old('addr4', $pickup_addr->addr4 ?? '') }}">
                  <p class="el_error_msg">
                    @error('addr4')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="c_form_item bl_edit_inner_content_data_form_item el_submit">
                  <input type="submit" value="編集" class='c_btn'>
                </div>

              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
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
@endsection
