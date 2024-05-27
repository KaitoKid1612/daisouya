@extends('layouts.delivery_office.app')

@section('title')
  レビュー一覧
@endsection

@section('content')
  <div class="bl_officeReviewIndex">
    <div class="bl_officeReviewIndex_inner">
      <div class="bl_officeReviewIndex_inner_head">
        <div class="bl_officeReviewIndex_inner_head_ttl">
          <h2>ドライバー{{$driver ? "($driver->full_name)" : ""}}へのレビュー一覧<span>/ review list</span></h2>
        </div>
      </div>
      <div class="bl_officeReviewIndex_inner_content">
        <div class="bl_officeReviewIndex_inner_content_review">
          <ul class="bl_officeReviewIndex_inner_content_review_list">
            @foreach ($review_list as $review)
              <li class="bl_officeReviewIndex_inner_content_review_list_li">
                <div class="bl_officeReviewIndex_inner_content_review_list_li_head">
                  <a href="{{ route('delivery_office.driver_task.show', [
                      'task_id' => $review->driver_task_id,
                  ]) }}">稼働ID: {{ $review->driver_task_id }}</a>
                  <p>
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

                <div class="bl_officeReviewIndex_inner_content_review_list_li_body">
                  <dl>
                    <dt>ご利用日</dt>
                    <dd>{{ $review->joinTask->taskDateYmd ?? '' }}</dd>
                  </dl>
                  <dl>
                    <dt>投稿者(依頼者)</dt>
                    <dd>{{$review->joinOffice->joinCompany->name ?? ''}} {{$review->joinOffice->delivery_company_name ?? ''}} {{ $review->joinOffice->name ?? '' }}</dd>
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
              </li>
            @endforeach
        </div>
      </div>
      {{ $review_list->links('parts.pagination') }}

    </div>
  </div>
@endsection
