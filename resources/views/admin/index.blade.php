@extends('layout.admin.app')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(request('success'))
        <div class="alert alert-success">
            {{ request('success') }}
        </div>
    @endif

    <div style="display:flex;justify-content:center;align-items:center;height:calc(100vh - 120px);">
        <img src="{{ asset('admin_assets/assets/images/logo.png') }}"
             alt="Coming Soon"
             style="max-width:400px;width:100%;">
    </div>

@endsection