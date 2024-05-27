@extends('layouts.email.app')


@section('content')
  <p>{{ $data['contact']->joinUserType->name ?? 'ゲスト' }}様</p>
  <p>お問い合わせを受け付けました。</p>
  <br>
  <h3>お問い合わせ内容</h3>
  <p>ユーザータイプ: {{ $data['contact']->joinUserType->name ?? '' }}</p>
  <p>名前: {{ $data['contact']->full_name ?? '' }}</p>
  <p>名前(カナ): {{ $data['contact']->full_name_kana ?? '' }}</p>
  <p>メールアドレス: {{ $data['contact']->email ?? '' }}</p>
  <p>電話番号: {{ $data['contact']->tel ?? '' }}</p>
  <p>お問い合わせタイプ: {{ $data['contact']->get_web_contact_type->name ?? '' }}</p>
  <p>題: {{ $data['contact']->title ?? '' }}</p>
  <p>内容: {{ $data['contact']->text ?? '' }}</p>
  <br>
@endsection
