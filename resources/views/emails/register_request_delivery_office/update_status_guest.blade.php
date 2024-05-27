@extends('layouts.email.app')

@section('content')
  @if ($data['register_request']->register_request_status_id == 2)
    <p>登録申請を許可しました。以下のリンクからパスワード設定を行いログインしてください。</p>
    <p>有効期限内に行ってください。</p>
    <p><a href="{{ route('delivery_office.register_request.edit', [
      'token' => $data['register_request']->token ?? ''
    ]) }}">パスワード設定</a></p>
    <br>
    <h3>内容</h3>
    <p>名前: {{ $data['register_request']->full_name ?? '' }}</p>
    <p>メールアドレス: {{ $data['register_request']->email ?? '' }}</p>
  @elseif($data['register_request']->register_request_status_id == 3)
    <p>申請内容をもとに慎重に選考した結果、誠に残念ではございますが、今回はご期待に添えない結果となりました。大変申し訳ございません。</p>
  @endif

  
@endsection
