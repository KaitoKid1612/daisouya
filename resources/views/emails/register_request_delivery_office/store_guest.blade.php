@extends('layouts.email.app')


@section('content')
  <p>登録申請を受け付けました。</p>
  <p>返信まで今しばらくお待ちください。</p>
  <br>
  <h3>内容</h3>
  <p>依頼者登録申請ID: {{ $data['guest']->id ?? '' }}</p>
  <p>配送会社: {{ $data['guest']->get_delivery_company_id->name ?? $data['guest']->delivery_company_name ?? '' }}</p>
  <p>営業所名: {{ $data['guest']->name ?? '' }}</p>
  <p>名前: {{ $data['guest']->full_name ?? '' }}</p>
  <p>メールアドレス: {{ $data['guest']->email ?? '' }}</p>
  <p>郵便番号: {{ $data['guest']->full_post_code ?? '' }}</p>
  <p>住所: {{ $data['guest']->full_addr ?? '' }}</p>
  <p>電話番号: {{ $data['guest']->manager_tel ?? '' }}</p>
  <p>メッセージ: {{ $data['guest']->message ?? '' }}</p>
  <br>
  
@endsection
