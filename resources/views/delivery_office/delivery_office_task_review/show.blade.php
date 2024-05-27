@extends('layouts.delivery_office.app')

@section('title')
  レビュー
@endsection


@section('content')
  <div class="bl_officeReviewShow">
    <div class="bl_officeReviewShow_inner">
      <div class="bl_officeReviewShow_inner_head">
        <div class="bl_officeReviewShow_inner_head_ttl">
          <h2>レビュー<span>/ review</span></h2>
        </div>
      </div>
      <div class="bl_officeReviewShow_inner_content">
        <section class="bl_driverShow_inner_content_review">
          <div class="bl_driverShow_inner_content_review_head">
            <p class="bl_driverShow_inner_content_review_head_taskId">依頼ID: {{ $review->driver_task_id }}</p>
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
              <dt>稼働日</dt>
              <dd><a href="{{ route('driver.driver_task.show', ['task_id' => $review->driver_task_id]) }}" class='c_normal_link'>{{ $review->joinTask->taskDateYmd ?? '' }}</a></dd>
            </dl>
            <dl>
              <dt>投稿者</dt>
              <dd>
                {{ $review->joinOffice->name ?? '' }} {{ $review->joinOffice->full_name }}</dd>
            </dl>
            <dl>
              <dt>担当ドライバー</dt>
              <dd>
                <a href="{{ route('driver.user.show', ['driver_id' => $review->driver_id]) }}" class="c_normal_link">{{ $review->joinDriver->full_name }}
                </a>
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

@endsection