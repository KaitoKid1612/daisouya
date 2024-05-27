@extends('layouts.admin.app')

@section('title')
  営業所登録申請 詳細
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($register_request)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>営業所登録申請 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            @if ($register_request->register_request_status_id == 1)
              <div class="bl_show_inner_content_handle_item">
                <a href="{{ route('admin.register_request_delivery_office.edit', ['register_request_id' => $register_request->id]) }}" class="c_btn">登録申請の処理ページ</a>
              </div>
            @endif
            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.register_request_delivery_office.destroy', ['register_request_id' => $register_request->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="hidden" name="type" value="force">
                <input type="submit" value="削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">
            <dl>
              <dt>ID</dt>
              <dd>{{ $register_request->id }}</dd>
            </dl>

            <dl>
              <dt>ステータス</dt>
              <dd>{{ $register_request->get_register_request_status->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>ユーザータイプ</dt>
              <dd>{{ $register_request->joinUserType->name ?? '非会員' }}</a>
            </dl>

            <dl>
              <dt>配送会社</dt>
              <dd>{{ $register_request->joinCompany->name ?? ($register_request->delivery_company_name ?? '') }}</dd>
            </dl>

            <dl>
              <dt>営業所</dt>
              <dd>{{ $register_request->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>名前</dt>
              <dd>{{ $register_request->full_name ?? '' }}</a>
              </dd>
            </dl>

            <dl>
              <dt>名前(読み仮名)</dt>
              <dd>{{ $register_request->full_name_kana ?? '' }}</dd>
            </dl>

            <dl>
              <dt>email</dt>
              <dd>{{ $register_request->email ?? '' }}</dd>
            </dl>

            <dl>
              <dt>住所</dt>
              <dd>{{ $register_request->full_post_code }}</dd>
              <dd>{{ $register_request->full_addr }}</dd>
            </dl>

            <dl>
              <dt>電話番号</dt>
              <dd>{{ $register_request->manager_tel ?? '' }}</dd>
            </dl>

            <dl>
              <dt>その他メッセージ</dt>
              <dd>{{ $register_request->message ?? '' }}</dd>
            </dl>

            <dl>
              <dt>登録の期限</dt>
              <dd>{{ $register_request->time_limit_at ?? '' }}</dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $register_request->created_at }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $register_request->updated_at }}</dd>
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
