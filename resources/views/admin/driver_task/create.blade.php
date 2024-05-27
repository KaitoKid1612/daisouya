@extends('layouts.admin.app')

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

  <div class="bl_create">
    <div class="bl_create_inner">
      <div class="bl_create_inner_head">
        <div class="bl_create_inner_head_ttl">
          <h2>稼働依頼 作成</h2>
        </div>
      </div>

      <div class="bl_create_inner_content">
        <section class="bl_create_inner_content_data">
          <form action="{{ route('admin.driver_task.store') }}"
            method="POST" class="js_confirm">
            @csrf
            <div class="bl_create_inner_content_data_form">

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_date">稼働日</label>
                <input type="date" name='task_date' id='task_date' value="{{ old('task_date') }}">
                <p class="el_error_msg">
                  @error('task_date')
                    {{ $message }}
                  @enderror
                </p>
              </div>
              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="driver_id">ドライバーID <span class='color_main'>{{ $driver->full_name ?? '' }}</span></label>
                <input type="number" name='driver_id' id='driver_id' value="{{ old('driver_id', $_GET['driver_id'] ?? '') }}" {{ isset($_GET['driver_id']) ? 'readonly' : '' }}>
                <p class="el_error_msg">
                  @error('driver_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="delivery_office_id">営業所ID <span class='color_main'>{{ $office->name ?? '' }}</span></label>
                <input type="number" name='delivery_office_id' id='delivery_office_id'
                  value="{{ old('delivery_office_id', $_GET['delivery_office_id'] ?? '') }}" {{ isset($_GET['delivery_office_id']) ? 'readonly' : '' }}>
                <p class="el_error_msg">
                  @error('delivery_office_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="driver_task_status_id">ステータス</label>
                <div class="c_form_select">
                  <select name="driver_task_status_id" id="driver_task_status_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($task_status_list as $status)
                      <option value="{{ $status->id }}"
                        {{ old('driver_task_status_id') == $status->id ? 'selected' : '' }}>
                        {{ $status->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <p class="el_error_msg">
                  @error('driver_task_status_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="rough_quantity">先週の平均物量(個)</label>
                <input type="number" name='rough_quantity' id='rough_quantity'
                  value='{{ old('rough_quantity') }}' placeholder="整数値">
                <p class="el_error_msg">
                  @error('rough_quantity')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="delivery_route">配送コース</label>
                <textarea name="delivery_route" id="delivery_route">{{ old('delivery_route') }}</textarea>
                <p class="el_error_msg">
                  @error('delivery_route')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_memo">メモ</label>
                <textarea name="task_memo" id="task_memo">{{ old('task_memo') }}</textarea>
                <p class="el_error_msg">
                  @error('task_memo')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <h3 class='bl_create_inner_content_data_form_caption'>
                集荷先情報
              </h3>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_delivery_company_name">配送会社名</label>
                <div class="c_form_select">
                  <select name="task_delivery_company_id" id="task_delivery_company_id">
                    <option disabled selected value="">
                      選択してください。
                    </option>
                    @foreach ($company_list as $company)
                      <option
                        value="{{ $company->id }}" {{ old('task_delivery_company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name ?? '' }}
                      </option>
                    @endforeach
                    <option value="None" {{ 'None' == old('task_delivery_company_id') ? 'selected' : '' }}>
                      その他
                    </option>
                  </select>
                </div>
                <p class="el_error_msg">
                  @error('task_delivery_company_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item js_form_delivery_company_name">
                <label for="task_delivery_company_name">配送会社名</label>
                <input type="text" name='task_delivery_company_name' id='task_delivery_company_name' value="{{ old('task_delivery_company_name') }}">
                <p class="el_error_msg">
                  @error('task_delivery_company_name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_delivery_office_name">営業所名・デポ名</label>
                <input type="text" name='task_delivery_office_name' id='task_delivery_office_name' value="{{ old('task_delivery_office_name') }}">
                <p class="el_error_msg">
                  @error('task_delivery_office_name')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_email">メールアドレス</label>
                <input type="text" name='task_email' id="task_email" value="{{ old('task_email') }}">
                <p class="el_error_msg">
                  @error('task_email')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_tel">電話番号</label>
                <input type="text" name='task_tel' id='task_tel' value="{{ old('task_tel') }}">
                <p class="el_error_msg">
                  @error('task_tel')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_post_code1">郵便番号</label>
                <input type="text" name="task_post_code1" value="{{ old('task_post_code1') }}" id="task_post_code1"
                  class="el_width12rem">

                <span>-</span>

                <input type="text" name="task_post_code2" value="{{ old('task_post_code2') }}" id="task_post_code2"
                  class="el_width12rem">
                <p class="el_error_msg">
                  @error('task_post_code2')
                    {{ $message }}
                  @enderror
                  @error('task_post_code2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_addr1_id">都道府県</label>
                <div class="c_form_select">
                  <select name="task_addr1_id" id="task_addr1_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($prefecture_list as $prefecture)
                      <option
                        value="{{ $prefecture->id }}" {{ old('task_addr1_id') == $prefecture->id ? 'selected' : '' }}>
                        {{ $prefecture->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <p class="el_error_msg">
                  @error('task_addr1_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_addr2">市区町村</label>
                <input type="text" name='task_addr2' id='task_addr2' value="{{ old('task_addr2') }}">
                <p class="el_error_msg">
                  @error('task_addr2')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_addr3">丁目 番地 号以降</label>
                <input type="text" name='task_addr3' id='task_addr3' value="{{ old('task_addr3') }}">
                <p class="el_error_msg">
                  @error('task_addr3')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="task_addr4">建物名 部屋番号</label>
                <input type="text" name='task_addr4' id='task_addr4' value="{{ old('task_addr4') }}">
                <p class="el_error_msg">
                  @error('task_addr4')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="driver_task_plan_id">稼働依頼プラン</label>
                <div class="c_form_select">
                  <select name="driver_task_plan_id" id="driver_task_plan_id">
                    <option disabled selected>
                      選択してください。
                    </option>
                    @foreach ($driver_task_plan_list as $driver_task_plan)
                      <option
                        value="{{ $driver_task_plan->id }}" {{ old('driver_task_plan_id') == $driver_task_plan->id ? 'selected' : '' }}>
                        {{ $driver_task_plan->name ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <p class="el_error_msg">
                  @error('driver_task_plan_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="system_price">
                  システム利用料金(円)
                </label>
                <input type="number" name="system_price" id="system_price" value="{{ old('system_price') }}">
                <p class="el_error_msg">
                  @error('system_price')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="busy_system_price">
                  システム利用料金(繁忙期)(円)
                </label>
                <input type="number" name="busy_system_price" id="busy_system_price" value="{{ old('busy_system_price', 0) }}">
                <p class="el_error_msg">
                  @error('busy_system_price')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="freight_cost">
                  ドライバー運賃(円)
                </label>
                <input type="number" name="freight_cost" id="freight_cost" value="{{ old('freight_cost') }}">
                <p class="el_error_msg">
                  @error('freight_cost')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="emergency_price">
                  緊急依頼料金(円)
                </label>
                <input type="number" name="emergency_price" id="emergency_price" value="{{ old('emergency_price') }}">
                <p class="el_error_msg">
                  @error('emergency_price')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="discount">
                  値引き額(円)
                </label>
                <input type="number" name="discount" id="discount" value="{{ old('discount') }}">
                <p class="el_error_msg">
                  @error('discount')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="number">
                  消費税率(%)
                </label>
                <input type="text" name="tax_rate" id="tax_rate" value="{{ old('tax_rate') }}" step="0.1">
                <p class="el_error_msg">
                  @error('tax_rate')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="payment_fee_rate">
                  決済手数料率(%)
                </label>
                <input type="number" name="payment_fee_rate" id="payment_fee_rate" value="{{ old('payment_fee_rate') }}" step="0.1">
                <p class="el_error_msg">
                  @error('payment_fee_rate')
                    {{ $message }}
                  @enderror
                </p>
              </div>

              <div class="c_form_item bl_create_inner_content_data_form_item">
                <label for="payment_method_id">
                  stripe_payment_method_id
                </label>
                <input type="text" name="payment_method_id" id="payment_method_id" value="{{ old('payment_method_id') }}">
                <p class="el_error_msg">
                  @error('payment_method_id')
                    {{ $message }}
                  @enderror
                </p>
              </div>


              <div class="c_form_item bl_create_inner_content_data_form_item el_submit">
                <input type="submit" value="作成" class='c_btn'>
              </div>

              <div class="bl_create_inner_content_data_form_advice">
                <p>※こちらの操作を行うと管理者、営業所、ドライバーへメールが送られます。</p>
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

        $task_date = document.getElementById('task_date');
        $driver_id = document.getElementById('driver_id');
        $delivery_office_id = document.getElementById('delivery_office_id');
        $driver_task_status_id = document.getElementById('driver_task_status_id');
        $delivery_route = document.getElementById('delivery_route');
        $rough_quantity = document.getElementById('rough_quantity');
        $task_memo = document.getElementById('task_memo');
        $task_delivery_company_name = document.getElementById('task_delivery_company_name');
        $task_delivery_office_name = document.getElementById('task_delivery_office_name');
        $task_email = document.getElementById('task_email');
        $task_tel = document.getElementById('task_tel');
        $task_post_code1 = document.getElementById('task_post_code1');
        $task_post_code2 = document.getElementById('task_post_code2');
        $task_addr1_id = document.getElementById('task_addr1_id');
        $task_addr2 = document.getElementById('task_addr2');
        $task_addr3 = document.getElementById('task_addr3');
        $task_addr4 = document.getElementById('task_addr4');
        $task_price = document.getElementById('system_price');
        $task_busy_system_price = document.getElementById('busy_system_price');
        $task_freight_cost = document.getElementById('freight_cost');
        $task_emergency_price = document.getElementById('emergency_price');
        $task_tax_rate = document.getElementById('tax_rate');
        $task_payment_fee_rate = document.getElementById('payment_fee_rate');
        $task_payment_method_id = document.getElementById('payment_method_id');


        $task_date.value = '2022-01-01';
        $driver_id.value = '1';
        $delivery_office_id.value = '1';
        $driver_task_status_id.value = '2';
        $delivery_route.value = '配送コース配送コース配送コース配送コース';
        $rough_quantity.value = '111';
        $task_memo.value = 'memomemo';
        $task_delivery_company_name.value = 'test会社';
        $task_delivery_office_name.value = 'test営業所';
        $task_email.value = 'test@google.com';
        $task_tel.value = '1234567890';
        $task_post_code1.value = '123';
        $task_post_code2.value = '4567';
        $task_addr1_id.value = '47';
        $task_addr2.value = 'test区';
        $task_addr3.value = 'test';
        $task_addr4.value = 'test 999';
        $task_price.value = '9999';
        $task_busy_system_price.value = '0';
        $task_freight_cost = '1234';
        $task_emergency_price.value = '8888';
        $task_tax_rate.value = '10';
        $task_payment_fee_rate.value = '3.6';
        $task_payment_method_id.value = 'payment_method_id_test';

      });
    </script>
  @endif
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
