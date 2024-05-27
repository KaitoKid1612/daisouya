@extends('layouts.email.app')

@section('content')
  <p>{{ $data['driver']->full_name ?? '' }} 様</p>
  <p>ドライバーアカウントを作成しました。</p>
  <br>
  <h3>内容</h3>
  <p>ドライバーID: {{ $data['driver']->id ?? '' }}</p>
  <p>名前: {{ $data['driver']->full_name ?? '' }}</p>
  <p>メールアドレス: {{ $data['driver']->email ?? '' }}</p>
  <p>初期パスワード:{{ $data['driver']->init_password ?? '' }}</p>
  <p><a href="{{ route('driver.login') }}">ログイン</a></p>
  <p>※ログイン後パスワードはご自身で変更してください。</p>
  <p>事前に<a href="{{ route('guest.web_terms_service.index',['type' => 'driver']) }}">ご利用規約</a>を確認してください。</p>
  <br>
  
@endsection