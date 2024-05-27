<h1>ドライバー作成</h1>
<form action="{{ route('driver.register') }}" method="POST">
    @csrf
  
    @foreach ($errors->all() as $error)
      <p>{{ $error }}</p>
    @endforeach
    <label for="">名前:<input type="text" name="name" value="{{ old('name') }}"></label>
    <label for="">メールアドレス: <input type="email" name="email" value="{{ old('email') }}"></label>
    <label for="">パスワード<input type="password" name="password" value="{{ old('password') }}"></label>
    <label for="">パスワード<input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}"></label>
    <input type="submit" value="決定">
  </form>