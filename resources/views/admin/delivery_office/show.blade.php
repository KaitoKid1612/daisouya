@extends('layouts.admin.app')

@section('title')
  依頼者 詳細
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($office)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>依頼者 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_task.index', ['delivery_office_id' => $office->id]) }}" class="c_btn">稼働依頼一覧</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_task.create', ['delivery_office_id' => $office->id]) }}" class="c_btn">稼働依頼作成</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_register_delivery_office.index', ['delivery_office_id' => $office->id]) }}" class="c_btn">登録営業所</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_task_review.index', ['delivery_office_id' => $office->id]) }}" class="c_btn">レビュー</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.delivery_office.edit', ['office_id' => $office->id]) }}" class="c_btn">編集</a>
            </div>
            @if (!$office->trashed())
            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.delivery_office.destroy', ['office_id' => $office->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="hidden" name="type" value="soft">
                <input type="submit" value="ソフト削除" class="c_btn el_bg_red">
              </form>
            </div>
            @endif
            @if ($office->trashed())
              <div class="bl_show_inner_content_handle_item">
                <form action="{{ route('admin.delivery_office.restore_delete', ['office_id' => $office->id]) }}" method="POST" class="js_confirm">
                  @csrf
                  <input type="submit" value="ソフト削除から復元" class="c_btn">
                </form>
              </div>
            @endif

            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.delivery_office.destroy', ['office_id' => $office->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="hidden" name="type" value="force">
                <input type="submit" value="完全削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">
            @if ($office->trashed())
              <dl>
                <dt>削除状態</dt>
                <dd>ソフトデリート</dd>
              </dl>
            @endif
            <dl>
              <dt>ID</dt>
              <dd>{{ $office->id ?? '' }}</dd>
            </dl>

            <dl>
              <dt>配送会社</dt>
              <dd>{{ $office->joinCompany->name ?? ($office->delivery_company_name ?? '') }}</dd>
            </dl>

            <dl>
              <dt>営業所タイプ</dt>
              <dd>{{ $office->joinDeliveryOfficeType->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>営業所</dt>
              <dd>{{ $office->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>担当者名</dt>
              <dd>{{ $office->full_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>担当者名(カナ)</dt>
              <dd>{{ $office->full_name_kana ?? '' }}</dd>
            </dl>

            <dl>
              <dt>email</dt>
              <dd>{{ $office->email ?? '' }}</dd>
            </dl>

            <dl>
              <dt>住所</dt>
              <dd>{{ $office->full_post_code ?? '' }}</dd>
              <dd>{{ $office->full_addr ?? '' }}</dd>
            </dl>

            <dl>
              <dt>担当者電話番号</dt>
              <dd>{{ $office->manager_tel ?? '' }}</dd>
            </dl>

            <dl>
              <dt>請求に関するユーザの種類</dt>
              <dd>{{ $office->joinChargeUserType->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>stripe_id</dt>
              <dd>{{ $office->stripe_id ?? '' }}</dd>
            </dl>

            <dl>
              <dt>Stripe 顧客情報</dt>
              <dd class="el_scroll">{{ $office->stripe_user ?? '' }}</dd>
            </dl>

            <dl>
              <dt>Stripe インボイス リスト</dt>
              <dd class="el_scroll">{{ $office->stripe_invoice_list ?? '' }}</dd>
            </dl>

            <dl>
              <dt>Stripe 支払い方法一覧</dt>
              <dd class="el_scroll">{{ $payment_method_list ?? '' }}</dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $office->created_at ?? '' }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $office->updated_at ?? '' }}</dd>
            </dl>

            <dl>
              <dt>ソフト削除日</dt>
              <dd>{{ $office->deleted_at ?? '' }}</dd>
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
