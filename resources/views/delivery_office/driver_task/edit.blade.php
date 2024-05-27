@extends('layouts.delivery_office.app')

@section('title')
  稼働依頼支払い方法変更
@endsection

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

  @if ($task)
    <div class="bl_taskEditPayment">
      <div class="bl_taskEditPayment_inner">
        <div class="bl_taskEditPayment_inner_head">
          <div class="bl_taskEditPayment_inner_head_ttl">
            <h2>稼働依頼 支払い方法変更<span>/ edit payment</span></h2>
          </div>
        </div>
        <div class="bl_taskEditPayment_inner_content">
          @if ($payment_method_list)
            <div class="bl_taskEditPayment_inner_content_form">
              <form action="{{ route('delivery_office.driver_task.update', ['task_id' => $task->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="hidden" name="type" value="payment_method">
                <section class="bl_taskEditPayment_inner_content_form_payment">
                  <div class="bl_taskEditPayment_inner_content_form_payment_title">
                    <h3>支払い方法を選択してください</h3>
                  </div>

                  <div class="bl_taskEditPayment_inner_content_form_payment_radioList">
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
                  </div>

                </section>
                <div class="bl_taskEditPayment_inner_content_form_submit">
                  <input type="submit" value="この内容で支払い方法を変更する" class="c_btn">
                </div>
              </form>
            </div>
          @endif
          <div class="bl_taskEditPayment_inner_content_info">
            <p>有効なクレジットカードがない場合、または新規のクレジットカードを利用する場合は <a href="{{ route('delivery_office.payment_config.create') }}" target="_blank" class="c_normal_link">クレジットカード登録</a>から登録してください。登録しましたらこのページを更新して支払い方法を変更してください。</p>

          </div>
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
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
