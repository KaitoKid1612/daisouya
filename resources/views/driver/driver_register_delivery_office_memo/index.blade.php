@extends('layouts.driver.app')

@section('title')
  ドライバー登録営業所メモ一覧
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif
  <div class="bl_RegisterOfficeMemoIndex">
    <div class="bl_RegisterOfficeMemoIndex_inner">
      <div class="bl_RegisterOfficeMemoIndex_inner_head">
        <h2 class="bl_RegisterOfficeMemoIndex_inner_head_ttl">ドライバー登録営業所メモ一覧</h2>
        <div class="bl_RegisterOfficeMemoIndex_inner_head_btn">
          <a href="{{ route('driver.driver_register_delivery_office_memo.create') }}" class="c_btn">
            作成
          </a>
        </div>
      </div>
      <div class="bl_RegisterOfficeMemoIndex_inner_content">
        <ul class="bl_RegisterOfficeMemoIndex_inner_content_head">
          <li>編集</li>
          <li>配送会社</li>
          <li>営業所名</li>
          <li>郵便番号</li>
          <li>住所</li>
          <li>削除</li>
        </ul>
        <ul class="bl_RegisterOfficeMemoIndex_inner_content_body">
          @foreach ($register_office_memo_list as $register_office_memo)
            <li class="bl_RegisterOfficeMemoIndex_inner_content_body_li">
              <span>
                <div>
                  <a
                    href="{{ route('driver.driver_register_delivery_office_memo.edit', [
                        'register_office_memo_id' => $register_office_memo->id,
                    ]) }}" class="c_normal_link el_link">
                    編集
                  </a>
                </div>
                <div>{{ $register_office_memo->joinDeliveryCompany->name ?? '' }}</div>
                <div>{{ $register_office_memo->delivery_office_name ?? '' }}</div>
                <div>{{ $register_office_memo->full_post_code ?? '' }}</div>
                <div>{{ $register_office_memo->full_addr ?? '' }}</div>
                <div class="el_form">
                  <form method="POST"
                    action="{{ route('driver.driver_register_delivery_office_memo.destroy', [
                        'register_office_memo_id' => $register_office_memo->id,
                    ]) }}"
                    class="js_confirm">
                    @csrf
                    <input type="submit" value="削除" class="c_btn el_bg_red el_btn">
                  </form>
                </div>
              </span>
            </li>
          @endforeach
        </ul>
      </div>
      {{ $register_office_memo_list->links('parts.pagination') }}
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection
