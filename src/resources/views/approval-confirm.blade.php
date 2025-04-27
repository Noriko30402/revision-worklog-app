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
        <td>{{ $pendingApplication->staff->name }}</td>
      </tr>

      <tr>
        <th>日付</th>
        <td>{{ \Carbon\Carbon::parse($pendingApplication->date)->format('Y/n/j') }}</td>
      </tr>
      <tr>
        <th>出勤</th>
        <td>{{ \Carbon\Carbon::parse($pendingApplication->clock_in)->format('H:i') }}</td>
        <td>~</td>
        <td>{{ \Carbon\Carbon::parse($pendingApplication->clock_out)->format('H:i') }}</td>
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
  <div class="word">
    <p>※承認待ちのため編集できません</p>
  </div>

</form>
</div>
@endsection


