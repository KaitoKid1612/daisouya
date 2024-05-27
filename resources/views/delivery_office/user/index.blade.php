@extends('layouts.delivery_office.app')

@section('title')
  アカウント
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_officeAccountIndex">
    <div class="bl_officeAccountIndex_inner">
      <div class="bl_officeAccountIndex_inner_head">
        <div class="bl_officeAccountIndex_inner_head_ttl">
          <h2>アカウント<span>/ account</span></h2>
        </div>
      </div>
      <div class="bl_officeAccountIndex_inner_content">
        <div class='bl_officeAccountIndex_inner_content_handle'>
          <a href="{{ route('delivery_office.user.edit', ['type' => 'user']) }}" class="c_link">編集</a>
          <a href="{{ route('delivery_office.user.edit', ['type' => 'password']) }}" class="c_link">パスワード変更</a>
          <a href="{{ route('delivery_office.user.edit', ['type' => 'email']) }}" class="c_link">メールアドレス変更</a>
          <a href="{{ route('delivery_office.payment_config.index') }}" class="c_link">支払い方法</a>
          <a href="{{ route('delivery_office.user.edit', ['type' => 'delete']) }}" class="c_link">退会する</a>

        </div>
        <div class="bl_officeAccountIndex_inner_content_info">

          {{-- 無料ユーザーのみ表示 --}}
          @if ($office->charge_user_type_id == 2)
            <dl>
              <dt>請求に関するユーザーの種類</dt>
              <dd>{{ $office->joinChargeUserType->name ?? '' }}</dd>
            </dl>
          @endif
          @php
            $data = [
                ['text' => '配送会社', 'val' => $office->joinCompany->name ?? ($office->delivery_company_name ?? '')], //
                ['text' => '営業所名・デポ名', 'val' => $office->name ?? ''], //
                ['text' => '営業所郵便番号', 'val' => $office->full_post_code ?? ''], //
                ['text' => '営業所住所', 'val' => $office->full_addr ?? ''], //
                ['text' => '担当者名 姓', 'val' => $office->manager_name_sei ?? ''], //
                ['text' => '担当者名 名', 'val' => $office->manager_name_mei ?? ''], //
                ['text' => '担当者名 姓 (カナ)', 'val' => $office->manager_name_sei_kana ?? ''], //
                ['text' => '担当者名 名 (カナ)', 'val' => $office->manager_name_mei_kana ?? ''], //
                ['text' => '担当者メールアドレス', 'val' => $office->email ?? ''], //
                ['text' => '担当者電話番号', 'val' => $office->manager_tel ?? ''], //
            ];
            // print_r($data);
          @endphp
          @foreach ($data as $item)
            <dl>
              <dt>{{ $item['text'] }}:</dt>
              <dd>{{ $item['val'] }}</dd>
            </dl>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection
