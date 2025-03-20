@extends('layouts.default')

@section('title','ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<h1>出勤前
</h1>
@endsection