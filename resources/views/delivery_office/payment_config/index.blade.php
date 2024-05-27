@extends('layouts.delivery_office.app')

@section('title')
  支払い方法 一覧
@endsection

@section('content')
  {{-- メッセージ --}}
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
        {{ $msg ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_paymentIndex">
    <div class="bl_paymentIndex_inner">
      <div class="bl_paymentIndex_inner_head">
        <div class="bl_paymentIndex_inner_head_ttl">
          <h2>支払い方法 設定<span>/ payment</span></h2>
        </div>
      </div>
      <div class="bl_paymentIndex_inner_content">
        <div class='bl_paymentIndex_inner_content_handle'>
          <a href="{{ route('delivery_office.payment_config.create') }}" class="c_link">
            作成
          </a>
        </div>
        <ul>
          @foreach ($payment_method_list as $payment_item)
            <li>
              <a href="{{ route('delivery_office.payment_config.show', ['payment_id' => $payment_item->id]) }}">
                <p>カード会社: {{ $payment_item->card->brand }}</p>
                <p>期限: {{ $payment_item->card->exp_month }}/{{ $payment_item->card->exp_year }}</p>
                <p>番号: ****{{ $payment_item->card->last4 }}</p>
                <p>名義人: {{ $payment_item->billing_details->name ?? '' }}</p>
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
@endsection
