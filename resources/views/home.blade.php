@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}

                    <!-- 添加链接到 dev 页面 -->
                    <div class="mt-4">
                        <a href="{{ url('/dev') }}" class="btn btn-primary">Go to SQL Executor</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
