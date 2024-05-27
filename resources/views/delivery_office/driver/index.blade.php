@extends('layouts.delivery_office.app')

@section('title')
  ドライバー検索
@endsection

@section('content')
  <div class="bl_driverIndex">
    <div class="bl_driverIndex_inner">
      <form action="{{ route('delivery_office.driver.index') }}" method="GET">

        <div class="bl_driverIndex_inner_head">
          <div class="bl_driverIndex_inner_head_ttl">
            <h2>ドライバー検索<span>/ driver search</span></h2>
          </div>
          <div class="bl_driverIndex_inner_head_keyword">
            <input type="text" name='keyword' id="keyword" placeholder="名前 エリア"
              value="{{ old('keyword', $_GET['keyword'] ?? '') }}">
          </div>

          <div class="bl_driverIndex_inner_head_orderby">
            <select name="orderby" id="orderby">
              <option disabled selected>
                並び順
              </option>
              @foreach ($orderby_list as $orderby)
                <option value={{ $orderby['value'] ?? '' }}
                  {{ old('orderby', $_GET['orderby'] ?? '') == $orderby['value'] ? 'selected' : '' }}>
                  {{ $orderby['text'] }}
                </option>
              @endforeach
            </select>
          </div>


          <div class="bl_driverIndex_inner_head_submit">
            <input type="submit" value="検索">
          </div>

          <div class="bl_driverIndex_inner_head_filter">

            <div class="bl_driverIndex_inner_head_filter_selectbox">
              <select name="from_review_avg_score" id="from_review_avg_score">
                <option value="" disabled selected>★ 評価</option>
                @foreach ($from_review_avg_score_list as $score)
                  <option value="{{ $score['value'] }}"
                    @if (isset($_GET['from_review_avg_score']) && $_GET['from_review_avg_score'] == $score['value']) {{ 'selected' }} @endif>
                    {{ $score['text'] }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="bl_driverIndex_inner_head_filter_selectbox">
              <select name="addr1_id" id="addr1_id">
                <option disabled selected>
                  都道府県
                </option>
                <option value="">
                  指定なし
                </option>
                @foreach ($prefecture_list as $prefecture)
                  <option
                    value="{{ $prefecture->id }}" {{ ($_GET['addr1_id'] ?? -1) == $prefecture->id ? 'selected' : '' }}>
                    {{ $prefecture->name ?? '' }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

        </div>
      </form>

      <div class='bl_driverIndex_inner_content'>
        <ul>
          @foreach ($driver_list as $driver)
            <li>
              <a href="{{ route('delivery_office.driver.show', ['driver_id' => $driver->id]) }}">
                <div class="bl_driverIndex_inner_content_plan {{ $driver->driver_plan_id ? "el_plan_{$driver->joinDriverPlan->label}" : '' }}">
                  <p>{{ $driver->joinDriverPlan->name ?? 'No Plan' }}</p>
                </div>
                <div class="bl_driverIndex_inner_content_icon">
                  <img src="{{ route('storage_file.show', ['path' => $driver->icon_img, 'type' => 'user_icon']) }}" alt="">
                </div>
                <div class="bl_driverIndex_inner_content_text">
                  <p class='bl_driverIndex_inner_content_text_name'>
                    {{ $driver->name_sei }} {{ $driver->name_mei }}
                  </p>
                  <div class="bl_driverIndex_inner_content_text_star">

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

                  <div class="bl_driverIndex_inner_content_text_addr">{{ $driver->joinAddr1->name . $driver->addr2 }}
                  </div>
                  <div class='bl_driverIndex_inner_content_text_intro'>
                    {{ $driver->introduction }}
                  </div>
                </div>
              </a>
            </li>
          @endforeach
        </ul>
      </div>
      {{ $driver_list->links('parts.pagination') }}
    </div>
  </div>
@endsection
