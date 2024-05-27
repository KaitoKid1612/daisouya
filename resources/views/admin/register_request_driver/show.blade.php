@extends('layouts.admin.app')

@section('title')
  ドライバー登録申請 詳細
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($register_request)
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>ドライバー登録申請 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            {{-- 新規,審査中,審査中(登録処理済み)の場合 --}}
            @if (in_array($register_request->register_request_status_id, [1, 6, 7]))
              <div class="bl_show_inner_content_handle_item">
                <a href="{{ route('admin.register_request_driver.edit', ['register_request_id' => $register_request->id]) }}" class="c_btn">登録申請の処理ページ</a>
              </div>
            @endif

            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.register_request_driver.destroy', ['register_request_id' => $register_request->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="hidden" name="type" value="force">
                <input type="submit" value="削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">
            <dl>
              <dt>ID</dt>
              <dd>{{ $register_request->id }}</dd>
            </dl>

            <dl>
              <dt>登録申請</dt>
              <dd>{{ $register_request->get_register_request_status->name ?? '' }}
                : {{ $register_request->register_request_status_id }}
              </dd>
            </dl>

            <dl>
              <dt>ユーザータイプ</dt>
              <dd>{{ $register_request->joinUserType->name ?? '非会員' }}</a>
            </dl>

            <dl>
              <dt>ドライバープラン</dt>
              <dd>{{ $register_request->joinDriverPlan->name ?? 'No Plan' }}: {{ $register_request->driver_plan_id ?? '' }}</a>
            </dl>

            <dl>
              <dt>名前</dt>
              <dd>{{ $register_request->full_name ?? '' }}</a>
              </dd>
            </dl>

            <dl>
              <dt>名前(読み仮名)</dt>
              <dd>{{ $register_request->full_name_kana ?? '' }}</dd>
            </dl>

            <dl>
              <dt>email</dt>
              <dd>{{ $register_request->email ?? '' }}</dd>
            </dl>

            <dl>
              <dt>住所</dt>
              <dd>{{ $register_request->full_post_code }}</dd>
              <dd>{{ $register_request->full_addr }}</dd>
            </dl>

            <dl>
              <dt>電話番号</dt>
              <dd>{{ $register_request->tel ?? '' }}</dd>
            </dl>

            <dl>
              <dt>顔写真</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->avatar, now()->addMinutes(60)) }}" alt="avatar">
              </dd>
            </dl>

            <dl>
              <dt>支払い先の口座情報</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->bank, now()->addMinutes(60)) }}" alt="bank">
              </dd>
            </dl>

            <dl>
              <dt>運転免許証の表</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->driving_license_front, now()->addMinutes(60)) }}" alt="driving_license_front">
              </dd>
            </dl>

            <dl>
              <dt>運転免許証の裏</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->driving_license_back, now()->addMinutes(60)) }}" alt="driving_license_back">
              </dd>
            </dl>

            <dl>
              <dt>自賠責保険</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->auto_insurance, now()->addMinutes(60)) }}" alt="auto_insurance">
              </dd>
            </dl>

            <dl>
              <dt>任意保険</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->voluntary_insurance, now()->addMinutes(60)) }}" alt="voluntary_insurance">
              </dd>
            </dl>

            <dl>
              <dt>車検証</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->inspection_certificate, now()->addMinutes(60)) }}" alt="inspection_certificate">
              </dd>
            </dl>

            <dl>
              <dt>ナンバープレートを含めた自動車の画像(前方)</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->license_plate_front, now()->addMinutes(60)) }}" alt="license_plate_front">
              </dd>
            </dl>

            <dl>
              <dt>ナンバープレートを含めた自動車の画像(後方)</dt>
              <dd>
                <img src="{{ Storage::disk('s3')->temporaryUrl($register_request->license_plate_back, now()->addMinutes(60)) }}" alt="license_plate_back">
              </dd>
            </dl>

            <dl>
              <dt>経歴</dt>
              <dd>{{ $register_request->career ?? '' }}</dd>
            </dl>

            <dl>
              <dt>紹介文</dt>
              <dd>{{ $register_request->introduction ?? '' }}</dd>
            </dl>

            <dl>
              <dt>その他メッセージ</dt>
              <dd>{{ $register_request->message ?? '' }}</dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $register_request->created_at }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $register_request->updated_at }}</dd>
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
