@extends('layouts.delivery_office.app')

@section('title')
  ドライバー詳細
@endsection

@section('content')
  @if ($driver)
    <div class="bl_driverShow">
      <div class="bl_driverShow_inner">
        <div class="bl_driverShow_inner_head">
          <div class="bl_driverShow_inner_head_ttl">
            <h2>ドライバー詳細<span>/ driver profile</span></h2>
          </div>
        </div>

        <div class="bl_driverShow_inner_content">
          <div class="bl_driverShow_inner_content_profile">
            <div class="bl_driverShow_inner_content_profile_icon">
              <img src="{{ route('storage_file.show', ['path' => $driver->icon_img, 'type' => 'user_icon']) }}" alt="">
            </div>
            <div class="bl_driverShow_inner_content_profile_name">
              <p>{{ $driver->full_name ?? '' }}</p>
            </div>
            <div class="bl_driverShow_inner_content_profile_task">
              <a href="{{ route('delivery_office.driver_task.create', ['driver_id' => $driver->id]) }}">このドライバーに依頼する</a>
            </div>
            <div class="bl_driverShow_inner_content_profile_info">
              <dl>
                <dt>ドライバープラン</dt>
                <dd>{{ $driver->joinDriverPlan->name ?? 'No Plan' }}</dd>
              </dl>
              <dl>
                <dt>移住エリア</dt>
                <dd>{{ $driver->joinAddr1->name . $driver->addr2 }}</dd>
              </dl>
              <dl>
                <dt>年齢</dt>
                <dd>{{ $driver->age }}</dd>
              </dl>
              <dl>
                <dt>登録済み営業所</dt>
                <dd>
                  @foreach ($register_office_list as $office)
                    <span>{{ $office->joinOffice->name ?? '' }}</span>
                  @endforeach
                </dd>
              </dl>
              <dl>
                <dt>経歴</dt>
                <dd>{{ $driver->career }}</dd>
              </dl>
              <dl>
                <dt>自己紹介</dt>
                <dd>{{ $driver->introduction }}</dd>
              </dl>
            </div>
          </div>


          <div class="bl_driverShow_inner_content_review">
            <div class="bl_driverShow_inner_content_review_title">
              <h3>{{ count($review_list, COUNT_RECURSIVE) ? 'ご依頼者様からのレビュー' : 'レビューはまだありません' }}</h3>
              <p>
                @if ($driver->join_driver_review_avg_score)
                  @php
                    $score = round($driver->join_driver_review_avg_score);
                  @endphp
                  @for ($i = 0; $i < $score; $i++)
                    <img src="{{ asset('images/delivery_office/icon/star.png') }}">
                  @endfor
                  @for ($i = 0; $i < 5 - $score; $i++)
                    <img src="{{ asset('images/delivery_office/icon/star_kara.png') }}">
                  @endfor
                  <span>({{ round($driver->join_driver_review_avg_score, 1) }})</span>
                  <span>{{ $driver->join_driver_review_count }}件</span>
                @endif
              </p>
            </div>
            <ul class="bl_driverShow_inner_content_review_list">
              @foreach ($review_list as $review)
                <li>
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
                  <dl>
                    <dt>ご利用日</dt>
                    <dd>{{ $review->joinTask->taskDateYmd ?? '' }}</dd>
                  </dl>
                  <dl>
                    <dt>投稿者</dt>
                    <dd>{{ $review->joinOffice->name ?? '' }}</dd>
                  </dl>
                  <dl>
                    <dt>タイトル</dt>
                    <dd>{{ $review->title }}</dd>
                  </dl>

                  <dl>
                    <dt>内容</dt>
                    <dd>{{ $review->text }}</dd>
                  </dl>
                </li>
              @endforeach

              @if (count($review_list) > 0)
                <p class="u_text_align_center"><a href={{ route('delivery_office.driver_task_review.index', [
                    'type' => 'all',
                    'driver_id' => $driver->id,
                ]) }} class="c_link2">一覧表示</a></p>
              @endif
          </div>
          <div id='schedule_position' class="bl_driverShow_inner_content_schedule">
            <div class="bl_driverShow_inner_content_schedule_title">
              <h3>スケジュール</h3>
            </div>
            <div class="c_calendar">
              <table>
                <thead>
                  <tr>
                    <th><a href="{{ route('delivery_office.driver.show', ['driver_id' => $driver->id, 'calendar_month' => $calendar->prev ?? '']) }}#schedule_position">&lt;</a>
                    </th>
                    <th><a href="{{ route('delivery_office.driver.show', ['driver_id' => $driver->id, 'calendar_month' => $calendar->next ?? '']) }}#schedule_position">&gt;</a>
                    </th>
                    <th colspan="3">{{ $calendar->year_month ?? '' }}</th>
                    <th colspan="2"><a href="{{ route('delivery_office.driver.show', ['driver_id' => $driver->id]) }}#schedule_position">Today</a></th>
                  </tr>

                </thead>
                <tbody class="active">
                  <tr class='el_day_week'>
                    <td>Sun</td>
                    <td>Mon</td>
                    <td>Tue</td>
                    <td>Wed</td>
                    <td>Thu</td>
                    <td>Fri</td>
                    <td>Sat</td>
                  </tr>
                  {{ $calendar->show() ?? '' }}
                </tbody>
              </table>
            </div>
          </div>
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
