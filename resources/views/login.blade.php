<!doctype html>
<html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin sign in</title></head>
<body><main><h1>অ্যাডমিন লগইন</h1>
@if ($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
<form method="POST" action="{{ route('login.store') }}">@csrf
<label>Email <input type="email" name="email" required value="{{ old('email') }}" autocomplete="username"></label>
<label>Password <input type="password" name="password" required autocomplete="current-password"></label>
<button type="submit">লগইন</button></form><a href="{{ route('home') }}">হোম</a></main></body></html>
