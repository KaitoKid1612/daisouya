@extends('layouts.email.app')

@section('content')
  {{-- すでに登録しているドライバーがいて、更新だけを行なった場合は、パスワード再設定リンクを送らない。 --}}
  @if ($data['register_request']->register_request_status_id == 2)
    <p>登録申請を許可しました。以下のリンクからパスワード設定を行いログインしてください。</p>
    <p>以下のリンクからパスワード設定を行い、有効期限内にログインしてください。</p>
    <p><a href="{{ route('driver.register_request.edit', [
        'token' => $data['register_request']->token ?? '',
    ]) }}">パスワード設定</a></p>
    <br>
    <h3>内容</h3>
    <p>名前: {{ $data['register_request']->full_name ?? '' }}</p>
    <p>メールアドレス: {{ $data['register_request']->email ?? '' }}</p>
  @elseif($data['register_request']->register_request_status_id == 3)
    <p>申請内容をもとに慎重に選考した結果、誠に残念ではございますが、今回はご期待に添えない結果となりました。大変申し訳ございません。</p>
  @elseif($data['register_request']->register_request_status_id == 6)
    <p>審査中のため、稼働依頼詳細を閲覧できません。<br>審査と登録が完了次第、閲覧が可能となります。</p>
    <p>以下のリンクからパスワード設定を行い、有効期限内にログインしてください。</p>
    <p><a href="{{ route('driver.register_request.edit', [
        'token' => $data['register_request']->token ?? '',
    ]) }}">パスワード設定</a></p>
    <br>
    <h3>内容</h3>
    <p>名前: {{ $data['register_request']->full_name ?? '' }}</p>
    <p>メールアドレス: {{ $data['register_request']->email ?? '' }}</p>
  @endif
@endsection
