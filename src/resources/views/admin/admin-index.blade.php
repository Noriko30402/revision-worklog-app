@extends('layouts.default')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.admin-header')
<div class="container">
  <h1>{{$displayDate1  }}の勤怠</h1>
  <div class="month-form">
      <a href="{{ route('admin.index', ['date' => $prevDate]) }}" class="btn-date">← 前日</a>
        <h2>{{ $displayDate2 }}</h2>
      <a href="{{ route('admin.index', ['date' => $nextDate]) }}" class="btn-date">翌日 →</a>
  </div>

  <div class="table">
    <table>
      <thead class="table__header">
        <tr>
          <th>名前</th>
          <th>出勤</th>
          <th>退勤</th>
          <th>休憩</th>
          <th>合計</th>
          <th>詳細</th>
        </tr>
      </thead>

      <tbody class="table__main">
        @foreach ($works as $work)
          <tr>
            <td>{{ $work->staff->name }}</td>
            <td>{{ \Carbon\Carbon::parse($work->clock_in)->format('H:i') }}</td>
            <td>{{ \Carbon\Carbon::parse($work->clock_out)->format('H:i') }}</td>
            <td>{{ \Carbon\Carbon::parse($rest->total_rest_time)->format('H:i') }}</td>
            <td>{{ \Carbon\Carbon::parse($work->total_work_time)->format('H:i') }}</td>
            <td><a href="{{  route('admin.detail', ['work_id' => $work->id])  }}">詳細</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
