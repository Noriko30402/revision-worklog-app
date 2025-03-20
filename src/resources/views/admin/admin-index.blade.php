@extends('layouts.default')

@section('title','インデックス')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.admin-header')

<h1>管理者画面</h1>
@endsection