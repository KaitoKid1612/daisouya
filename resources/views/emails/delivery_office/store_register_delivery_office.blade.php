@extends('layouts.email.app')

@section('content')
  <p>{{ $data['office']->full_name ?? '' }} 様</p>
  <p>営業所アカウントを作成しました。</p>
  <br>
  <h3>内容</h3>
  <p>ドライバーID: {{ $data['office']->id ?? '' }}</p>
  <p>名前: {{ $data['office']->full_name ?? '' }}</p>
  <p>メールアドレス: {{ $data['office']->email ?? '' }}</p>
  <p>初期パスワード:{{ $data['office']->init_password ?? '' }}</p>
  <p><a href="{{ route('delivery_office.login') }}">ログイン</a></p>
  <p>※ログイン後パスワードはご自身で変更してください。</p>
  <p>事前に<a href="{{ route('guest.web_terms_service.index',['type' => 'office']) }}">ご利用規約</a>を確認してください。</p>
  <br>
  
@endsection