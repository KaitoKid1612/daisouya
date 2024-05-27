<div style="width:100%; text-align: center;margin-bottom: 64px;">
  <img src="{{ asset('images/common/logo.png') }}" alt="代走屋" style="width:20%;">
</div>

<p style="text-align: center;"">有効期間内に以下のリンクからパスワードの再設定手続きにお進みください。</p>

<div style="text-align: center;">
  <a href="{{ route('driver.password.reset', ['token' => $token]) }}"
    style='background: #3c8dbc;
    color: #fff;
    font-size: 1.2rem;
    padding: 0.6rem 1.2rem;
    border-radius: 0.2rem;'>パスワード再設定</a>
  <br>
  
</div>
