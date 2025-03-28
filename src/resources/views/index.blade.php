@extends('layouts.default')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/worklog.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.staff-header')

<div class="container">
<h1>{{ $currentMonth}}</h1>

@if($works->isEmpty())
  <p>この月には勤務記録がありません。</p>
@else

  <table class="table">
    <thead>
      <tr>
        <th>日付</th>
        <th>出勤</th>
        <th>退勤</th>
        <th>休憩</th>
        <th>合計</th>
        <th>詳細</th>
      </tr>
    </thead>

    <tbody>
      @foreach ($works as $work)
          <tr>
            <td>{{ \Carbon\Carbon::parse($work->date)->format('Y年m月d日') }}</td>
            <td>{{ $work ->clock_in }}</td>
            <td>{{ $work->clock_out }}</td>
          </tr>
      @endforeach

      @foreach ($rests as $rest)
          <tr>
            <td>{{ $rest->getFormattedRestTime() }}</td>
          </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif
@endsection