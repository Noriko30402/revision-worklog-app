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
        <td>{{$staff['name']}}</td>
      </tr>

      <tr>
        <th>日付</th>
        <td> {{ \Carbon\Carbon::parse($application->date)->format('Y年n月j日') }}</td>
      </tr>
      <tr>
        <th>出勤</th>
        <td>{{ \Carbon\Carbon::parse($application->clock_in)->format('H:i') }}</td>
        <td>~</td>
        <td>{{ \Carbon\Carbon::parse($application->clock_out)->format('H:i') }}</td>
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
        <td>{{ $application->comment }}</td>
      </tr>
  </table>
  </div>
  <div class="word">
    <p>※承認待ちのため編集できません</p>
  </div>

</form>
</div>
@endsection


