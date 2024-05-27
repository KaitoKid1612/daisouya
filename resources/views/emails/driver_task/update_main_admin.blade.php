@extends('layouts.email.app')


@section('content')
  <p>管理者様</p>
  <p>稼働内容に更新がありました。</p>

  @if (in_array($data['task']->driver_task_status_id, [1, 2, 10, 11]))
    <p>※受諾完了していません。</p>
  @endif

  @if (in_array($data['task']->driver_task_status_id, [8]))
    <p>依頼者からドライバーの不履行の報告を受けました。返金対応の判断をしてください。</p>
    <p><a href="{{ route('admin.driver_task.show', ['task_id' => $data['task']->id ?? '']) }}">稼働依頼確認</a></p>
  @endif

  <br>
  <h3>稼働内容</h3>
  <p>稼働ステータス:{{ $data['task']->joinTaskStatus->name ?? '' }}</p>
  <p>稼働ID: {{ $data['task']->id ?? '' }}</p>
  <p>稼働日時: {{ $data['task']->taskDateYmd ?? '' }}</p>
  <p>依頼申請ドライバー: {{ $data['task']->joinDriver->full_name ?? '指定なし' }}</p>
  <p>依頼者(営業所): {{ $data['office']->name ?? '' }}</p>
  <p>稼働依頼プラン: {{ $data['task']->joinDriverTaskPlan->name ?? '' }}</p>
  <p>依頼者住所: {{ $data['office']->full_post_code ?? '' }}
    {{ $data['office']->full_addr ?? '' }}</p>
  <p>依頼者メールアドレス: {{ $data['office']->email ?? '' }}</p>
  <p>依頼者電話番号: {{ $data['office']->manager_tel ?? '' }}</p>
  <p>先週の平均物量(個): {{ $data['task']->rough_quantity ?? '' }}</p>
  <p>配送コース: {{ $data['task']->delivery_route ?? '' }}</p>
  <p>依頼メモ: {{ $data['task']->task_memo ?? '' }}</p>
  <br>
  <p>集荷先配送会社:{{ $data['task']->task_delivery_company_name ?? '' }}</p>
  <p>集荷先営業所:{{ $data['task']->task_delivery_office_name ?? '' }}</p>
  <p>
    集荷先住所: {{ $data['task']->full_post_code ?? '' }}<br>
    {{ $data['task']->full_addr ?? '' }}
  </p>
  <p>集荷先メールアドレス:{{ $data['task']->task_email ?? '' }}</p>
  <p>集荷先電話番号:{{ $data['task']->task_tel ?? '' }}</p>

  <br>
  <h3>料金</h3>
  <p>合計:{{ number_format($data['task']->TotalPrice ?? '') }}円</p>
  <p>システム利用料金:{{ number_format($data['task']->system_price ?? '') }}円</p>
  <p>システム利用料金(繁忙期):{{ number_format($data['task']->busy_system_price ?? '') }}円</p>
  <p>ドライバー運賃:{{ $data['task']->freight_cost !== null ? number_format($data['task']->freight_cost) : '-' }}円</p>
  <p>緊急依頼料金:{{ number_format($data['task']->emergency_price ?? '') }}円</p>
  <p>消費税 {{ $data['task']->tax_rate ?? '?' }}%:{{ number_format($data['task']->tax ?? '') }}円</p>
  @if ($data['task']->discount > 0)
    <p>値引き額:{{ number_format($data['task']->discount ?? '') }}円</p>
  @endif

  <br>

  <p><a href="{{ route('admin.driver_task.show', ['task_id' => $data['task']->id ?? '']) }}">稼働依頼確認</a></p>

  <br>
@endsection
