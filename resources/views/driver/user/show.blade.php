@extends('layouts.driver.app')

@section('title')
  アカウント
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($driver)

    <div class="bl_driverShow">
      <div class="bl_driverShow_inner">
        <div class="bl_driverShow_inner_head">
          <div class="bl_driverShow_inner_head_ttl">
            <h2>ドライバー詳細<span>/ driver profile</span></h2>
          </div>
        </div>

        <div class="bl_driverShow_inner_top">

        </div>

        <div class="bl_driverShow_inner_content">
          {{-- ログインID と ドライバーID が同じ場合 --}}
          @if (Auth::guard('drivers')->id() === $driver->id)
            <section class="bl_driverShow_inner_content_editLink">
              <a href="{{ route('driver.user.edit', ['type' => 'icon']) }}">アイコン設定</a>
              <a href="{{ route('driver.user.edit', ['type' => 'user']) }}">アカウント情報編集</a>
              <a href="{{ route('driver.user.edit', ['type' => 'email']) }}">メールアドレス変更</a>
              <a href="{{ route('driver.user.edit', ['type' => 'password']) }}">パスワード変更</a>
              <a href="{{ route('driver.user.edit', ['type' => 'office']) }}">営業所登録</a>
              <a href="{{ route('driver.user.edit', ['type' => 'delete']) }}">退会する</a>
            </section>
          @endif

          <section class="bl_driverShow_inner_content_top">
            <div class="bl_driverShow_inner_content_top_icon">
              <img src="{{ route('storage_file.show', ['path' => $driver->icon_img, 'type' => 'user_icon']) }}" alt="">
            </div>
            <div class="bl_driverShow_inner_content_top_info">
              <div class="bl_driverShow_inner_content_top_info_1">
                <p class="bl_driverShow_inner_content_top_info_1_name">
                  {{ $driver->full_name }}
                </p>
                <div class="bl_driverShow_inner_content_top_info_1_score">
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
                  @endif
                </div>
                <a href="{{ route('driver.driver_task.index', [
                    'self' => 'self',
                ]) }}" class="bl_driverShow_inner_content_top_info_1_link">
                  担当依頼一覧
                </a>

              </div>

              @if (Auth::guard('drivers')->id() === $driver->id)
                <div class="bl_driverShow_inner_content_top_info_2">
                  <p><img src="{{ asset('images/common/tel.png') }}" alt=""><span>{{ $driver->tel ?? '' }}</span>
                  </p>
                  <p><img src="{{ asset('images/common/email.png') }}"
                      alt=""><span>{{ $driver->email ?? '' }}</span></p>
                  <p><img src="{{ asset('images/common/sns.png') }}"
                      alt=""><span>{{ $driver->lineid ?? '' }}</span></p>
                </div>
              @endif
            </div>

          </section>

          <div class="bl_driverShow_inner_content_profile">
            <div class="bl_driverShow_inner_content_profile_info">
              <dl>
                <dt>ドライバープラン</dt>
                <dd>{{ $driver->joinDriverPlan->name ?? 'データなし' }}</dd>
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

                  @if ($driver->joinRegisterOfficeMemo)
                    @foreach ($driver->joinRegisterOfficeMemo as $office)
                      <span>{{ $office->delivery_office_name ?? '' }}</span>
                    @endforeach
                  @endif
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

              @if ($driver->driver_entry_status_id)
                <dl>
                  <dt>登録申請ステータス</dt>
                  <dd>{{ $driver->joinDriverEntryStatusId->name ?? '' }}</dd>
                </dl>
              @endif
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
            </ul>
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
