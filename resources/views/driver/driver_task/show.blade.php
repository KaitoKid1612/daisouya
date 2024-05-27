@extends('layouts.driver.app')

@section('title')
  稼働依頼詳細
@endsection

@section('content')
  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {{ session('msg') ?? '' }}
      </p>
    </div>
  @endif

  @if ($task)
    <div class="bl_taskShow">
      <div class="bl_taskShow_inner">
        <div class="bl_taskShow_inner_head">
          <div class="bl_taskShow_inner_head_ttl">
            <h2>稼働依頼詳細<span>/ request details</span></h2>
          </div>

          @if ($task->driver_task_permission['create']['delivery_office_task_review'])
            <div class="bl_taskShow_inner_head_link">
              <a href="{{ route('driver.delivery_office_task_review.create', ['driver_task_id' => $task->id]) }}" class="c_btn">レビューをする</a>
            </div>
          @elseif($office_review)
            <div>レビュー済み</div>
          @endif

        </div>
        <div class="bl_taskShow_inner_content">
          <div class="bl_taskShow_inner_content_top">
            <span class="bl_taskShow_inner_content_top_status el_task_status_{{ $task->driver_task_status_id }}">
              {{ $task->joinTaskStatus->name ?? '' }}
            </span>
            <span class="bl_taskShow_inner_content_top_id">依頼ID: {{ $task->id }}</span>
            <span class="bl_taskShow_inner_content_top_date">{{ $task->created_at }}</span>
          </div>

          <div class="bl_taskShow_inner_content_request">
            <dl>
              <dt>稼働日</dt>
              <dd>{{ $task->taskDateYmd ?? '' }}</dd>
            </dl>
            <dl>
              <dt>稼働依頼プラン</dt>
                {{ $task->joinDriverTaskPlan->name ?? 'No Plan' }}
            </dl>
            <dl>
              <dt>ドライバー運賃</dt>
              <dd>{{ $task->freight_cost !== null ? number_format($task->freight_cost) : '-' }}円</dd>
            </dl>
            <dl>
              <dt>担当ドライバー</dt>
              <dd>
                @if ($task->driver_id && $task->joinDriver)
                  @if ($task->driver_id == Auth::guard('drivers')->id())
                    {{ $task->joinDriver->full_name ?? '' }}
                  @else
                    自分以外のドライバー
                  @endif
                @elseif ($task->driver_id && !$task->joinDriver)
                  データなしorソフト削除済み
                @else
                  未定
                @endif
              </dd>
            </dl>
            <dl>
              <dt>先週の平均物量(個)</dt>
              <dd>{{ $task->rough_quantity ?? '' }}</dd>
            </dl>
            <dl>
              <dt>配送コース</dt>
              <dd>{{ $task->delivery_route ?? '' }}</dd>
            </dl>
            <dl>
              <dt>依頼メモ 備考</dt>
              <dd>{{ $task->task_memo }}</dd>
            </dl>

            <h3 class='bl_taskShow_inner_content_request_caption'>集荷先情報</h3>
            <dl>
              <dt>配送会社名</dt>
              <dd>{{ $task->task_delivery_company_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>営業所名・デポ名</dt>
              <dd>{{ $task->task_delivery_office_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>住所</dt>
              <dd>{{ $task->full_post_code ?? '' }} {{ $task->full_addr ?? '' }}</dd>
            </dl>

            {{-- 担当ドライバーのみ閲覧可能 --}}
            @if ($task->driver_id == Auth::guard('drivers')->id())
              <dl>
                <dt>メールアドレス</dt>
                <dd>{{ $task->task_email ?? '' }}</dd>
              </dl>

              <dl>
                <dt>電話番号</dt>
                <dd>{{ $task->task_tel ?? '' }}</dd>
              </dl>
            @endif

            <h3 class='bl_taskShow_inner_content_request_caption'>依頼者情報</h3>
            <dl>
              <dt>依頼者</dt>
              <dd>
                {{ $task->joinOffice->joinCompany->name ?? ($task->joinOffice->delivery_company_name ?? '') }} {{ $task->joinOffice->name ?? '' }}
              </dd>
            </dl>

            <dl>
              <dt>ご担当者</dt>
              <dd>{{ $task->joinOffice->full_name ?? '' }}</dd>
            </dl>

            {{-- 担当ドライバーのみ閲覧可能 --}}
            @if ($task->driver_id == Auth::guard('drivers')->id())
              <dl>
                <dt>メールアドレス</dt>
                <dd>{{ $task->joinOffice->email ?? '' }}</dd>
              </dl>

              <dl>
                <dt>電話番号</dt>
                <dd>{{ $task->joinOffice->manager_tel ?? '' }}</dd>
              </dl>
            @endif

            {{-- 受託ボタン --}}
            @if ($task->driver_task_permission['update']['accept'])
              <div class="bl_taskShow_inner_content_request_form">
                <form method="POST"
                  action="{{ route('driver.driver_task.update', [
                      'task_id' => $task->id,
                  ]) }}"
                  class="js_confirm" data-confirm_msg='この依頼を引き受けます。本当によろしいですか？' data-idx="1">
                  @csrf
                  <input type="hidden" name="type" value="accept">
                  <input type="submit" value="この依頼を引き受ける" class="c_btn">
                </form>
              </div>

              <div class="el_form_popup" id="el_form_popup_1">
                <p class="el_form_popup_heading">受諾前の確認</p>

                <p class="el_form_popup_question">この依頼を引き受けます。本当によろしいですか?</p>
                <p class="el_form_popup_warning">受諾後のキャンセルは如何なる理由があっても禁止となります。</p>

                <div class="el_form_popup_checkbox">
                  <input 
                    type="checkbox"
                    name="el_form_popup_checkbox"
                    value="1"
                    id="el_form_popup_input_1"
                  >
                  <label for="el_form_popup_input_1">確認しました</label>
                </div>
                <div class="el_form_popup_footer">
                  <button class="el_form_popup_footer_cancel el_form_popup_footer_cancel_1">キャンセル</button>
                  <button class="el_form_popup_footer_ok el_form_popup_footer_ok_1">OK</button>
                </div>
              </div>
            @endif
            {{-- ステータスが「新規(指名)」or「決済準備完了」のみ拒否可能 --}}
            @if ($task->driver_task_permission['update']['reject'])
              <div class="bl_taskShow_inner_content_request_form">
                <form
                  action="{{ route('driver.driver_task.update', [
                      'task_id' => $task->id,
                  ]) }}"
                  method="POST" class="js_confirm" data-confirm_msg='この依頼を却下します。本当によろしいですか？'>
                  @csrf
                  <input type="submit" class="c_btn c_btn_bgRed" value="ご依頼を却下">
                  <input type="hidden" name="type" value="reject">
                </form>
              </div>
            @endif
          </div>
        </div>


        <section class="bl_taskShow_inner_review">
          <div class="bl_taskShow_inner_review_ttl">
            <h3>レビュー</h3>
          </div>
          <div class="bl_taskShow_inner_review_item">
            @if ($review)
              <div class="bl_taskShow_inner_review_item_head">
                <p class="bl_taskShow_inner_review_item_head_text">依頼者様の投稿</p>
                <p class="bl_taskShow_inner_review_item_head_score">
                  @if (isset($review->score) && $review->score)
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
              <div class="bl_taskShow_inner_review_item_body">
                <dl>
                  <dt>投稿者</dt>
                  <dd>{{ $review->joinOffice->joinCompany->name ?? ($review->joinOffice->delivery_company_name ?? '') }}{{ $review->joinOffice->name ?? '' }} {{ $review->joinOffice->full_name ?? '' }}</dd>
                </dl>
                <dl>
                  <dt>タイトル</dt>
                  <dd>{{ $review->title ?? '' }}</dd>
                </dl>

                <dl>
                  <dt>内容</dt>
                  <dd>{{ $review->text ?? '' }}</dd>
                </dl>
              </div>
            @else
              <p>依頼者様 未投稿</p>
            @endif
          </div>

          <div class="bl_taskShow_inner_review_item">
            @if ($office_review)
              <div class="bl_taskShow_inner_review_item_head">
                <p class="bl_taskShow_inner_review_item_head_text">ドライバー様の投稿</p>
                <p class="bl_taskShow_inner_review_item_head_score">
                  @if (isset($office_review->score) && $office_review->score)
                    @php
                      $score = round($office_review->score);
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
              <div class="bl_taskShow_inner_review_item_body">
                <dl>
                  <dt>タイトル</dt>
                  <dd>{{ $office_review->title ?? '' }}</dd>
                </dl>

                <dl>
                  <dt>内容</dt>
                  <dd>{{ $office_review->text ?? '' }}</dd>
                </dl>
              </div>
            @else
              <p>ドライバー様 未投稿</p>
            @endif
          </div>
        </section>

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
  <script src="{{ asset('./js/libs/Functions/form_submit.js') }}"></script>
@endsection
