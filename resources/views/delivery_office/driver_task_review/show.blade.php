@extends('layouts.delivery_office.app')

@section('title')
  レビュー詳細
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
    <div class="bl_reviewShow">
      <div class="bl_reviewShow_inner">
        <div class="bl_reviewShow_inner_head">
          <div class="bl_reviewShow_inner_head_ttl">
            <h2>レビュー<span>/ review</span></h2>
          </div>
        </div>
        <div class="bl_reviewShow_inner_content">
          <section class="bl_driverShow_inner_content_review">
            <div class="bl_driverShow_inner_content_review_head">
              <p class="bl_driverShow_inner_content_review_head_taskId">
                <a href="{{ route('delivery_office.driver_task.show', [
                    'task_id' => $review->driver_task_id,
                ]) }}" class="c_btn">依頼ID: {{ $review->joinTask->id }}</a>
              </p>
              <p class="bl_driverShow_inner_content_review_head_score">
                @if ($review->score)
                  @php
                    $score = round($review->score);
                  @endphp
                  @for ($i = 0; $i < $score; $i++)
                    <img src="{{ asset('images/delivery_office/icon/star.png') }}">
                  @endfor
                  @for ($i = 0; $i < 5 - $score; $i++)
                    <img src="{{ asset('images/delivery_office/icon/star_kara.png') }}">
                  @endfor
                @endif
              </p>
            </div>
            <div class="bl_driverShow_inner_content_review_body">
              <dl>
                <dt>レビューID</dt>
                <dd>{{ $review->id ?? '' }}</dd>
              </dl>
              <dl>
                <dt>稼働日</dt>
                <dd>{{ $review->joinTask->taskDateYmd ?? '' }}</dd>
              </dl>
              <dl>
                <dt>投稿者</dt>
                <dd>{{ $review->joinOffice->name ?? '' }} {{ $review->joinOffice->manager_name_sei }}
                  {{ $review->joinOffice->manager_name_mei }}</dd>
              </dl>
              <dl>
                <dt>担当ドライバー</dt>
                <dd>
                  @if ($review->driver_id && $review->joinDriver)
                    <a href="{{ route('delivery_office.driver.show', ['driver_id' => $review->driver_id]) }}" class="c_normal_link">
                      {{ $review->joinDriver->full_name ?? '' }}
                    </a>
                  @elseif($review->driver_id && !$review->joinDriver)
                    データなし
                  @else
                    指定なし
                  @endif
                </dd>
              </dl>
              <dl>
                <dt>タイトル</dt>
                <dd>{{ $review->title }}</dd>
              </dl>

              <dl>
                <dt>内容</dt>
                <dd>{{ $review->text }}</dd>
              </dl>
            </div>
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