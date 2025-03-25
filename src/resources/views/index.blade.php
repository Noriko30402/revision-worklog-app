@extends('layouts.default')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/worklog.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.staff-header')


@endsection