@extends('layouts.delivery_office.app')

@section('title')
  ご依頼履歴
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

  <div class="bl_taskIndex">
    <div class="bl_taskIndex_inner">
      <form action="{{ route('delivery_office.driver_task.index') }}" method="GET">


        <div class="bl_taskIndex_inner_head">
          <div class="bl_taskIndex_inner_head_ttl">
            <h2>ご依頼履歴<span>/ request history</span></h2>
          </div>
          <div class="bl_taskIndex_inner_head_filter">
            <div class="bl_taskIndex_inner_head_filter_selectbox">
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
            <div class="bl_taskIndex_inner_head_filter_selectbox">
              <select name="task_status_id[]" id="">
                <option disabled selected>
                  ステータス
                </option>
                <option value="">
                  指定なし
                </option>
                @foreach ($task_status_list as $task_status)
                  <option value="{{ $task_status->id }}">
                    {{ $task_status->name ?? '' }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="bl_taskIndex_inner_head_filter_submit">
              <input type="submit" value="検索">
            </div>
          </div>

        </div>
      </form>

      <div class="bl_taskIndex_inner_content">
        <ul>
          @foreach ($task_list as $task)
            <li>
              <section>
                <p>
                  <span class="el_status{{ $task->driver_task_status_id }}">{{ $task->joinTaskStatus->name ?? '' }}</span>
                  <span>依頼ID:{{ $task->id }}</span>
                </p>

                <div>
                  <dl>
                    <dt>稼働日</dt>
                    <dd>{{ $task->taskDateYmd ?? '' }}</dd>
                  </dl>
                  <dl>
                    <dt>集荷先</dt>
                    <dd>{{ $task->task_delivery_company_name ?? '' }}{{ $task->task_delivery_office_name ?? '' }}</dd>
                  </dl>
                  <dl>
                    <dt>担当ドライバー</dt>
                    @if ($task->driver_id)
                      <dd>{{ $task->joinDriver->full_name ?? 'データなし' }}</dd>
                    @else
                      <dd>{{ $task->joinDriver->full_name ?? '指定なし' }}</dd>
                    @endif
                  </dl>
                  <dl>
                    <dt>稼働依頼プラン</dt>
                    <dd>{{ $task->joinDriverTaskPlan->name ?? 'No Plan' }}</dd>
                  </dl>
                </div>
              </section>
              <div class="bl_taskIndex_inner_content_link">
                <a href="{{ route('delivery_office.driver_task.show', ['task_id' => $task->id]) }}">詳細</a>
              </div>
            </li>
          @endforeach
        </ul>
      </div>
      {{ $task_list->links('parts.pagination') }}
    </div>
  </div>
@endsection
