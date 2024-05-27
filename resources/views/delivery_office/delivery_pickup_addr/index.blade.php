@extends('layouts.delivery_office.app')

@section('title')
  集荷先住所登録
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

  <div class="bl_pickupAddrIndex">
    <div class="bl_pickupAddrIndex_inner">
      <form action="{{ route('delivery_office.delivery_pickup_addr.index') }}" method="GET">


        <div class="bl_pickupAddrIndex_inner_head">
          <div class="bl_pickupAddrIndex_inner_head_ttl">
            <h2>集荷先住所登録<span>/ pickup addr </span></h2>
          </div>
          <div class="bl_pickupAddrIndex_inner_head_keyword">
            <input type="text" name='keyword' id="keyword" placeholder="キーワード"
              value={{ old('keyword', $_GET['keyword'] ?? '') }}>
          </div>
          <div class="bl_pickupAddrIndex_inner_head_filter">
            <div class="bl_pickupAddrIndex_inner_head_filter_submit">
              <input type="submit" value="検索">
            </div>
          </div>

        </div>
      </form>

      <div class="bl_pickupAddrIndex_inner_content">
        <div class='bl_pickupAddrIndex_inner_content_handle'>
          <a href="{{ route('delivery_office.delivery_pickup_addr.create') }}" class="c_link">
            作成
          </a>
        </div>
        <ul>
          @foreach ($pickup_addr_list as $pickup_addr)
            <li>
              <section>
                <div>
                  <dl>
                    <dt>配送会社 営業所</dt>
                    <dd>{{ $pickup_addr->delivery_company_name ?? '' }} {{ $pickup_addr->delivery_office_name ?? '' }}
                    </dd>
                  </dl>
                  <dl>
                    <dt>住所</dt>
                    <dd>{{ $pickup_addr->full_post_code ?? '' }} {{ $pickup_addr->full_addr ?? '' }}</dd>
                  </dl>
                  <dl>
                    <dt>メールアドレス</dt>
                    <dd>{{ $pickup_addr->email ?? '' }}</dd>
                  </dl>
                  <dl>
                    <dt>電話番号</dt>
                    <dd>{{ $pickup_addr->tel ?? '' }}</dd>
                  </dl>

                </div>
              </section>
              <div class="bl_pickupAddrIndex_inner_content_link">
                <a
                  href="{{ route('delivery_office.delivery_pickup_addr.edit', ['pickup_id' => $pickup_addr->id]) }}"
                  class="c_link">編集</a>
                <form
                  action="{{ route('delivery_office.delivery_pickup_addr.destroy', ['pickup_id' => $pickup_addr->id]) }}"
                  method="POST" class="js_confirm">
                  @csrf
                  <input type="submit" value="削除" class="c_btn c_btn_bgRed">
                </form>
              </div>
            </li>
          @endforeach
        </ul>
      </div>
      {{ $pickup_addr_list->links('parts.pagination') }}
    </div>
  </div>
@endsection

@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
@endsection