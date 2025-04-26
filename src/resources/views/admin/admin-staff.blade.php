@extends('layouts.default')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/staff.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.admin-header')
<div class="container">
  <h1>スタッフ一覧</h1>
  <div class="table">
    <table>
      <thead class="table__header">
        <tr>
          <th>名前</th>
          <th>メールアドレス</th>
          <th>月次勤怠</th>
        </tr>
      </thead>

      <tbody class="table__main">
        @foreach ($staffs as $staff)
          <tr>
            <td>{{ $staff->name }}</td>
            <td>{{ $staff->email }}</td>
            <td><a href="{{ route('staff.worklog', ['staff_id' => $staff->id]) }}">詳細</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
