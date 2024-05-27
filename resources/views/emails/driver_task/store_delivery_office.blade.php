@extends('layouts.email.app')


@section('content')
  <p>{{ $data['office']->name ?? '' }} {{ $data['office']->manager_name_sei ?? '' }}
    {{ $data['office']->manager_name_mei ?? '' }} 様</p>
  <p>稼働のご依頼を申し込みました。</p>
  <br>
  <h3>稼働内容</h3>
  <p>稼働ID: {{ $data['task']->id ?? '' }}</p>
  <p>稼働日時: {{ $data['task']->taskDateYmd ?? '' }}</p>
  <p>依頼申請ドライバー: {{ $data['task']->joinDriver->full_name ?? '指定なし' }}</p>
  <p>依頼者(営業所): {{ $data['office']->name ?? '' }}</p>
  <p>稼働依頼プラン: {{ $data['task']->joinDriverTaskPlan->name ?? '' }}</p>
  <p>依頼者住所: {{ $data['office']->full_post_code ?? '' }} {{ $data['office']->full_addr ?? '' }}</p>
  <p>ドライバーメールアドレス: {{ $data['driver']->email ?? '' }}</p>
  <p>ドライバー電話番号: {{ $data['driver']->tel ?? '' }}</p>
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

  <p><a href="{{ route('delivery_office.driver_task.show', ['task_id' => $data['task']->id ?? '']) }}">稼働依頼確認</a></p>
  <br>
@endsection