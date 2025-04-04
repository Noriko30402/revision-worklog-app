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
      @foreach($dates as $date)
        <tr>
          <td>{{ $date->isoFormat('M/D（ddd）') }}</td>

        <!-- 出勤時間と退勤時間 -->
          @php
            $workForThisDate = $worksByDate[$date->toDateString()] ?? null;
          @endphp

          @if ($workForThisDate)
            <td>{{ \Carbon\Carbon::parse($workForThisDate->clock_in)->format('H:i') }}</td>
            <td>{{ \Carbon\Carbon::parse($workForThisDate->clock_out)->format('H:i') }}</td>
          @else
            <td>-</td>
            <td>-</td>
          @endif

        <!-- 休憩時間 -->
          @php
            $restForThisDate = $restsByDate[$date->toDateString()] ?? null;
          @endphp

          @if ($restForThisDate)
            <td>{{ \Carbon\Carbon::parse($restForThisDate->total_rest_time)->format('H:i') }}</td>
          @else
            <td>-</td>
          @endif

        <!-- 勤務時間 -->
          @if ($workForThisDate)
            <td>{{ \Carbon\Carbon::parse($workForThisDate->total_work_time)->format('H:i') }}</td>
          @else
            <td>-</td>
          @endif

        @if ($workForThisDate)
          <td><a  href={{ route('detail')}}>詳細</a></td>
        @else
          <td>-</td>
        @endif

        </tr>
    @endforeach
  </tbody>
  </table>
  </div>
@endsection