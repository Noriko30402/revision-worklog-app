@extends('layouts.default')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.staff-header')

<div class="container">
  <h1>勤怠詳細</h1>
  <div class="table">
    <table>
      <tr>
        <th>名前</th>
        <td>{{ $approvedApplication->staff->name }}</td>
      </tr>

      <tr>
        <th>日付</th>
        <td>{{ \Carbon\Carbon::parse($approvedApplication->date)->format('Y/n/j') }}</td>
      </tr>

      <tr>
        <th>出勤</th>
        <td>{{ \Carbon\Carbon::parse($approvedApplication->clock_in)->format('H:i') }}</td>
        <td>~</td>
        <td>{{ \Carbon\Carbon::parse($approvedApplication->clock_out)->format('H:i') }}</td>
      </tr>

      @foreach ($rests as $index => $rest)
      <tr>
        <th>休憩{{ $index + 1 }}</th>
        <td>{{ \Carbon\Carbon::parse($rest->rest_in)->format('H:i') }}</td>
        <td>~</td>
        <td>{{ \Carbon\Carbon::parse($rest->rest_out)->format('H:i') }}</td>
      </tr>
      @endforeach

      <tr>
        <th>コメント</th>
        <td>{{ $approvedApplication->comment }}</td>
      </tr>
    </table>
  </div>
  <button type="submit" class="edit" disabled style="pointer-events: none; opacity: 0.5;">承認済み</button>
  </div>
</div>
@endsection


