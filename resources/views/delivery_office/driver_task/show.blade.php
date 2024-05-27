@extends('layouts.delivery_office.app')

@section('title')
  稼働依頼詳細
@endsection

@section('content')
  @foreach ($errors->all() as $error)
    <script>
      console.log("バリデーション {{ $error }}");
    </script>
  @endforeach

  @if (session('msg'))
    <div class="bl_msg">
      <p class="el_red">
        {!! nl2br(session('msg') ?? '') !!}
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
        </div>

        @php
          $today_datetime = new \DateTime();
          $task_datetime = new \DateTime($task->task_date ?? '');
        @endphp

        <section class="bl_taskShow_inner_handle">
          {{-- ステータスが新規か受諾のみキャンセルと完了OK。 && 当日キャンセルは不可 --}}
          @if ($task->driver_task_permission['update']['cancel'])
            <form
              action="{{ route('delivery_office.driver_task.update', [
                  'task_id' => $task->id,
              ]) }}"
              method="POST" class="js_confirm">
              @csrf
              <input type="submit" value="ご依頼をキャンセル" class="c_btn">
              <input type="hidden" name="type" value='cancel'>
            </form>
          @endif
          @if ($task->driver_task_permission['update']['failure'])
            <form
              action="{{ route('delivery_office.driver_task.update', [
                  'task_id' => $task->id,
              ]) }}"
              method="POST" class="js_confirm">
              @csrf
              <input type="submit" value="ドライバーの不履行" class="c_btn">
              <input type="hidden" name="type" value='failure'>
            </form>
          @endif
          @if ($task->driver_task_permission['update']['complete'])
            <form
              action="{{ route('delivery_office.driver_task.update', [
                  'task_id' => $task->id,
              ]) }}"
              method="POST" class="js_confirm">
              @csrf
              <input type="submit" value="ご依頼を完了する" class="c_btn">
              <input type="hidden" name="type" value='complete'>
            </form>
          @endif
          @if ($task->driver_task_permission['create']['driver_task_review'])
            {{-- レビューリンク表示 稼働前はレビューリンク非表示 --}}
            <a
              href="{{ route('delivery_office.driver_task_review.create', ['driver_task_id' => $task->id]) }}"
              class='c_btn'>レビューをする</a>
          @endif
          @if ($task->driver_task_permission['update']['payment_method'])
            {{-- 支払い方法見設定の場合 --}}
            <a href="{{ route('delivery_office.driver_task.edit', ['task_id' => $task->id ?? '', 'type' => 'payment_method']) }}" class='c_btn'>支払い方法再設定</a>
          @endif
          @if ($task->driver_task_permission['create']['pdf_receipt'])
            {{-- <form>
              @csrf

              <input type="submit" value="HTMLプレビュー" formmethod="POST"
                formtarget="_blank"formaction="{{ route('delivery_office.pdf_receipt.store', [
                    'type' => 'html_preview',
                    'driver_task_id' => $task->id,
                ]) }}" class='c_btn'>
            </form> --}}
            <form>
              @csrf

              <input type="submit" value="領収書PDFプレビュー" formmethod="POST"
                formtarget="_blank"formaction="{{ route('delivery_office.pdf_receipt.store', [
                    'type' => 'pdf_preview',
                    'driver_task_id' => $task->id,
                ]) }}" class='c_btn'>
            </form>
            <form>
              @csrf
              <input type="submit" value="領収書PDFダウンロード" formmethod="POST"
                formtarget="_blank"formaction="{{ route('delivery_office.pdf_receipt.store', [
                    'type' => 'pdf_download',
                    'driver_task_id' => $task->id,
                ]) }}" class='c_btn'>
            </form>
          @endif
        </section>

        <div class="bl_taskShow_inner_content">
          <div class="bl_taskShow_inner_content_top">
            <span class="bl_taskShow_inner_content_top_status {{ 'el_task_status_' . $task->driver_task_status_id }}">
              {{ $task->joinTaskStatus->name ?? '' }}
            </span>
            <span class="bl_taskShow_inner_content_top_id">依頼ID: {{ $task->id ?? '' }}</span>
            <span class='el_center'>
              @php
                  $paymentStatus = $task->joinTaskPaymentStatus->name ?? '';
                  $isInStatusArray = in_array($task->driver_task_status_id, [1, 2, 5, 6, 7, 9, 10]);
              @endphp

              @if($task->joinOffice->charge_user_type_id === 1)
                  {{ !$isInStatusArray ? '支払い済み' : $paymentStatus }}
              @elseif($task->joinOffice->charge_user_type_id === 2)
                  {{ !$isInStatusArray ? '請求書払い' : $paymentStatus . '【請求書払い】' }}
              @endif
            </span>
            <span class="bl_taskShow_inner_content_top_text">{{ $task->joinTaskRefundStatus->name ?? '' }}</span>
            <span class="bl_taskShow_inner_content_top_date">{{ $task->created_at }}</span>
          </div>

          <div class="bl_taskShow_inner_content_request">
            <dl>
              <dt>稼働日</dt>
              <dd>{{ $task->taskDateYmd ?? '' }}</dd>
            </dl>

            <dl>
              <dt>稼働依頼プラン</dt>
              <dd>{{ $task->joinDriverTaskPlan->name ?? 'No Plan' }}</dd>
            </dl>

            <dl>
              <dt>担当ドライバー</dt>
              <dd>
                @if ($task->driver_id && $task->joinDriver)
                  <a href="{{ route('delivery_office.driver.show', ['driver_id' => $task->driver_id]) }}"
                    class="c_normal_link">
                    {{ $task->joinDriver->full_name ?? '' }}
                  </a>
                @elseif($task->driver_id && !$task->joinDriver)
                  データなし
                @else
                  指定なし
                @endif

                @if ($review)
                  <a
                    href="{{ route('delivery_office.driver_task_review.show', ['review_id' => $review->id]) }}"
                    class='el_review_link'>レビュー済み</a>
                @else
                  {{-- レビューリンク表示 稼働前はレビューリンク非表示 --}}
                  @if (in_array($task->driver_task_status_id, [4, 8]))
                    <a
                      href="{{ route('delivery_office.driver_task_review.create', ['driver_task_id' => $task->id]) }}"
                      class='el_review_link'>レビューをする</a>
                  @endif
                @endif

              </dd>
            </dl>

            <dl>
              <dt>料金</dt>
              <dd>
                総計: {{ number_format($task->TotalPrice ?? '') }}円 <br>
                システム利用料金: {{ number_format($task->system_price ?? '') }}円 <br>
                @if ($task->busy_system_price)
                  システム利用料金(繁忙期): {{ number_format($task->busy_system_price ?? '') }}円 <br>
                @endif
                ドライバー運賃: {{ $task->freight_cost !== null ? number_format($task->freight_cost) : '-' }}円<br>
                緊急依頼料金: {{ number_format($task->emergency_price ?? '') }}円 <br>
                値引き額: {{ number_format($task->discount ?? '') }}円 <br>
                税金{{ number_format($task->tax_rate ?? '') }}%: {{ number_format($task->tax ?? '') }}円
              </dd>
            </dl>

            @if ($task->refund_amount)
              <dl>
                <dt>返金額</dt>
                <dd>
                  {{ number_format($task->refund_amount ?? '') }}円
                </dd>
              </dl>
            @endif

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
              <dd>{{ $task->task_memo ?? '' }}</dd>
            </dl>

            <h3 class="bl_taskShow_inner_content_request_caption">集荷先情報</h3>

            <dl>
              <dt>配送会社名</dt>
              <dd>{{ $task->task_delivery_company_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>営業所名・デポ名</dt>
              <dd>{{ $task->task_delivery_office_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>メールアドレス</dt>
              <dd>{{ $task->task_email ?? '' }}</dd>
            </dl>

            <dl>
              <dt>電話番号</dt>
              <dd>{{ $task->task_tel ?? '' }}</dd>
            </dl>

            <dl>
              <dt>住所</dt>
              <dd>{{ $task->full_post_code ?? '' }} {{ $task->full_addr ?? '' }}</dd>
            </dl>
          </div>

          <div class="bl_taskShow_inner_content_officeInfo">
            <h3>ご依頼者様の情報</h3>
            <div>
              <dl>
                <dt>会社名</dt>
                <dd>{{ $task->joinOffice->joinCompany->name ?? ($task->joinOffice->delivery_company_name ?? '') }}</dd>
              </dl>

              <dl>
                <dt>営業所名・デポ名</dt>
                <dd>{{ $task->joinOffice->name ?? '' }}</dd>
              </dl>

              <dl>
                <dt>郵便番号</dt>
                <dd>{{ $task->joinOffice->post_code1 ?? '' }} - {{ $task->joinOffice->post_code2 ?? '' }}</dd>
              </dl>

              <dl>
                <dt>依頼者住所</dt>
                <dd>{{ $task->joinOffice->name ?? '' }}</dd>
              </dl>

              <dl>
                <dt>ご担当者</dt>
                <dd>{{ $task->joinOffice->full_name ?? '' }}</dd>
              </dl>

              <dl>
                <dt>メールアドレス</dt>
                <dd>{{ $task->joinOffice->email ?? '' }}</dd>
              </dl>

              <dl>
                <dt>電話番号</dt>
                <dd>{{ $task->joinOffice->manager_tel ?? '' }}</dd>
              </dl>
            </div>
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
                  <dt>投稿者</dt>
                  <dd>{{ $review->joinDriver->full_name ?? '' }}</dd>
                </dl>
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
  <script src="{{ asset('./js/libs/Functions/form_parts.js') }}"></script>
@endsection
