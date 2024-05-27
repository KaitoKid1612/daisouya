@extends('layouts.email.app')

@section('content')
  <p>{{ $data['office']->name ?? '' }} {{ $data['office']->manager_name_sei ?? '' }}
    {{ $data['office']->manager_name_mei ?? '' }} 様</p>

  @if ($data['task']->driver_task_payment_refund_status_id == 3)
    <p>返金処理が行われました。</p>
  @else
    <p>返金処理に失敗しました。管理者にお問合せください。</p>
  @endif

  <p><a href="{{ route('delivery_office.driver_task.show', ['task_id' => $data['task']->id ?? '']) }}">稼働依頼確認</a></p>
  <br>
@endsection
