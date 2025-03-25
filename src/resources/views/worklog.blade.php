@extends('layouts.default')

@section('title','勤怠')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/worklog.css')  }}">
{{-- <link rel="stylesheet" href="{{ asset('/css/common.css')  }}"> --}}
@endsection

<!-- 本体 -->
@section('content')

@include('components.staff-header')

    <div class="worklog">
    <form class="form__wrap" action="{{ route('work') }}" method="post">
        @csrf

        <div class="form__item">
            @if($status == 0)
                <p class="status">勤務外</p>
            @elseif($status == 1)
                <p class="status">出勤中</p>
            @elseif($status == 2)
                <p class="status">休憩中</p>
            @elseif($status == 3 )
                <p class="status">退勤済</p>
            @endif


        <div class="date">{{$formatted_date}}</div>
        <div class="time">{{$now_time}}</div>

        <div class="form__item-button__box">
            @if($status == 0)
                <button class="form__item-button work" type="submit" name="start_work">出勤</button>
            @elseif($status == 1)
                <button class="form__item-button rest" type="submit" name="start_rest">休憩開始</button>
                <button class="form__item-button work" type="submit" name="end_work">退勤</button>
            @elseif($status == 2)
                <button class="form__item-button rest" type="submit" name="end_rest">休憩戻</button>
            @elseif($status == 3 )
                <p class="word">お疲れ様でした</p>
            @endif
        </div>
    </div>
@endsection