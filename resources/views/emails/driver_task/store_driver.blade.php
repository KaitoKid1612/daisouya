@extends('layouts.email.app')


@section('content')
  <p>{{ $data['driver']->full_name ?? '' }} 様</p>
  <p>稼働のご依頼です。</p>
  <br>
  <h3>稼働内容</h3>
  <p>稼働ID: {{ $data['task']->id ?? '' }}</p>
  <p>稼働日時: {{ $data['task']->taskDateYmd ?? '' }}</p>
  <p>依頼者: {{ $data['office']->name ?? '' }}</p>
  <p>担当者: {{ $data['office']->name ?? '' }} {{ $data['office']->manager_name_sei ?? '' }}
    {{ $data['office']->manager_name_mei ?? '' }}</p>
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
  <p>ドライバー運賃:{{ $data['task']->freight_cost !== null ? number_format($data['task']->freight_cost) : '-' }}円</p>
  <br>

  <p><a href="{{ route('driver.driver_task.show', ['task_id' => $data['task']->id ?? '']) }}">稼働依頼確認</a></p>
  <br>
@endsection
