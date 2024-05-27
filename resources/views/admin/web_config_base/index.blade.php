@extends('layouts.admin.app')

@section('title')
  基本設定
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  <div class="bl_show">
    <div class="bl_show_inner">
      <div class="bl_show_inner_head">
        <div class="bl_show_inner_head_ttl">
          <h2>基本設定</h2>
        </div>
      </div>

      <div class="bl_show_inner_content">
        <div class="bl_show_inner_content_handle">
          <div class="bl_show_inner_content_handle_item">
            <a href="{{ route('admin.web_config_base.edit') }}" class="c_btn">編集</a>
          </div>
        </div>
        <section class="bl_show_inner_content_data">
          <dl>
            <dt>ID</dt>
            <dd>{{ $config_base->id }}</dd>
          </dl>

          <dl>
            <dt>サイト名</dt>
            <dd>{{ $config_base->site_name ?? '' }}</dd>
          </dl>

          <dl>
            <dt>会社名</dt>
            <dd>
              {{ $config_base->company_name ?? '' }}</dd>
          </dl>

          <dl>
            <dt>会社名(カナ)</dt>
            <dd>
              {{ $config_base->company_name_kana ?? '' }}
            </dd>
          </dl>

          <dl>
            <dt>郵便番号</dt>
            <dd>
              {{ $config_base->full_post_code ?? '' }}
            </dd>
          </dl>

          <dl>
            <dt>住所</dt>
            <dd>
              {{ $config_base->full_addr ?? '' }}
            </dd>
          </dl>

          <dl>
            <dt>電話番号</dt>
            <dd>
              {{ $config_base->tel ?? '' }}
            </dd>
          </dl>

          <dl>
            <dt>特定商取引法に基づく表記 依頼者</dt>
            <dd class="el_scroll">{!! $config_base->commerce_law_delivery_office ?? '' !!}</dd>
          </dl>

          <dl>
            <dt>特定商取引法に基づく表記 ドライバー</dt>
            <dd class="el_scroll">{!! $config_base->commerce_law_driver ?? '' !!}</dd>
          </dl>

          <dl>
            <dt>ご利用規約 依頼者</dt>
            <dd class="el_scroll">
              {!! $config_base->terms_service_delivery_office ?? '' !!}
            </dd>
          </dl>

          <dl>
            <dt>ご利用規約 ドライバー</dt>
            <dd class="el_scroll">
              {!! $config_base->terms_service_driver ?? '' !!}
            </dd>
          </dl>

          <dl>
            <dt>プライバシーポリシー 依頼者</dt>
            <dd class="el_scroll">{!! $config_base->privacy_policy_delivery_office ?? '' !!}
            </dd>
          </dl>

          <dl>
            <dt>プライバシーポリシー ドライバー</dt>
            <dd class="el_scroll">{!! $config_base->privacy_policy_driver ?? '' !!}
            </dd>
          </dl>

          <dl>
            <dt>ご利用ガイド 依頼者</dt>
            <dd>
              <a href="{{ route('storage_file.show', ['path' => $config_base->user_guide_path_delivery_office]) }}" alt="" target="_blank">
                ご利用ガイド 依頼者
              </a>
            </dd>
          </dl>

          <dl>
            <dt>ご利用ガイド ドライバー</dt>
            <dd>
              <a href="{{ route('storage_file.show', ['path' => $config_base->user_guide_path_driver]) }}" alt="" target="_blank">
                ご利用ガイド ドライバー
              </a>
            </dd>
          </dl>

          <dl>
            <dt>振込情報</dt>
            <dd>{{ $config_base->transfer ?? '' }}
            </dd>
          </dl>

          <dl>
            <dt>作成日</dt>
            <dd>{{ $config_base->created_at }}</dd>
          </dl>

          <dl>
            <dt>更新日</dt>
            <dd>{{ $config_base->updated_at }}</dd>
          </dl>
        </section>
      </div>
    </div>
  </div>
@endsection
