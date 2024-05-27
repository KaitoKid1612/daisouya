@extends('layouts.admin.app')

@section('title')
  お問い合わせ 詳細
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($web_contact)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>お問い合わせ 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.web_contact.edit', ['contact_id' => $web_contact->id]) }}" class="c_btn">ステータス編集</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.web_contact.destroy', ['contact_id' => $web_contact->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="hidden" name="type" value="force">
                <input type="submit" value="削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">
            <dl>
              <dt>ID</dt>
              <dd>{{ $web_contact->id }}</dd>
            </dl>

            <dl>
              <dt>ステータス</dt>
              <dd>{{ $web_contact->get_web_contact_status->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>ユーザータイプ</dt>
              <dd>{{ $web_contact->joinUserType->name ?? '非会員' }}</a>
            </dl>

            <dl>
              <dt>名前</dt>
              <dd>{{ $web_contact->full_name ?? '' }}</a>
              </dd>
            </dl>

            <dl>
              <dt>名前(読み仮名)</dt>
              <dd>{{ $web_contact->full_name_kana ?? '' }}</dd>
            </dl>

            <dl>
              <dt>email</dt>
              <dd>{{ $web_contact->email ?? '' }}</dd>
            </dl>

            <dl>
              <dt>電話番号</dt>
              <dd>{{ $web_contact->tel ?? '' }}</dd>
            </dl>

            <dl>
              <dt>お問い合わせタイプ</dt>
              <dd>{{ $web_contact->get_web_contact_type->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>題</dt>
              <dd>{{ $web_contact->title ?? '' }}</dd>
            </dl>

            <dl>
              <dt>内容</dt>
              <dd>{{ $web_contact->text ?? '' }}</dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $web_contact->created_at }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $web_contact->updated_at }}</dd>
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
