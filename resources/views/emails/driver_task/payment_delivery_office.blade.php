@extends('layouts.email.app')

@section('content')
  <p>{{ $data['office']->name ?? '' }} {{ $data['office']->manager_name_sei ?? '' }}
    {{ $data['office']->manager_name_mei ?? '' }} 様</p>

  @if ($data['task']->driver_task_status_id == 10 && $data['task']->driver_task_payment_status_id == 1)
    <p>決済に失敗しましたので、受諾が完了しませんでした。以下のリンクから支払い方法を再設定してください。有効期限以内に行われなかった場合、この稼働依頼はキャンセルになります。</p>
    <p><a href="{{ route('delivery_office.driver_task.edit', ['task_id' => $data['task']->id ?? '', 'type' => 'payment_method']) }}">支払い方法再設定</a></p>
  @else
    <p>稼働が受諾されましたので、決済を行いました。</p>
  @endif

  <p><a href="{{ route('delivery_office.driver_task.show', ['task_id' => $data['task']->id ?? '']) }}">稼働依頼確認</a></p>
  <br>
@endsection
