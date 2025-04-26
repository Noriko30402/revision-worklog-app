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
    <form class="form__button" action="post" action={{ route('approval.update',['work_id' => $pendingApplication->work_id])}}>
  <table>
      <tr>
        <th>名前</th>
        <td>{{ $pendingApplication->staff->name }}</td>
      </tr>

      <tr>
        <th>日付</th>
        <td>{{ \Carbon\Carbon::parse($pendingApplication->date)->format('Y/n/j') }}</td>
      </tr>
      <tr>
        <th>出勤</th>
        <td>{{ $pendingApplication->clock_in }}</td>
        <td>~</td>
        <td> {{ $pendingApplication->clock_out }}</td>
      </tr>

      @foreach ($restArray['rest_in'] as $i => $in)
      <tr>
        <th>休憩{{ $i + 1 }}</th>
        <td>{{ $in }}</td>
        <td>~</td>
        <td>{{ $restArray['rest_out'][$i] ?? '' }}</td>
      </tr>
    @endforeach

      <tr>
        <th>コメント</th>
        <td>{{ $pendingApplication->comment }}</td>
      </tr>
  </table>
  </div>
    <button type="submit" class="edit">承認</button>
</form>
</div>
@endsection


