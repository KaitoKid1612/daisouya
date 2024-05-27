@extends('layouts.admin.app')

@section('title')
  依頼者レビュー 詳細
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($review)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>依頼者レビュー 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.delivery_office_task_review.edit', ['review_id' => $review->id]) }}" class="c_btn">編集</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.delivery_office_task_review.destroy', ['review_id' => $review->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="submit" value="削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">
            <dl>
              <dt>ID</dt>
              <dd>{{ $review->id ?? '' }}</dd>
            </dl>

            <dl>
              <dt>稼働日</dt>
              <dd>
                <a href="{{ route('admin.driver_task.show', ['task_id' => $review->driver_task_id]) }}">
                  {{ $review->joinTask->taskDateYmd ?? '' }}
                </a>
              </dd>
            </dl>

            <dl>
              <dt>ドライバー名</dt>
              <dd>
                @if ($review->driver_id && $review->joinDriver)
                  <a href="{{ route('admin.driver.show', ['driver_id' => $review->driver_id]) }}" class="c_normal_link">
                    {{ $review->joinDriver->full_name ?? '' }}
                  </a>
                @elseif ($review->driver_id && !$review->joinDriver)
                  <a href="{{ route('admin.driver.show', ['driver_id' => $review->driver_id]) }}" class="c_normal_link">
                    データなしorソフト削除済み
                  </a>
                @else
                  なし
                @endif
              </dd>
            </dl>

            <dl>
              <dt>配送営業所(依頼者)</dt>
              <dd>
                @if ($review->delivery_office_id && $review->joinOffice)
                  <a href="{{ route('admin.delivery_office.show', ['office_id' => $review->delivery_office_id]) }}" class="c_normal_link">
                    {{ $review->joinOffice->name ?? '' }}
                  </a>
                @elseif ($review->delivery_office_id && !$review->joinOffice)
                  <a href="{{ route('admin.delivery_office.show', ['office_id' => $review->delivery_office_id]) }}" class="c_normal_link">
                    データなしorソフト削除済み
                  </a>
                @else
                  なし
                @endif
              </dd>
            </dl>

            <dl>
              <dt>評価点</dt>
              <dd>{{ $review->score ?? '' }}</dd>
            </dl>

            <dl>
              <dt>レビュータイトル</dt>
              <dd>{{ $review->title ?? '' }}</dd>
            </dl>

            <dl>
              <dt>レビュー内容</dt>
              <dd>{{ $review->text ?? '' }}</dd>
            </dl>

            <dl>
              <dt>公開ステータス</dt>
              <dd>{{ $review->joinPublicStatus->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $review->created_at ?? '' }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $review->updated_at ?? '' }}</dd>
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
