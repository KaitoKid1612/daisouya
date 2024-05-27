@extends('layouts.email.app')


@section('content')
  <p>登録申請を受け付けました。</p>
  <p>返信まで今しばらくお待ちください。</p>
  <br>
  <h3>内容</h3>
  <p>ドライバー登録申請ID: {{ $data['guest']->id ?? '' }}</p>
  <p>名前: {{ $data['guest']->full_name ?? '' }}</p>
  <p>メールアドレス: {{ $data['guest']->email ?? '' }}</p>
  <p>性別: {{ $data['guest']->joinGender->name ?? '' }}</p>
  <p>誕生日: {{ $data['guest']->birthday ?? '' }}</p>
  <p>郵便番号: {{ $data['guest']->full_post_code ?? '' }}</p>
  <p>住所: {{ $data['guest']->full_addr ?? '' }}</p>
  <p>電話番号: {{ $data['guest']->tel ?? '' }}</p>
  <p>経歴: {{ $data['guest']->career ?? '' }}</p>
  <p>紹介文: {{ $data['guest']->introduction ?? '' }}</p>
  <p>メッセージ: {{ $data['guest']->message ?? '' }}</p>
  <br>
  
@endsection
