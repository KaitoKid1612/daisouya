@extends('layouts.admin.app')

@section('title')
  稼働依頼 詳細
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
    <div class="bl_show">
      <div class="bl_show_inner">
        <div class="bl_show_inner_head">
          <div class="bl_show_inner_head_ttl">
            <h2>稼働依頼 詳細</h2>
          </div>
        </div>

        <div class="bl_show_inner_content">
          <div class="bl_show_inner_content_handle">
            @if (isset($task->joinDriverReview) && !$task->joinDriverReview && !in_array($task->driver_task_status_id, [1, 2, 3, 6]))
              <div class="bl_show_inner_content_handle_item">
                <a href="{{ route('admin.driver_task_review.create', ['driver_task_id' => $task->id]) }}" class="c_btn">レビューをする</a>
              </div>
            @endif

            @if (in_array($task->driver_task_status_id, [8]) && in_array($task->driver_task_refund_status_id, [1, 2]))
              <div class="bl_show_inner_content_handle_item">
                <form action="{{ route('admin.driver_task.payment.refund', ['task_id' => $task->id]) }}" method="POST" class="js_confirm">
                  @csrf
                  <input type="submit" value="返金する" class="c_btn el_bg_red">
                </form>
              </div>
            @endif

            <div class="bl_show_inner_content_handle_item">
              <a href="{{ route('admin.driver_task.edit', ['task_id' => $task->id]) }}" class="c_btn">編集</a>
            </div>
            <div class="bl_show_inner_content_handle_item">
              <form action="{{ route('admin.driver_task.destroy', ['task_id' => $task->id]) }}" method="POST" class="js_confirm">
                @csrf
                <input type="submit" value="削除" class="c_btn el_bg_red">
              </form>
            </div>
          </div>
          <section class="bl_show_inner_content_data">
            <dl>
              <dt>ID</dt>
              <dd>{{ $task->id }}</dd>
            </dl>

            <dl>
              <dt>稼働ステータス</dt>
              <dd>{{ $task->joinTaskStatus->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>稼働日</dt>
              <dd>{{ $task->taskDateYmd ?? '' }}</dd>
            </dl>

            <dl>
              <dt>申込日</dt>
              <dd>{{ $task->request_date ?? '' }}</dd>
            </dl>

            <dl>
              <dt>稼働依頼プラン</dt>
              <dd>{{ $task->joinDriverTaskPlan->name ?? 'No Plan' }}</dd>
            </dl>

            <dl>
              <dt>ドライバー名</dt>
              <dd>
                @if ($task->driver_id && $task->joinDriver)
                  <a href="{{ route('admin.driver.show', [
                      'driver_id' => $task->driver_id,
                  ]) }}">{{ $task->joinDriver->full_name ?? '' }}
                  </a>
                @elseif ($task->driver_id && !$task->joinDriver)
                  <a href="{{ route('admin.driver.show', [
                      'driver_id' => $task->driver_id,
                  ]) }}">{{ $task->joinDriver->full_name ?? 'データなしorソフト削除済み' }}
                  </a>
                @else
                  指定なし
                @endif
              </dd>
            </dl>

            <dl>
              <dt>配送営業所(依頼者)</dt>
              <dd>
                @if ($task->delivery_office_id && $task->joinOffice)
                  <a href="{{ route('admin.delivery_office.show', [
                      'office_id' => $task->delivery_office_id,
                  ]) }}">
                    {{ $task->joinOffice->name ?? '' }}
                  </a>
                @elseif ($task->delivery_office_id && !$task->joinOffice)
                  <a href="{{ route('admin.delivery_office.show', [
                      'office_id' => $task->delivery_office_id,
                  ]) }}">
                    {{ $task->joinOffice->name ?? 'データなしorソフト削除済み' }}
                  </a>
                @else
                  なし
                @endif

              </dd>
            </dl>

            <dl>
              <dt>集荷先会社</dt>
              <dd>{{ $task->task_delivery_company_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>集荷先営業所名</dt>
              <dd>{{ $task->task_delivery_office_name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>集荷先住所</dt>
              <dd>{{ $task->full_post_code ?? '' }}</dd>
              <dd>{{ $task->FullAddr ?? '' }}</dd>
            </dl>

            <dl>
              <dt>集荷先メールアドレス</dt>
              <dd>{{ $task->task_email ?? '' }}</dd>
            </dl>

            <dl>
              <dt>集荷先電話番号</dt>
              <dd>{{ $task->task_tel ?? '' }}</dd>
            </dl>

            <dl>
              <dt>先週の平均物量(個)</dt>
              <dd>{{ $task->rough_quantity }}</dd>
            </dl>

            <dl>
              <dt>配送コース</dt>
              <dd>{{ $task->delivery_route }}</dd>
            </dl>

            <dl>
              <dt>メモ</dt>
              <dd>{{ $task->task_memo }}</dd>
            </dl>

            <dl>
              <dt>レビュースコア</dt>
              @if ($task->joinDriverReview)
                <dd>
                  <a
                    href="{{ route('admin.driver_task_review.show', [
                        'review_id' => $task->joinDriverReview->id,
                    ]) }}">
                    レビュー詳細
                  </a>
                </dd>
                <dd>{{ $task->joinDriverReview->score }}</dd>
                <dd>{{ $task->joinDriverReview->title }}</dd>
              @endif
            </dl>

            <dl>
              <dt>総計</dt>
              <dd>{{ number_format($task->TotalPrice ?? '') }}円</dd>
            </dl>

            <dl>
              <dt>システム利用料金</dt>
              <dd>{{ number_format($task->system_price ?? 0) }}円</dd>
            </dl>

            <dl>
              <dt>システム利用料金(繁忙期)</dt>
              <dd>{{ number_format($task->busy_system_price ?? 0) }}円</dd>
            </dl>

            <dl>
              <dt>ドライバー運賃</dt>
              <dd>{{ $task->freight_cost !== null ? number_format($task->freight_cost) : '-'}}円</dd>
            </dl>

            <dl>
              <dt>緊急依頼料金</dt>
              <dd>{{ number_format($task->emergency_price ?? 0) }}円</dd>
            </dl>

            <dl>
              <dt>値引き額</dt>
              <dd>{{ number_format($task->discount ?? 0) }}円</dd>
            </dl>

            <dl>
              <dt>返金額</dt>
              <dd>{{ number_format($task->refund_amount ?? 0) }}円</dd>
            </dl>

            <dl>
              <dt>税金</dt>
              <dd>{{ number_format($task->tax ?? 0) }}円</dd>
            </dl>

            <dl>
              <dt>税率</dt>
              <dd>{{ $task->tax_rate ?? 0 }}%</dd>
            </dl>

            <dl>
              <dt>決済手数料率</dt>
              <dd>{{ $task->payment_fee_rate ?? 0 }}%</dd>
            </dl>

            <dl>
              <dt>stripe_payment_method_id</dt>
              <dd>{{ $task->stripe_payment_method_id ?? '' }}</dd>
            </dl>

            <dl>
              <dt>stripe_payment_intent_id</dt>
              <dd>{{ $task->stripe_payment_intent_id ?? '' }}</dd>
            </dl>
            <dl>
              <dt>stripe_payment_refund_id</dt>
              <dd>{{ $task->stripe_payment_refund_id ?? '' }}</dd>
            </dl>

            <dl>
              <dt>支払いステータス</dt>
              <dd>{{ $task->joinTaskPaymentStatus->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>返金ステータス</dt>
              <dd>{{ $task->joinTaskRefundStatus->name ?? '' }}</dd>
            </dl>

            <dl>
              <dt>作成日</dt>
              <dd>{{ $task->created_at }}</dd>
            </dl>

            <dl>
              <dt>更新日</dt>
              <dd>{{ $task->updated_at }}</dd>
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
