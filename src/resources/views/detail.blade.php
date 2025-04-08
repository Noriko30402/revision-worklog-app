@extends('layouts.default')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/worklog.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.staff-header')

<div class="container">
<h1>勤怠詳細</h1>

<form action="" method="">
  @csrf
  <div class="table">
    <table>
      <tr>
        <th>名前</th>
        <td>{{$staff['name']}}</td>
      </tr>

      <tr>
        <th>日付</th>
        <input type="hidden" name="date" value="{{ $work->date }}">
        <td>{{ \Carbon\Carbon::parse($work->date)->isoFormat('Y[年]') }}</td>
        <td>{{ \Carbon\Carbon::parse($work->date)->isoFormat('M[月]D[日]') }}</td>
      </tr>

      <tr>
        <th>出勤・退勤</th>
        <td><input type="text" name="clock_in" value="{{ \Carbon\Carbon::parse($work->clock_in)->format('H:i') }}"></td>
        <td><input type="text" name="clock_out" value="{{ \Carbon\Carbon::parse($work->clock_out)->format('H:i') }}"></td>
      </tr>

      <tr>
        <th>休憩</th>
        <td><input type="text" name="rest_in" value="{{ \Carbon\Carbon::parse($rest->rest_in)->format('H:i') }}"></td>
        <td><input type="text" name="rest_out" value="{{ \Carbon\Carbon::parse($rest->rest_out)->format('H:i') }}"></td>
      </tr>

      <tr>
        <th>備考</th>
        <td><input type="text" name="comment" value="{{ $work->comment}}"></td>
      </tr>
    </table>
  </div>

  <div class="form__button">
    <button type="submit">修正</button>
  </div>

</form>
</div>
@endsection
