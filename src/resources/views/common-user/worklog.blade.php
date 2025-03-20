@extends('layouts.default')

<!-- タイトル -->
@section('title','勤怠登録＿出勤前')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<h1>WorkLog</h1>
@endsection