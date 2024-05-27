@extends('layouts.email.app')


@section('content')
  <p>{{ $data['driver']->name_sei ?? '' }} {{ $data['driver']->name_mei ?? '' }} 様</p>
  <p>稼働内容に更新がありました。</p>
  @if (in_array($data['task']->driver_task_status_id, [1, 2, 10, 11]))
    <p>※受諾完了していません。</p>
  @endif


  @if ($data['task']->driver_task_status_id == 11 && $data['task']->driver_task_payment_status_id == 1)
    <p>依頼者の決済準備が整いました。受諾の可否を、下記の稼働依頼確認のリンクからアクセスして行なってください。</p>
    <p><a href="{{ route('driver.driver_task.show', ['task_id' => $data['task']->id ?? '']) }}">稼働依頼確認</a></p>
  @endif


  <br>
  <h3>稼働内容</h3>
  <p>稼働ステータス:{{ $data['task']->joinTaskStatus->name ?? '' }}</p>
  <p>稼働ID: {{ $data['task']->id ?? '' }}</p>
  <p>稼働日時: {{ $data['task']->taskDateYmd ?? '' }}</p>
  <p>依頼申請ドライバー: {{ $data['task']->joinDriver->full_name ?? '指定なし' }}</p>
  <p>依頼者: {{ $data['office']->name ?? '' }}</p>
  <p>稼働依頼プラン: {{ $data['task']->joinDriverTaskPlan->name ?? '' }}</p>
  <p>依頼者住所: {{ $data['office']->full_post_code ?? '' }}
    {{ $data['office']->full_addr ?? '' }}</p>
  <p>依頼者メールアドレス: {{ $data['office']->email ?? '' }}</p>
  <p>依頼者電話番号: {{ $data['office']->manager_tel ?? '' }}</p>
  <p>先週の平均物量(個): {{ $data['task']->rough_quantity ?? '' }}</p>
  <p>配送コース: {{ $data['task']->delivery_route ?? '' }}</p>
  <p>依頼メモ: {{ $data['task']->task_memo ?? '' }}</p>

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
  <p>ドライバー運賃:{{ $data['task']->freight_cost !== null ? number_format($data['task']->freight_cost) : '-' }}円</p>
  <br>

  <p><a href="{{ route('driver.driver_task.show', ['task_id' => $data['task']->id ?? '']) }}">稼働依頼確認</a></p>
  <br>
@endsection
