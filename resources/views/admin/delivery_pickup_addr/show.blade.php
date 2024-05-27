@extends('layouts.admin.app')

@section('title')
  営業所 集荷先住所 詳細
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($pickup_addr)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>営業所 集荷先住所 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.delivery_pickup_addr.edit', ['pickup_id' => $pickup_addr->id]) }}"
                class="c_btn">編集</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.delivery_pickup_addr.destroy', ['pickup_id' => $pickup_addr->id]) }}"
                method="POST" class="js_confirm">
                @csrf
                <input type="submit" value="削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">
            <dl>
              <dt>ID</dt>
              <dd>{{ $pickup_addr->id }}</dd>
            </dl>

            <dl>
              <dt>配送営業所(依頼者)</dt>
              <dd>
                @if ($pickup_addr->delivery_office_id && $pickup_addr->joinOffice)
                  <a
                    href="{{ route('admin.delivery_office.show', [
                        'office_id' => $pickup_addr->delivery_office_id,
                    ]) }}">
                    {{ $pickup_addr->joinOffice->name ?? '' }}
                  </a>
                @elseif ($pickup_addr->delivery_office_id && !$pickup_addr->joinOffice)
                  <a
                    href="{{ route('admin.delivery_office.show', [
                        'office_id' => $pickup_addr->delivery_office_id,
                    ]) }}">
                    {{ $pickup_addr->joinOffice->name ?? 'データなしorソフト削除済み' }}
                  </a>
                @else
                  なし
                @endif

              </dd>
            </dl>

            <dl>
              <dt>集荷先会社</dt>
              <dd>{{ $pickup_addr->delivery_company_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>集荷先営業所名</dt>
              <dd>{{ $pickup_addr->delivery_office_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>集荷先住所</dt>
              <dd>{{ $pickup_addr->full_post_code ?? '' }}</dd>
              <dd>{{ $pickup_addr->full_addr ?? '' }}</dd>
            </dl>

            <dl>
              <dt>集荷先メールアドレス</dt>
              <dd>{{ $pickup_addr->email ?? '' }}</dd>
            </dl>

            <dl>
              <dt>集荷先電話番号</dt>
              <dd>{{ $pickup_addr->tel ?? '' }}</dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $pickup_addr->created_at }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $pickup_addr->updated_at }}</dd>
            </dl>
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
