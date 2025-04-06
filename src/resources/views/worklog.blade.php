@extends('layouts.default')

@section('title','勤怠')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/worklog.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.staff-header')

<div class="worklog">

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
    </div>

        <div class="date">{{$formatted_date}}</div>
        <div class="time" > <span id="current-time">{{$now_time}}</span></div>

    <form class="form__wrap" action="{{ route('work') }}" method="post">
    @csrf
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
    </form>

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
</div>


<script>
    // 現在時刻をページ読み込み時に設定
    let currentTimeElement = document.getElementById('current-time');
    let currentTime = currentTimeElement.innerText;

    // 現在時刻を分割して、時間と分を取得
    let timeParts = currentTime.split(':');
    let hours = parseInt(timeParts[0], 10);
    let minutes = parseInt(timeParts[1], 10);

    // 時間を1分ずつ進める関数
    function updateTime() {
        minutes++;
        if (minutes === 60) {
            minutes = 0;
            hours++;
            if (hours === 24) {
                hours = 0; // 24時間制にリセット
            }
        }

        // 新しい時間を設定
        currentTime = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
        currentTimeElement.innerText = currentTime;
    }

    // 1分ごとに時間を進める
    setInterval(updateTime, 60000);
</script>
@endsection