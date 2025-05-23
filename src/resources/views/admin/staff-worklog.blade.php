@extends('layouts.default')

@section('title','各スタッフ勤怠')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.admin-header')
<div class="container">
  <h1>{{ $staff->name }}さんの勤怠</h1>

  <div class="month-form">
    <a href="{{ route('staff.worklog', ['staff_id' => $staff_id, 'month' => $prevMonth]) }}" class="btn-date">← 前月</a>
    <h2>{{ $displayDate1 }}</h2>
    <a href="{{ route('staff.worklog', ['staff_id' => $staff_id, 'month' => $nextMonth]) }}" class="btn-date">次月 →</a>

  </div>

  <div class="table">
    <table>
      <thead class="table__header">
        <tr>
          <th>日付</th>
          <th>出勤</th>
          <th>退勤</th>
          <th>休憩</th>
          <th>合計</th>
          <th>詳細</th>
        </tr>
      </thead>

      <tbody class="table__main">
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
            @if ($workForThisDate)
              <td>{{ \Carbon\Carbon::parse($workForThisDate->total_rest_time)->format('H:i') }}</td>
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
            <td><a href="{{  route('admin.detail', ['work_id' => $workForThisDate])  }}">詳細</a></td>
            @else
              <td>-</td>
            @endif
          </tr>
        @endforeach
        </tbody>
    </table>
  </div>
      <!-- CSVエクスポートボタン -->
  <div class="export-form">
    <a href="{{ route('staff.export', ['staff_id' => $staff->id]) }}" class="csv">CSV出力</a>
  </div>

</div>

@endsection