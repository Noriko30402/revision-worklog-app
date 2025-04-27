@extends('layouts.default')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}">
<link rel="stylesheet" href="{{ asset('/css/approval.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.admin-header')
<div class="container">
  <h1>申請一覧</h1>
  <ul class="tabs">
    <li><a href="{{ route('admin.approval', ['tab' => 'approval']) }}"class="{{ request('tab', 'approval') == 'approval' ? 'active' : '' }}">承認待ち</a></li>
    <li><a href="{{ route('admin.approval', ['tab' => 'approved']) }}" class="{{ request('tab') == 'approved' ? 'active' : '' }}">承認済み</a></li>
  </ul>

  <div class="table">
    <table>
      <thead class="table__header">
        <tr>
          <th>状態</th>
          <th>名前</th>
          <th>対象日時</th>
          <th>申請理由</th>
          <th>申請日時</th>
          <th>詳細</th>
        </tr>
      </thead>

  <tbody class="table__main">
  <div class="tab-content">
    @if($tab == 'approval')
      @foreach ($pendingApplications as $pendingApplication)
        <tr>
          <td>
            @if ( $pendingApplication->approved  == 0)
              承認待ち
            @endif
          </td>
          <td>{{ $pendingApplication->staff->name }}</td>
          <td>{{ \Carbon\Carbon::parse($pendingApplication->date)->format('Y/n/j') }}</td>
          <td>{{ $pendingApplication->comment }}</td>
          <td>{{ \Carbon\Carbon::parse($pendingApplication->created_at)->format('Y/n/j') }}</td>
          <td><a href="{{ route('admin.approval.detail', ['work_id' => $pendingApplication->work_id]) }}">詳細</a></td>
        </tr>


      @endforeach

    @elseif($tab == 'approved')
      @foreach ($approvedApplications as $approvedApplication)
        <tr>
          <td>承認済み</td>
          <td>{{ $approvedApplication->staff->name }}</td>
          <td>{{ \Carbon\Carbon::parse($approvedApplication->date)->format('Y/n/j') }}</td>
          <td>{{ $approvedApplication->comment }}</td>
          <td>{{ \Carbon\Carbon::parse($approvedApplication->created_at)->format('Y/n/j') }}</td>
          <td><a href="{{ route('approval.detail', ['work_id' => $pendingApplication->work_id]) }}">詳細</a></td>
        </tr>
      @endforeach
    @endif
  </div>
    </table>
  </div>
</div>
@endsection