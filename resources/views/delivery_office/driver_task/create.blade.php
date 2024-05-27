@extends('layouts.delivery_office.app')

@section('title')
  稼働依頼登録
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  {{-- メッセージ --}}
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_taskCreate">
    <div class="bl_taskCreate_inner">
      <div class="bl_taskCreate_inner_head">
        <div class="bl_taskCreate_inner_head_ttl">
          <h2>稼働依頼登録<span>/ registration for new request</span></h2>
        </div>
      </div>

      @if (!$template)
        <div class="bl_taskCreate_inner_content">
          <div class="bl_taskCreate_inner_content_form">
            <form action="{{ route('delivery_office.driver_task.store') }}" method="POST" class="js_confirm">
              @csrf
              <section class="bl_taskCreate_inner_content_form_request">

                <div class="bl_taskCreate_inner_content_form_request_price">
                  <div class="bl_taskCreate_inner_content_form_request_price_ttl">
                    <h3>ご請求</h3>
                  </div>
                  <p>システム利用料金: <span class="js_systemPrice">{{ old('system_price', '-') }}</span>円</p>
                  <input type="hidden" name="system_price" class="js_systemPrice_input" value="{{ old('system_price') }}">

                  <p class='js_busySystemPrice_p'>システム利用料金(繁忙期): <span class="js_busySystemPrice">{{ old('busy_system_price') }}</span>円</p>
                  <input type="hidden" name="busy_system_price" class="js_busySystemPrice_input" value="{{ old('busy_system_price') }}">

                  <p>ドライバー運賃: <span class="js_freightCost">{{ old('freight_cost', '-') }}</span>円</p>
                  <input type="hidden" name="freight_cost" class="js_freightCost_input" value="{{ old('freight_cost') }}">

                  <p>緊急依頼料金: <span class="js_emergencyPrice">{{ old('emergency_price', '-') }}</span>円</p>
                  <input type="hidden" name="emergency_price" class="js_emergencyPrice_input" value="{{ old('emergency_price') }}">

                  <p>消費税: <span class="js_tax">{{ old('tax', '-') }}</span>円</p>
                  <input type="hidden" name="tax" value="{{ old('tax') }}" class="js_tax_input">

                  <p>総計: <span class="js_totalPrice">{{ old('total_price', '-') }}</span>円</p>
                  <input type="hidden" name="total_price" class="js_totalPrice_input" value="{{ old('total_price') }}">

                  <p class="u_font_bold">{{ Auth::guard('delivery_offices')->user()->charge_user_type_id == 2 ? '※無料ユーザーのため請求されません。' : '' }}</p>

                  <p></p>
                </div>


                <div class="bl_taskCreate_inner_content_form_request_ttl">
                  <h3>ご依頼内容</h3>
                  <a href="/delivery-office/driver-task-template">保存した依頼一覧</a>
                </div>
                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="task_date" class="el_align_center">稼働日<span class="u_red">*</span></label>
                  <div class="bl_taskCreate_inner_content_form_request_item_selectbox el_width12rem">
                    <select name="task_date" id="task_date">
                      <option value="" disabled selected>年月日</option>
                      @foreach ($date_list as $item)
                        <option value="{{ $item['value'] }}" {{ old('task_date') == $item['value'] ? 'selected' : '' }}>
                          {{ $item['value'] }} ({{ $item['week'] }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('task_date')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="driver_task_plan_id" class="el_align_center">稼働依頼プラン<span class="u_red">*</span></label>
                  <div class="bl_taskCreate_inner_content_form_request_item_selectbox el_width12rem">
                    <select name="driver_task_plan_id" id="driver_task_plan_id">
                      <option value="" disabled selected>選択してください</option>
                      @foreach ($driver_task_plan_list as $driver_task_plan)
                        <option value="{{ $driver_task_plan->id }}" {{ old('driver_task_plan_id') == $driver_task_plan->id ? 'selected' : '' }}>
                          {{ $driver_task_plan->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg js_error_msg_driver_task_plan_id">
                      @error('driver_task_plan_id')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                  <label for="driver_id">ドライバー検索</label>
                  <input type="text" id="search_driver" name="search_driver" placeholder="キーワード(名前 ドライバーID)" class="">

                  <p class="bl_taskCreate_inner_content_form_request_item_labelSpace"></p>
                  <ul class="el_search_driver_ul js_search_driver_ul"></ul>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="driver_id">担当ドライバー</label>
                  <p class='js_driver_name'>{{ $driver->full_name ?? '指定なし' }}</p>
                  <input type="hidden" name='driver_id' id='driver_id' value="{{ old('driver_id', $driver->id ?? '') }}">

                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('driver_id')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                  <label for="dummy_freight_cost">ドライバー運賃<span class="u_red">*</span>
                  </label>
                  <input type="number" name='dummy_freight_cost' id='dummy_freight_cost'
                    value="{{ old('dummy_freight_cost') }}" placeholder="整数値" class="el_width12rem js_dummyFreightCost_input">
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg js_freight_cost_error_msg">
                      @error('freight_cost')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="c_form_item bl_taskCreate_inner_content_form_request_item div_system_price" style="display: flex">
                    <label for="system_price">システム利用料金<span class="u_red">*</span></label>
                    <div class="bl_taskCreate_inner_content_form_request_item_selectbox el_width12rem">
                        <select name='system_price' id='system_price' class="el_width12rem">
                            @foreach(config('constants.SYSTEM_PRICE_LIST') as $priceOption)
                                <option value="{{ $priceOption['value'] }}">{{ $priceOption['value'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                        <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                        <p class="el_error_msg js_system_price_error_msg">
                            @error('system_price')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                </div>

                <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                  <label for="rough_quantity">先週の平均物量(個/件)<span class="u_red">*</span>
                  </label>
                  <input type="number" name='rough_quantity' id='rough_quantity'
                    value="{{ old('rough_quantity') }}" placeholder="整数値" class="el_width12rem">
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('rough_quantity')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="delivery_route">配送コース<span class="u_red">*</span></label>
                  <textarea name="delivery_route" id="delivery_route"
                    placeholder="">{{ old('delivery_route') }}</textarea>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('delivery_route')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="task_memo">備考 依頼メモ</label>
                  <textarea name="task_memo" id="task_memo"
                    placeholder="その他特記事項がございましたらご記入下さい"></textarea>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('task_memo')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_ttl">
                  <h3>集荷先情報</h3>
                </div>
                <div class='bl_taskCreate_inner_content_form_request_radioList'>
                  <input type="radio" name="pickup_addr_id" id="pickup_addr_new"
                    class="js_pickup_addr_radio" value="is_new" {{ old('pickup_addr_id', 'is_new') === 'is_new' ? 'checked' : '' }}>
                  <label for="pickup_addr_new">
                    新しい集荷先
                  </label>
                  @foreach ($pickup_addr_list as $pickup_addr)
                    <input type="radio" name="pickup_addr_id" id="pickup_addr_{{ $pickup_addr->id }}"
                      value="{{ $pickup_addr->id }}" {{ old('pickup_addr_id') == $pickup_addr->id ? 'checked' : '' }} class="js_pickup_addr_radio">
                    <label
                      for="pickup_addr_{{ $pickup_addr->id }}">
                      {{ $pickup_addr->delivery_company_name }} {{ $pickup_addr->full_post_code }}
                      {{ $pickup_addr->full_addr }} {{ $pickup_addr->email }} {{ $pickup_addr->tel }}
                    </label>
                  @endforeach
                  <p class="el_error_msg">
                    @error('pickup_addr_new')
                      {{ $message }}
                    @enderror
                  </p>
                  <p class="el_error_msg">
                    @error('pickup_addr_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="js_pickup_addr_form bl_taskCreate_inner_content_form_request_box {{ old('pickup_addr_new', true) ? 'js_active' : '' }}">

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_delivery_company_id">配送会社名<span class="u_red">*</span></label>
                    <div class="c_form_select el_width12rem">
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
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_delivery_company_id')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item js_form_delivery_company_name">
                    <label for="task_delivery_company_name">配送会社名<span class="u_red">*</span></label>
                    <input type="text" name='task_delivery_company_name' id='task_delivery_company_name'
                      value="{{ old('task_delivery_company_name') }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_delivery_company_name')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_delivery_office_name">営業所名・デポ名<span class="u_red">*</span></label>
                    <input type="text" name='task_delivery_office_name' id='task_delivery_office_name'
                      value="{{ old('task_delivery_office_name') }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_delivery_office_name')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_email">メールアドレス<span class="u_red">*</span></label>
                    <input type="text" name='task_email' id="task_email" value="{{ old('task_email') }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_email')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_tel">電話番号<span class="u_red">*</span></label>
                    <input type="text" name='task_tel' id='task_tel' value="{{ old('task_tel') }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_tel')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_post_code1">郵便番号<span class="u_red">*</span></label>
                    <input type="text" name="task_post_code1" value="{{ old('task_post_code1') }}"
                      id="task_post_code1"
                      class="el_width8rem">

                    <span>-</span>

                    <input type="text" name="task_post_code2" value="{{ old('task_post_code2') }}"
                      id="task_post_code2"
                      class="el_width10rem">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_post_code1')
                          {{ $message }}
                        @enderror
                        @error('task_post_code2')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>

                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_addr1_id">都道府県<span class="u_red">*</span></label>
                    <div class="c_form_select el_width12rem">
                      <select name="task_addr1_id" id="task_addr1_id">
                        <option disabled selected value="">
                          選択してください。
                        </option>
                        @foreach ($prefecture_list as $prefecture)
                          <option
                            value="{{ $prefecture->id }}"
                            {{ old('task_addr1_id') == $prefecture->id ? 'selected' : '' }}>
                            {{ $prefecture->name ?? '' }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_addr1_id')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_addr2">市区町村<span class="u_red">*</span></label>
                    <input type="text" name='task_addr2' id='task_addr2' value="{{ old('task_addr2') }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_addr2')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_addr3">丁目 番地 号以降<span class="u_red">*</span></label>
                    <input type="text" name='task_addr3' id='task_addr3' value="{{ old('task_addr3') }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_addr3')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_addr4">建物名 部屋番号</label>
                    <input type="text" name='task_addr4' id='task_addr4' value="{{ old('task_addr4') }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_addr4')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item c_form_checkbox">
                    <input type="checkbox" name="is_create_pickup_addr" id="is_create_pickup_addr" value="1" {{ old('is_create_pickup_addr') == '1' ? 'checked' : '' }}>
                    <label for="is_create_pickup_addr">この集荷先を保存する場合はチェック</label>
                    <p class="el_error_msg">
                      @error('is_create_pickup_addr')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>
              </section>


              <section class="bl_taskCreate_inner_content_form_payment">
                <h3>支払い方法<span class="u_red">*</span></h3>
                <div class="bl_taskCreate_inner_content_form_payment_radioList">
                  @if (auth('delivery_offices')->user()->charge_user_type_id === 2)
                    <p>※無料ユーザーのため請求されません。</p>
                  @else
                    @foreach ($payment_method_list as $payment_item)
                      <input type="radio" name="payment_method_id" id="payment_method_id_{{ $payment_item->id }}"
                        value="{{ $payment_item->id }}" {{ old('payment_method_id') == $payment_item->id ? 'checked' : '' }} class="js_payment_radio">
                      <label
                        for="payment_method_id_{{ $payment_item->id }}">
                        カード会社: {{ $payment_item->card->brand }}
                        期限: {{ $payment_item->card->exp_month }}/{{ $payment_item->card->exp_year }}
                        番号: ****{{ $payment_item->card->last4 }}
                        名義人: {{ $payment_item->billing_details->name ?? '' }}
                      </label>
                    @endforeach
                    @error('payment_method_id')
                      <p class="el_error_msg">
                        {{ $message }}
                      </p>
                    @enderror
                  @endif
                </div>
              </section>


              <section class="bl_taskCreate_inner_content_form_officeInfo">
                <h3>ご依頼者様の情報</h3>
                <div>
                  <dl>
                    <dt>会社名</dt>
                    <dd>{{ $office->joinCompany->name ?? ($office->delivery_company_name ?? '') }}</dd>
                  </dl>

                  <dl>
                    <dt>営業所名・デポ名</dt>
                    <dd>{{ $office->name ?? '' }}</dd>
                  </dl>

                  <dl>
                    <dt>郵便番号</dt>
                    <dd>{{ $office->full_post_code }}</dd>
                  </dl>

                  <dl>
                    <dt>集荷先住所</dt>
                    <dd>{{ $office->full_addr }}</dd>
                  </dl>

                  <dl>
                    <dt>ご担当者</dt>
                    <dd>{{ $office->manager_name_sei }} {{ $office->manager_name_mei }}</dd>
                  </dl>

                  <dl>
                    <dt>メールアドレス</dt>
                    <dd>{{ $office->email }}</dd>
                  </dl>

                  <dl>
                    <dt>電話番号</dt>
                    <dd>{{ $office->manager_tel }}</dd>
                  </dl>
                </div>
              </section>

              <div class="bl_taskCreate_inner_content_form_submit">
                <input type="submit" value="この内容で登録" class="create">
                <input type="submit" value="この依頼を保存する" class="template">
              </div>
            </form>
          </div>
        </div>
      @else
        <div class="bl_taskCreate_inner_content">
          <div class="bl_taskCreate_inner_content_form">
            <form action="{{ route('delivery_office.driver_task.store') }}" method="POST" class="js_confirm">
              @csrf
              <section class="bl_taskCreate_inner_content_form_request">

                <div class="bl_taskCreate_inner_content_form_request_price">
                  <div class="bl_taskCreate_inner_content_form_request_price_ttl">
                    <h3>ご請求</h3>
                  </div>
                  <p>システム利用料金: <span class="js_systemPrice">{{ old('system_price', '-') }}</span>円</p>
                  <input type="hidden" name="system_price" class="js_systemPrice_input" value="{{ old('system_price') }}">

                  <p class='js_busySystemPrice_p'>システム利用料金(繁忙期): <span class="js_busySystemPrice">{{ old('busy_system_price') }}</span>円</p>
                  <input type="hidden" name="busy_system_price" class="js_busySystemPrice_input" value="{{ old('busy_system_price') }}">

                  <p>ドライバー運賃: <span class="js_freightCost">{{ old('freight_cost', '-') }}</span>円</p>
                  <input type="hidden" name="freight_cost" class="js_freightCost_input" value="{{ old('freight_cost') }}">

                  <p>緊急依頼料金: <span class="js_emergencyPrice">{{ old('emergency_price', '-') }}</span>円</p>
                  <input type="hidden" name="emergency_price" class="js_emergencyPrice_input" value="{{ old('emergency_price') }}">

                  <p>消費税: <span class="js_tax">{{ old('tax', '-') }}</span>円</p>
                  <input type="hidden" name="tax" value="{{ old('tax') }}" class="js_tax_input">

                  <p>総計: <span class="js_totalPrice">{{ old('total_price', '-') }}</span>円</p>
                  <input type="hidden" name="total_price" class="js_totalPrice_input" value="{{ old('total_price') }}">

                  <p class="u_font_bold">{{ Auth::guard('delivery_offices')->user()->charge_user_type_id == 2 ? '※無料ユーザーのため請求されません。' : '' }}</p>

                  <p></p>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_ttl">
                  <h3>ご依頼内容</h3>
                  <a href="/delivery-office/driver-task-template">保存した依頼一覧</a>
                </div>
                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="task_date" class="el_align_center">稼働日<span class="u_red">*</span></label>
                  <div class="bl_taskCreate_inner_content_form_request_item_selectbox el_width12rem">
                    <select name="task_date" id="task_date">
                      <option value="" disabled selected>年月日</option>
                      @foreach ($date_list as $item)
                        <option value="{{ $item['value'] }}" {{ date('Y-m-d', strtotime($template->task_date)) == $item['value'] ? 'selected' : '' }}>
                          {{ $item['value'] }} ({{ $item['week'] }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('task_date')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="driver_task_plan_id" class="el_align_center">稼働依頼プラン<span class="u_red">*</span></label>
                  <div class="bl_taskCreate_inner_content_form_request_item_selectbox el_width12rem">
                    <select name="driver_task_plan_id" id="driver_task_plan_id">
                      <option value="" disabled selected>選択してください</option>
                      @foreach ($driver_task_plan_list as $driver_task_plan)
                        <option value="{{ $driver_task_plan->id }}" {{ $template->driver_task_plan_id == $driver_task_plan->id ? 'selected' : '' }}>
                          {{ $driver_task_plan->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg js_error_msg_driver_task_plan_id">
                      @error('driver_task_plan_id')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                  <label for="driver_id">ドライバー検索</label>
                  <input type="text" id="search_driver" name="search_driver" placeholder="キーワード(名前 ドライバーID)" class="" value="{{ $template->driver_id }}">

                  <p class="bl_taskCreate_inner_content_form_request_item_labelSpace"></p>
                  <ul class="el_search_driver_ul js_search_driver_ul"></ul>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="driver_id">担当ドライバー</label>
                  <p class='js_driver_name'>{{ $driver->full_name ?? '指定なし' }}</p>
                  <input type="hidden" name='driver_id' id='driver_id' value="{{ old('driver_id', $driver->id ?? '') }}">

                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('driver_id')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                  <label for="dummy_freight_cost">ドライバー運賃<span class="u_red">*</span></label>
                  <input type="number" name='dummy_freight_cost' id='dummy_freight_cost'
                         value="{{ $template->freight_cost }}" placeholder="整数値" class="el_width12rem">
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg js_freight_cost_error_msg">
                          @error('freight_cost')
                          {{ $message }}
                          @enderror
                      </p>
                  </div>
                </div>

                <div class="c_form_item bl_taskCreate_inner_content_form_request_item div_system_price" style="display: none">
                  <label for="system_price">システム利用料金<span class="u_red">*</span></label>
                  <div class="bl_taskCreate_inner_content_form_request_item_selectbox el_width12rem">
                      <select name='system_price' id='system_price' class="el_width12rem">
                          @foreach(config('constants.SYSTEM_PRICE_LIST') as $priceOption)
                              <option value="{{ $priceOption['value'] }}" {{ $template->system_price == $priceOption['value'] ? 'selected' : '' }}>
                                  {{ $priceOption['value'] }}
                              </option>
                          @endforeach
                      </select>
                  </div>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg js_system_price_error_msg">
                          @error('system_price')
                          {{ $message }}
                          @enderror
                      </p>
                  </div>
              </div>

                <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                  <label for="rough_quantity">先週の平均物量(個/件)<span class="u_red">*</span>
                  </label>
                  <input type="number" name='rough_quantity' id='rough_quantity'
                    value="{{ $template->rough_quantity }}" placeholder="整数値" class="el_width12rem">
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('rough_quantity')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="delivery_route">配送コース<span class="u_red">*</span></label>
                  <textarea name="delivery_route" id="delivery_route"
                    placeholder="">{{ $template->delivery_route }}</textarea>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('delivery_route')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_item">
                  <label for="task_memo">備考 依頼メモ</label>
                  <textarea name="task_memo" id="task_memo"
                    placeholder="その他特記事項がございましたらご記入下さい">{{ $template->task_memo }}</textarea>
                  <div class="bl_taskCreate_inner_content_form_request_item_error">
                    <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                    <p class="el_error_msg">
                      @error('task_memo')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>

                <div class="bl_taskCreate_inner_content_form_request_ttl">
                  <h3>集荷先情報</h3>
                </div>
                <div class='bl_taskCreate_inner_content_form_request_radioList'>
                  <input type="radio" name="pickup_addr_id" id="pickup_addr_new"
                    class="js_pickup_addr_radio" value="is_new" {{ old('pickup_addr_id', 'is_new') === 'is_new' ? 'checked' : '' }}>
                  <label for="pickup_addr_new">
                    新しい集荷先
                  </label>
                  @foreach ($pickup_addr_list as $pickup_addr)
                    <input type="radio" name="pickup_addr_id" id="pickup_addr_{{ $pickup_addr->id }}"
                      value="{{ $pickup_addr->id }}" {{ $template->pickup_addr_id == $pickup_addr->id ? 'checked' : '' }} class="js_pickup_addr_radio">
                    <label
                      for="pickup_addr_{{ $pickup_addr->id }}">
                      {{ $pickup_addr->delivery_company_name }} {{ $pickup_addr->full_post_code }}
                      {{ $pickup_addr->full_addr }} {{ $pickup_addr->email }} {{ $pickup_addr->tel }}
                    </label>
                  @endforeach
                  <p class="el_error_msg">
                    @error('pickup_addr_new')
                      {{ $message }}
                    @enderror
                  </p>
                  <p class="el_error_msg">
                    @error('pickup_addr_id')
                      {{ $message }}
                    @enderror
                  </p>
                </div>

                <div class="js_pickup_addr_form bl_taskCreate_inner_content_form_request_box {{ old('pickup_addr_new', true) ? 'js_active' : '' }}">

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_delivery_company_id">配送会社名<span class="u_red">*</span></label>
                    <div class="c_form_select el_width12rem">
                      <select name="task_delivery_company_id" id="task_delivery_company_id">
                        <option disabled selected value="">
                          選択してください。
                        </option>
                        @foreach ($company_list as $company)
                          <option
                            value="{{ $company->id }}" {{ $template->task_delivery_company_name == $company->name ? 'selected' : '' }}>
                            {{ $company->name ?? '' }}
                          </option>
                        @endforeach
                        <option value="None" {{ 'None' == old('task_delivery_company_id') ? 'selected' : '' }}>
                          その他
                        </option>
                      </select>
                    </div>
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_delivery_company_id')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item js_form_delivery_company_name">
                    <label for="task_delivery_company_name">配送会社名<span class="u_red">*</span></label>
                    <input type="text" name='task_delivery_company_name' id='task_delivery_company_name'
                      value="{{ old('task_delivery_company_name') }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_delivery_company_name')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_delivery_office_name">営業所名・デポ名<span class="u_red">*</span></label>
                    <input type="text" name='task_delivery_office_name' id='task_delivery_office_name'
                      value="{{ $template->task_delivery_office_name }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_delivery_office_name')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_email">メールアドレス<span class="u_red">*</span></label>
                    <input type="text" name='task_email' id="task_email" value="{{ $template->task_email }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_email')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_tel">電話番号<span class="u_red">*</span></label>
                    <input type="text" name='task_tel' id='task_tel' value="{{ $template->task_tel }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_tel')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_post_code1">郵便番号<span class="u_red">*</span></label>
                    <input type="text" name="task_post_code1" value="{{ $template->task_post_code1 }}"
                      id="task_post_code1"
                      class="el_width8rem">

                    <span>-</span>

                    <input type="text" name="task_post_code2" value="{{ $template->task_post_code2 }}"
                      id="task_post_code2"
                      class="el_width10rem">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_post_code1')
                          {{ $message }}
                        @enderror
                        @error('task_post_code2')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>

                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_addr1_id">都道府県<span class="u_red">*</span></label>
                    <div class="c_form_select el_width12rem">
                      <select name="task_addr1_id" id="task_addr1_id">
                        <option disabled selected value="">
                          選択してください。
                        </option>
                        @foreach ($prefecture_list as $prefecture)
                          <option
                            value="{{ $prefecture->id }}"
                            {{ $template->task_addr1_id == $prefecture->id ? 'selected' : '' }}>
                            {{ $prefecture->name ?? '' }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_addr1_id')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_addr2">市区町村<span class="u_red">*</span></label>
                    <input type="text" name='task_addr2' id='task_addr2' value="{{ $template->task_addr2 }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_addr2')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_addr3">丁目 番地 号以降<span class="u_red">*</span></label>
                    <input type="text" name='task_addr3' id='task_addr3' value="{{ $template->task_addr3 }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_addr3')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item bl_taskCreate_inner_content_form_request_item">
                    <label for="task_addr4">建物名 部屋番号</label>
                    <input type="text" name='task_addr4' id='task_addr4' value="{{ $template->task_addr4 }}">
                    <div class="bl_taskCreate_inner_content_form_request_item_error">
                      <p class='bl_taskCreate_inner_content_form_request_item_error_spaceLabel'></p>
                      <p class="el_error_msg">
                        @error('task_addr4')
                          {{ $message }}
                        @enderror
                      </p>
                    </div>
                  </div>

                  <div class="c_form_item c_form_checkbox">
                    <input type="checkbox" name="is_create_pickup_addr" id="is_create_pickup_addr" value="1" {{ $template->is_create_pickup_addr == '1' ? 'checked' : '' }}>
                    <label for="is_create_pickup_addr">この集荷先を保存する場合はチェック</label>
                    <p class="el_error_msg">
                      @error('is_create_pickup_addr')
                        {{ $message }}
                      @enderror
                    </p>
                  </div>
                </div>
              </section>


              <section class="bl_taskCreate_inner_content_form_payment">
                <h3>支払い方法<span class="u_red">*</span></h3>
                <div class="bl_taskCreate_inner_content_form_payment_radioList">
                  @if (auth('delivery_offices')->user()->charge_user_type_id === 2)
                    <p>※無料ユーザーのため請求されません。</p>
                  @else
                    @foreach ($payment_method_list as $payment_item)
                      <input type="radio" name="payment_method_id" id="payment_method_id_{{ $payment_item->id }}"
                        value="{{ $payment_item->id }}" {{ $template->stripe_payment_method_id == $payment_item->id ? 'checked' : '' }} class="js_payment_radio">
                      <label
                        for="payment_method_id_{{ $payment_item->id }}">
                        カード会社: {{ $payment_item->card->brand }}
                        期限: {{ $payment_item->card->exp_month }}/{{ $payment_item->card->exp_year }}
                        番号: ****{{ $payment_item->card->last4 }}
                        名義人: {{ $payment_item->billing_details->name ?? '' }}
                      </label>
                    @endforeach
                    @error('payment_method_id')
                      <p class="el_error_msg">
                        {{ $message }}
                      </p>
                    @enderror
                  @endif
                </div>
              </section>


              <section class="bl_taskCreate_inner_content_form_officeInfo">
                <h3>ご依頼者様の情報</h3>
                <div>
                  <dl>
                    <dt>会社名</dt>
                    <dd>{{ $office->joinCompany->name ?? ($office->delivery_company_name ?? '') }}</dd>
                  </dl>

                  <dl>
                    <dt>営業所名・デポ名</dt>
                    <dd>{{ $office->name ?? '' }}</dd>
                  </dl>

                  <dl>
                    <dt>郵便番号</dt>
                    <dd>{{ $office->full_post_code }}</dd>
                  </dl>

                  <dl>
                    <dt>集荷先住所</dt>
                    <dd>{{ $office->full_addr }}</dd>
                  </dl>

                  <dl>
                    <dt>ご担当者</dt>
                    <dd>{{ $office->manager_name_sei }} {{ $office->manager_name_mei }}</dd>
                  </dl>

                  <dl>
                    <dt>メールアドレス</dt>
                    <dd>{{ $office->email }}</dd>
                  </dl>

                  <dl>
                    <dt>電話番号</dt>
                    <dd>{{ $office->manager_tel }}</dd>
                  </dl>
                </div>
              </section>

              <div class="bl_taskCreate_inner_content_form_submit">
                <input type="submit" value="この内容で登録" class="create">
                <input type="submit" value="この依頼を保存する" class="template">
              </div>
            </form>
          </div>
        </div>
      @endif
      <div class="bl_taskCreate_popup" id="bl_taskCreate_popup_1">
        <p class="bl_taskCreate_popup_heading"></p>

        <p class="bl_taskCreate_popup_question"></p>
        <p class="bl_taskCreate_popup_warning"></p>

        <div class="bl_taskCreate_popup_checkbox">
          <input
            type="checkbox"
            name="bl_taskCreate_popup_checkbox"
            value="1"
            id="bl_taskCreate_popup_input_1"
          >
          <label for="bl_taskCreate_popup_input_1">確認しました</label>
        </div>
        <div class="bl_taskCreate_popup_footer">
          <button class="bl_taskCreate_popup_footer_cancel bl_taskCreate_popup_footer_cancel_1">キャンセル</button>
          <button class="bl_taskCreate_popup_footer_ok bl_taskCreate_popup_footer_ok_1">OK</button>
        </div>
      </div>
    </div>

    {{-- @if (config('app.env') === 'local')
      <script>
        /**
         *  テスト用フォーム自動入力
         * */
        document.addEventListener('DOMContentLoaded', function() {
          $task_date = document.getElementById('task_date');
          $task_memo = document.getElementById('task_memo');
          $rough_quantity = document.getElementById('rough_quantity');
          $delivery_route = document.getElementById('delivery_route');
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

          // $task_date.value = '2022-12-01';
          $rough_quantity.value = '111';
          $delivery_route.value = 'ルートメモ';
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
        });
      </script>
    @endif --}}
  @endsection

  @section('script_bottom')
    {{-- <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script> --}}
    <script>
      function handlePopup() {
        let action;

        // Elements
        const form = document.querySelector('.js_confirm');
        const popup = document.querySelector('.bl_taskCreate_popup');
        const buttonCreate = document.querySelector('.bl_taskCreate_inner_content_form_submit .create');
        const buttonCreateTemplate = document.querySelector('.bl_taskCreate_inner_content_form_submit .template');
        const buttonClose = document.querySelector('.bl_taskCreate_popup_footer_cancel')
        const buttonConfirm = document.querySelector('.bl_taskCreate_popup_footer_ok')
        const checkbox = document.querySelector(`#bl_taskCreate_popup_input_1`);
        const headingPopup = document.querySelector('.bl_taskCreate_popup_heading')
        const questionPopup = document.querySelector('.bl_taskCreate_popup_question');

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

        // Create normal
        buttonCreate.addEventListener('click', (e) => {
          e.preventDefault();
          popup.classList.add('show');
          headingPopup.innerHTML = '稼働依頼登の確認';
          questionPopup.innerHTML = 'この内容で登録します。本当に宜しいですか？';
          action = 'create-normal';
        });

        // Create template
        buttonCreateTemplate.addEventListener('click', (e) => {
          e.preventDefault();
          popup.classList.add('show');
          headingPopup.innerHTML = '保存前の確認';
          questionPopup.innerHTML = 'この依頼を保存します。本当によろしいですか？';
          action = 'create-template';
        });

        // Close popup
        buttonClose.addEventListener('click', (e) => {
          popup.classList.remove('show');
          buttonConfirm.classList.add("disabled");
          checkbox.checked = false;
        });

        // Confirm popup
        buttonConfirm.addEventListener('click', (e) => {
          if (!checkbox.checked) return;

          if (window.location.search.includes('template')) {
            // Update
            const template_id = new URLSearchParams(window.location.search).get('template')

            if (template_id) {
              form.action = '{{ route("delivery_office.driver_task.store") }}/' + action + '/' + template_id;
            }
          } else {
            // Case normal
            form.action = '{{ route("delivery_office.driver_task.store") }}/' + action;
          }

          form.submit();
        })
      }
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        handlePopup();
        /**
         * 集荷先の選択肢による処理。
         * 新しい集荷先の場合はフォームを表示
         */
        (function() {
          let $pickup_addr_radio_list = document.querySelectorAll('.js_pickup_addr_radio'); // ラジオボタンリスト
          let $js_pickup_addr_form =
            document.querySelector('.js_pickup_addr_form'); // 集荷先住所を登録するフォーム
          let $pickup_addr_new = document.getElementById('pickup_addr_new'); // 新しい住所radio


          if ($pickup_addr_new.checked == true) {
            $js_pickup_addr_form.classList.add('js_active'); // フォーム表示
          } else {
            $js_pickup_addr_form.classList.remove('js_active'); // フォーム非表示
          }

          // ラジオ選択肢にイベントリスナーを設置。
          $pickup_addr_radio_list.forEach($radio => {

            $radio.addEventListener('change', (e) => {
              if ($pickup_addr_new.checked == true) {
                $js_pickup_addr_form.classList.add('js_active'); // フォーム表示
              } else {
                $js_pickup_addr_form.classList.remove('js_active'); // フォーム非表示
              }
            });
          });
        }());

        function checkAndShowSystemPrice() {
          let driverTaskPlanId = document.getElementById('driver_task_plan_id').value;
          let divSystemPrice = document.querySelector('.div_system_price');

          if (driverTaskPlanId === '2') {
              divSystemPrice.style.display = 'flex';
          } else {
              divSystemPrice.style.display = 'none';
          }
      }

        checkAndShowSystemPrice();
        document.getElementById('driver_task_plan_id').addEventListener('change', checkAndShowSystemPrice);

        /**
         * 料金計算
         */
        (function() {
          let task_date = document.getElementById('task_date'); // 稼働日セレクトボックス
          let e_systemPrice = document.querySelector('.js_systemPrice'); // システム利用料金
          let e_js_busySystemPrice_p = document.querySelector('.js_busySystemPrice_p'); // システム利用料金(繁忙期) pタグ
          let e_busySystemPrice = document.querySelector('.js_busySystemPrice'); // システム利用料金(繁忙期)
          let e_emergencyPrice = document.querySelector('.js_emergencyPrice'); // 緊急依頼料金
          let e_tax = document.querySelector('.js_tax'); // 消費税
          let e_total = document.querySelector('.js_totalPrice'); // 合計
          let e_freight_cost = document.querySelector('.js_freightCost'); // ドライバー運賃

          let e_systemPrice_input = document.querySelector('.js_systemPrice_input'); // システム利用料金 input
          let e_busySystemPrice_input = document.querySelector('.js_busySystemPrice_input'); // システム利用料金(繁忙期) input
          let e_emergencyPrice_input = document.querySelector('.js_emergencyPrice_input'); // 緊急依頼料金 input
          let e_tax_input = document.querySelector('.js_tax_input'); // 消費税 input
          let e_total_input = document.querySelector('.js_totalPrice_input'); // 合計 input
          let e_freight_cost_input = document.querySelector('.js_freightCost_input'); // ドライバー運賃 input

          let dummy_freight_cost_input = document.getElementById('dummy_freight_cost'); // dummy ドライバー運賃 input
          let el_freight_cost_error_msg = document.querySelector('.js_freight_cost_error_msg');

          let system_price_input = document.getElementById('system_price'); // システム利用料金 input
          let system_price_error_msg = document.querySelector('.js_system_price_error_msg');
          let divSystemPrice = document.querySelector('.div_system_price');

          let input_driver_task_plan_id = document.getElementById('driver_task_plan_id'); // 稼働依頼プランIDのフォーム

          // // 初期状態、システム利用料金(繁忙期)は非表示
          if (!e_busySystemPrice_input.value) {
            console.log(e_busySystemPrice_input.value);
            e_js_busySystemPrice_p.style.display = 'none';
          }

          input_driver_task_plan_id.addEventListener('change', () => {
              let selectedValue = input_driver_task_plan_id.value;
              if (selectedValue === '2') {
                  divSystemPrice.style.display = 'flex';
              } else {
                  divSystemPrice.style.display = 'none';
              }
            if (task_date.value && dummy_freight_cost_input.value && input_driver_task_plan_id.value) {
              getTaskPriceApi();
            }
          })

          // 稼働日によって料金が変更される。
          task_date.addEventListener('change', () => {
            if (task_date.value && dummy_freight_cost_input.value && input_driver_task_plan_id.value) {
              getTaskPriceApi();
            }
          })

          // 運賃は制度運賃により異なります
          system_price_input.addEventListener('change', () => {
              system_price_error_msg.textContent = '';
            if (task_date.value && system_price_input.value && input_driver_task_plan_id.value) {
                getTaskPriceApi();
            }
        });

          // ドライバー運賃によって料金が変更される。
          dummy_freight_cost_input.addEventListener('change', () => {
            el_freight_cost_error_msg.textContent = '';
            if (task_date.value && dummy_freight_cost_input.value && input_driver_task_plan_id.value) {
              getTaskPriceApi();
            }
          });

          // Mode update template
          if (window.location.search.includes('template')) {
            getTaskPriceApi()
          }

          /**
           * 請求金額を取得するAPIを叩く
           */
          function getTaskPriceApi() {

            axios.get("/delivery-office/driver-task/calc-basic-price", {
                params: {
                  driver_task_plan_id: input_driver_task_plan_id.value,
                  task_date: task_date.value,
                  freight_cost: dummy_freight_cost_input.value,
                  system_price: system_price_input.value,
                }
              })
              .then(function(res) {
                console.log(res["data"]);
                let selectedValue = input_driver_task_plan_id.value;
                if (selectedValue !== '2') {
                    system_price_input.value = res["data"]['system_price'];
                }

                let $data = res["data"];
                e_systemPrice.textContent = $data['system_price'] ? $data['system_price'].toLocaleString() : '-';
                e_busySystemPrice.textContent = $data['busy_system_price'] ? $data['busy_system_price'].toLocaleString() : '-';
                e_emergencyPrice.textContent = $data['emergency_price'].toLocaleString();
                e_total.textContent = $data['total_including_tax'].toLocaleString();
                e_tax.textContent = $data['tax'].toLocaleString();
                e_freight_cost.textContent = $data['freight_cost'].toLocaleString();
                e_systemPrice.textContent = $data['system_price'].toLocaleString();

                e_systemPrice_input.value = $data['system_price'] ?? '';
                e_busySystemPrice_input.value = $data['busy_system_price'] ?? '';
                e_emergencyPrice_input.value = $data['emergency_price'] ?? '';
                e_tax_input.value = $data['tax'] ?? '';
                e_total_input.value = $data['total_including_tax'] ?? '';
                e_freight_cost_input.value = $data['freight_cost'] ?? '';

                if (!e_busySystemPrice_input.value) {
                  e_js_busySystemPrice_p.style.display = 'none';
                } else {
                  e_js_busySystemPrice_p.style.display = 'block';
                }

              })
              .catch(function(error) {
                console.log(error);
                console.log('バリデーション ', error.response.data.errors);
                // el_freight_cost_error_msg.textContent = error.response.data.errors['freight_cost'].toLocaleString();

                e_systemPrice.textContent = '-';
                e_emergencyPrice.textContent = '-';
                e_total.textContent = '-';
                e_tax.textContent = '-';
                e_freight_cost.textContent = '-';

                e_systemPrice_input.value = system_price_input.value;
                e_emergencyPrice_input.value = null;
                e_tax_input.value = null;
                e_total_input.value = null;
                e_freight_cost_input.value = dummy_freight_cost_input.value;
              });
          }
        }());


        /**
         * ドライバー検索
         */
        (function() {
          let input_search_driver = document.getElementById('search_driver'); // ドライバー検索フォーム
          let input_driver_task_plan_id = document.getElementById('driver_task_plan_id'); // 稼働依頼プランIDのフォーム
          let el_search_driver_ul = document.querySelector('.js_search_driver_ul'); // 取得したドライバーを表示する要素
          let el_driver_name = document.querySelector('.js_driver_name'); // ドライバーの名前を表示する要素
          let input_driver_id = document.getElementById('driver_id'); // ドライバーIDのフォーム
          let driver_list = []; // 検索APIで取得したドライバーの一覧
          let driver_id = ''; // 検索結果から選択したドライバーのID

          input_driver_task_plan_id.addEventListener('input', (event) => {
            input_driver_task_plan_id = document.getElementById('driver_task_plan_id');
          });

          /**
           * ドライバー入力検索インベントリスナー
           */
          input_search_driver.addEventListener('input', (event) => {

            // 検索入力した値が存在する場合の処理
            if (event.target.value) {

              // 検索結果UIを動的に変更
              (async () => {
                await getDriverIndexApi(event.target.value);
                el_search_driver_ul.innerHTML = '';
                driver_list.forEach(($driver) => {
                  $add_html = `<li class="" data-driver-id="${$driver['id']}" data-driver-name="${$driver['name_sei']} ${$driver['name_mei']}">${$driver['name_sei']} ${$driver['name_mei']} (id:${$driver['id']})</li>`;
                  el_search_driver_ul.insertAdjacentHTML('beforeend', $add_html);
                });


                // ドライバー一覧から、選択したものをフォームに反映
                let el_search_driver_li_all = el_search_driver_ul.querySelectorAll('li');
                el_search_driver_li_all.forEach((li) => {
                  li.addEventListener('click', () => {
                    el_driver_name.textContent = li.dataset.driverName;
                    input_driver_id.value = li.dataset.driverId;
                    el_search_driver_ul.innerHTML = '';


                    /* URLパラメータ(driver_id)を書き換え */
                    let currentUrl = new URL(window.location.href); // 現在のURLを取得
                    let searchParams = new URLSearchParams(currentUrl.search);
                    searchParams.set("driver_id", li.dataset.driverId); // URLのパラメータ変更
                    currentUrl.search = searchParams.toString(); // 変更後のGETパラメータをURLにセット
                    window.history.pushState({}, '', currentUrl.href); // URLの履歴を更新（ページは再読み込みされない）
                  });
                });
              }).call(this);
            } else {
              el_search_driver_ul.innerHTML = '';
            }
          });

          /**
           * ドライバー検索API
           */
          async function getDriverIndexApi($keyword = "") {
            let that = this;
            await axios
              .get("/delivery-office/api/driver", {
                params: {
                  keyword: $keyword,
                  driver_task_plan_id: input_driver_task_plan_id.value,
                }
              })
              .then(function(res) {
                driver_list = res["data"]["data"]["data"]
                // console.log(driver_list);
              })
              .catch(function(error) {
                console.log(error);
              });
          }
        }());


        /* 稼働依頼プランがドライバープランに対応しているか判定 */
        (function() {
          let input_driver_task_plan_id = document.getElementById('driver_task_plan_id'); // 稼働依頼プランIDのフォーム
          let input_driver_id = document.getElementById('driver_id'); // ドライバーIDのフォーム
          let el_error_msg_driver_task_plan_id = document.querySelector('.js_error_msg_driver_task_plan_id');

          driver_task_plan_id.addEventListener('change', () => {
            input_driver_task_plan_id = document.getElementById('driver_task_plan_id');
            input_driver_id = document.getElementById('driver_id'); // ドライバーIDのフォーム

            axios
              .get("/delivery-office/api/driver-task-plan-allow-driver/check", {
                params: {
                  driver_task_plan_id: input_driver_task_plan_id.value,
                  driver_id: input_driver_id.value,
                }
              })
              .then(function(res) {
                console.log(res["data"]);
                el_error_msg_driver_task_plan_id.textContent = '';
              })
              .catch(function(error) {
                console.log(error);
                console.log('バリデーション ', error.response.data.errors.driver_task_plan_id[0]);
                el_error_msg_driver_task_plan_id.textContent = error.response.data.errors.driver_task_plan_id[0]
                el_error_msg_driver_task_plan_id
              });
          });
        }());

      });
    </script>
  @endsection
