@extends('layouts.admin.app')
@section('script_head')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endsection
@section('title')
  ドライバー 詳細
@endsection

@section('content')
  {{-- メッセージ --}}
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($driver)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>ドライバー 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_task.index', ['driver_id' => $driver->id]) }}" class="c_btn">稼働依頼一覧</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_task.create', ['driver_id' => $driver->id]) }}" class="c_btn">稼働依頼作成(指名)</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_schedule.index', ['driver_id' => $driver->id]) }}" class="c_btn">稼働可能日一覧</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_schedule.create', ['driver_id' => $driver->id]) }}" class="c_btn">稼働可能日登録</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_register_delivery_office.index', ['driver_id' => $driver->id]) }}" class="c_btn">営業所登録一覧</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_register_delivery_office.edit', ['driver_id' => $driver->id]) }}" class="c_btn">営業所登録</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_register_delivery_office_memo.create', ['driver_id' => $driver->id]) }}" class="c_btn">営業所登録メモ</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_task_review.index', ['driver_id' => $driver->id]) }}" class="c_btn">レビュー</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver.edit', ['driver_id' => $driver->id]) }}" class="c_btn">編集</a>
            </div>
            @if (!$driver->trashed())
              <div class="bl_show_inner_content_handle_item">
                <form action="{{ route('admin.driver.destroy', ['driver_id' => $driver->id]) }}" method="POST" class="js_confirm">
                  @csrf
                  <input type="hidden" name="type" value="soft">
                  <input type="submit" value="ソフト削除" class="c_btn el_bg_red">
                </form>
              </div>
            @endif
            @if ($driver->trashed())
              <div class="bl_show_inner_content_handle_item">
                <form action="{{ route('admin.driver.restore_delete', ['driver_id' => $driver->id]) }}" method="POST" class="js_confirm">
                  @csrf
                  <input type="submit" value="ソフト削除から復元" class="c_btn">
                </form>
              </div>
            @endif
            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.driver.destroy', ['driver_id' => $driver->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="hidden" name="type" value="force">
                <input type="submit" value="完全削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">

            @if ($driver->trashed())
              <dl>
                <dt>削除状態</dt>
                <dd>ソフトデリート</dd>
              </dl>
            @endif

            <dl>
              <dt>ID</dt>
              <dd>{{ $driver->id ?? '' }}</dd>
            </dl>

            <dl>
              <dt>ドライバープラン</dt>
              <dd>{{ $driver->joinDriverPlan->name ?? 'データなし' }}</dd>
            </dl>

            <dl>
              <dt>申請状況</dt>
              <dd>{{ $driver->joinDriverEntryStatusId->name ?? 'データなし' }}</dd>
            </dl>

            <dl>
              <dt>アイコン画像</dt>
              <dd>
                @if ($driver->icon_img)
                  <img src="{{ route('storage_file.show', ['path' => $driver->icon_img, 'type' => 'user_icon']) }}" alt="">
                @else
                  <p>画像なし</p>
                @endif
              </dd>
            </dl>

            <dl>
              <dt>名前</dt>
              <dd>{{ $driver->full_name ?? '' }}</a>
              </dd>
            </dl>

            <dl>
              <dt>名前(読み仮名)</dt>
              <dd>{{ $driver->full_name_kana ?? '' }}</dd>
            </dl>

            <dl>
              <dt>性別</dt>
              <dd>{{ $driver->joinGender->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>email</dt>
              <dd>{{ $driver->email ?? '' }}</dd>
            </dl>

            <dl>
              <dt>住所</dt>
              <dd>{{ $driver->full_post_code ?? '' }}</dd>
              <dd>{{ $driver->full_addr ?? '' }}</dd>
            </dl>

            <dl>
              <dt>電話番号</dt>
              <dd>{{ $driver->tel ?? '' }}</dd>
            </dl>

            @if ($driver->avatar)
              <dl>
                <dt>顔写真</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->avatar, now()->addMinutes(60)) }}" alt="avatar">
                </dd>
              </dl>

              <dl>
                <dt>支払い先の口座情報</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->bank, now()->addMinutes(60)) }}" alt="bank">
                </dd>
              </dl>

              <dl>
                <dt>運転免許証の表</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->driving_license_front, now()->addMinutes(60)) }}" alt="driving_license_front">
                </dd>
              </dl>

              <dl>
                <dt>運転免許証の裏</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->driving_license_back, now()->addMinutes(60)) }}" alt="driving_license_back">
                </dd>
              </dl>

              <dl>
                <dt>自賠責保険</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->auto_insurance, now()->addMinutes(60)) }}" alt="auto_insurance">
                </dd>
              </dl>

              <dl>
                <dt>任意保険</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->voluntary_insurance, now()->addMinutes(60)) }}" alt="voluntary_insurance">
                </dd>
              </dl>

              <dl>
                <dt>車検証</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->inspection_certificate, now()->addMinutes(60)) }}" alt="inspection_certificate">
                </dd>
              </dl>

              <dl>
                <dt>ナンバープレートを含めた自動車の画像(前方)</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->license_plate_front, now()->addMinutes(60)) }}" alt="license_plate_front">
                </dd>
              </dl>

              <dl>
                <dt>ナンバープレートを含めた自動車の画像(後方)</dt>
                <dd>
                  <img src="{{ Storage::disk('s3')->temporaryUrl($driver->license_plate_back, now()->addMinutes(60)) }}" alt="license_plate_back">
                </dd>
              </dl>
            @endif

            <dl>
              <dt>評価点</dt>
              <dd>
                {{ $driver->join_driver_review_avg_score ?? '' }}
              </dd>
            </dl>

            <dl>
              <dt>稼働数</dt>
              <dd>
                {{ $driver->join_task_count ?? '' }}
              </dd>
            </dl>

            <dl>
              <dt>経歴</dt>
              <dd>{{ $driver->career ?? '' }}</dd>
            </dl>

            <dl>
              <dt>紹介文</dt>
              <dd>{{ $driver->introduction ?? '' }}</dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $driver->created_at ?? '' }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $driver->updated_at ?? '' }}</dd>
            </dl>

            <dl>
              <dt>ソフト削除日</dt>
              <dd>{{ $driver->deleted_at ?? '' }}</dd>
            </dl>
          </section>

          <section class="bl_show_inner_content_relationData">
            <h3 class="bl_show_inner_content_relationData_ttl">登録営業所一覧</h3>
            <div class="bl_show_inner_content_relationData_tableBox">
              <table>
                <tbody>
                  <tr>
                    <th class='el_width4rem'>ID</th>
                    <th class='el_width12rem'>営業所</th>
                    <th class='el_width12rem'>担当者</th>
                    <th class='el_width14rem'>メールアドレス</th>
                    <th class='el_width11rem'>作成日</th>
                    <th class='el_width11rem'>更新日</th>
                  </tr>

                  @foreach ($driver->joinRegisterOffice as $office)
                    <tr>
                      <td class='ec_center'><a
                          href="{{ route('admin.delivery_office.show', ['office_id' => $office->id]) }}"
                          class="c_btn">{{ $office->id ?? '' }}</a></td>
                      <td>{{ $office->joinOffice->name ?? 'データなしorソフト削除済み' }}</td>
                      <td>{{ $office->joinOffice->full_name ?? 'データなしorソフト削除済み' }}</td>
                      <td>{{ $office->joinOffice->email ?? 'データなしorソフト削除済み' }}</td>
                      <td>{{ $office->created_at ?? '' }}</td>
                      <td>{{ $office->updated_at ?? '' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </section>

          <section class="bl_show_inner_content_relationData">
            <h3 class="bl_show_inner_content_relationData_ttl">登録営業所メモ一覧</h3>
            <div class="bl_show_inner_content_relationData_tableBox">
              <table>
                <tbody>
                  <tr>
                    <th class='el_width4rem'>ID</th>
                    <th class='el_width12rem'>配送会社</th>
                    <th class='el_width12rem'>営業所</th>
                    <th class='el_width11rem'>作成日</th>
                    <th class='el_width11rem'>更新日</th>
                  </tr>

                  @foreach ($driver->joinRegisterOfficeMemo as $office)
                    <tr>
                      <td>{{ $office->id }}</td>
                      {{-- <td class='ec_center'><a
                      href="{{ route('admin.delivery_office.show', ['office_id' => $office->id]) }}"
                      class="c_btn">{{ $office->id }}</a></td> --}}
                      <td>{{ $office->joinDeliveryCompany->name ?? '' }}</td>
                      <td>{{ $office->delivery_office_name ?? '' }}</td>
                      <td>{{ $office->created_at }}</td>
                      <td>{{ $office->updated_at }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
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

  <div id="imageModal" class="modal">
      <span class="close">&times;</span>
      <img class="modal-content" id="modalImage">

      <a href="" id="downloadButton" download class="download-button">ダウンロード</a>
  </div>

@endsection
@section('script_bottom')
  <script src="{{ asset('js/libs/Functions/form_parts.js') }}"></script>
  <script>
    $(document).ready(function(){
        $('img').click(function(){
            const imageUrl = $(this).attr('src');
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').show();

            $('#downloadButton').attr('download', 'ダウンロード.jpg');
        });

        $('.close').click(function(){
            $('#imageModal').hide();
        });

        $(window).click(function(e) {
            if ($(e.target).is('#imageModal')) {
                $('#imageModal').hide();
            }
        });
    });
  </script>
@endsection
