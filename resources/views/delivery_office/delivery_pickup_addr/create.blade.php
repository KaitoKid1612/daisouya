@extends('layouts.delivery_office.app')

@section('title')
  集荷先住所登録
@endsection

@section('content')
  <div class="bl_pickupAddrCreate">
    <div class="bl_pickupAddrCreate_inner">
      <div class="bl_pickupAddrCreate_inner_head">
        <div class="bl_pickupAddrCreate_inner_head_ttl">
          <h2>集荷先住所 登録</h2>
        </div>
      </div>

      <div class="bl_pickupAddrCreate_inner_content">
        <div class="bl_pickupAddrCreate_inner_content_form">
          <form action="{{ route('delivery_office.delivery_pickup_addr.store') }}" method="POST" class="js_confirm">
            @csrf
            <section class="bl_pickupAddrCreate_inner_content_form_request">
              <h3 class='bl_create_inner_content_data_form_caption'>
                集荷先情報
              </h3>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="delivery_company_id">配送会社名</label>
                <div class="c_form_select el_width12rem">
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
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('delivery_company_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item js_form_delivery_company_name">
                <label for="delivery_company_name">配送会社名</label>
                <input type="text" name='delivery_company_name' id='delivery_company_name'
                  value="{{ old('delivery_company_name') }}">
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('delivery_company_name')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="delivery_office_name">営業所名・デポ名</label>
                <input type="text" name='delivery_office_name' id='delivery_office_name'
                  value="{{ old('delivery_office_name') }}">
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('delivery_office_name')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="email">メールアドレス</label>
                <input type="text" name='email' id="email" value="{{ old('email') }}">
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('email')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="tel">電話番号</label>
                <input type="text" name='tel' id='tel' value="{{ old('tel') }}">
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('tel')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="post_code1">郵便番号</label>
                <input type="text" name="post_code1" value="{{ old('post_code1') }}" id="post_code1"
                  class="el_width8rem">

                <span>-</span>

                <input type="text" name="post_code2" value="{{ old('post_code2') }}"
                  id="post_code2"
                  class="el_width10rem">
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('post_code1')
                      {{ $message }}
                    @enderror
                    @error('post_code2')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="addr1_id">都道府県</label>
                <div class="c_form_select el_width12rem">
                  <select name="addr1_id" id="addr1_id">
                    <option disabled selected value="">
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
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('addr1_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="addr2">市区町村</label>
                <input type="text" name='addr2' id='addr2' value="{{ old('addr2') }}">
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('addr2')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="addr3">丁目 番地 号以降</label>
                <input type="text" name='addr3' id='addr3' value="{{ old('addr3') }}">
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('addr3')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>

              <div class="c_form_item bl_pickupAddrCreate_inner_content_form_request_item">
                <label for="addr4">建物名 部屋番号</label>
                <input type="text" name='addr4' id='addr4' value="{{ old('addr4') }}">
                <div class="bl_pickupAddrCreate_inner_content_form_request_item_error">
                  <p class='bl_pickupAddrCreate_inner_content_form_request_item_error_spaceLabel'></p>
                  <p class="el_error_msg">
                    @error('addr4')
                      {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>
            </section>

            <div class="bl_pickupAddrCreate_inner_content_form_submit">
              <input type="submit" value="登録">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @if (config('app.env') === 'local')
    <script>
      /**
       *  テスト用フォーム自動入力
       * */
      document.addEventListener('DOMContentLoaded', function() {
        $delivery_company_name = document.getElementById('delivery_company_name');
        $delivery_office_name = document.getElementById('delivery_office_name');
        $email = document.getElementById('email');
        $tel = document.getElementById('tel');
        $post_code1 = document.getElementById('post_code1');
        $post_code2 = document.getElementById('post_code2');
        $addr1_id = document.getElementById('addr1_id');
        $addr2 = document.getElementById('addr2');
        $addr3 = document.getElementById('addr3');
        $addr4 = document.getElementById('addr4');

        $delivery_company_name.value = 'test会社';
        $delivery_office_name.value = 'test営業所';
        $email.value = 'test@google.com';
        $tel.value = '1234567890';
        $post_code1.value = '123';
        $post_code2.value = '4567';
        $addr1_id.value = '47';
        $addr2.value = 'test区';
        $addr3.value = 'test';
        $addr4.value = 'test 999';
      });
    </script>
  @endif
@endsection

@section('script_bottom')
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
