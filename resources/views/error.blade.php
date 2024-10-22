@extends('layouts.app')

@section('content')
    <div class="container text-center">
        <h1>Error</h1>
        <p>{{ $errorMessage }}</p>
        <p>You will be redirected to the login page in 3 seconds.</p>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = '{{ route('login') }}'; // 替换为你的登录路由
        }, 3000);
    </script>
@endsection
