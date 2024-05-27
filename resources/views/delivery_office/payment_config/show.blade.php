@extends('layouts.delivery_office.app')

@section('title')
  ドライバー検索
@endsection

@section('content')
  {{-- メッセージ --}}
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_paymentShow">
    <div class="bl_paymentShow_inner">
      <div class="bl_paymentShow_inner_head">
        <div class="bl_paymentShow_inner_head_ttl">
          <h2>支払い方法 設定<span>/ payment</span></h2>
        </div>
      </div>
      <div class="bl_paymentShow_inner_content">
        @if ($payment_item)
          <section>
            <p>カード会社: {{ $payment_item->card->brand }}</p>
            <p>期限: {{ $payment_item->card->exp_month }} / {{ $payment_item->card->exp_year }}</p>
            <p>番号: ****{{ $payment_item->card->last4 }}</p>
            <p>名義人: {{ $payment_item->billing_details->name ?? '' }}</p>
          </section>

          <div class="bl_paymentShow_inner_content_btnbox">
            <form
              action="{{ route('delivery_office.payment_config.destroy', ['payment_id' => $payment_item->id]) }}"
              method="POST" class="js_confirm">
              @csrf
              <input type="submit" value="削除" class="c_btn c_btn_bgRed">
            </form>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
