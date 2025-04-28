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
      @if(isset($pendingApplicationsGrouped))
        @foreach ($pendingApplicationsGrouped as $workId => $pendingApplication)
          <tr>
            <td>承認待ち</td>
            <td>{{ $pendingApplication->first()->staff->name }}</td>
            <td>{{ \Carbon\Carbon::parse($pendingApplication->first()->date)->format('Y/n/j') }}</td>
            <td>{{ $pendingApplication->first()->comment }}</td>
            <td>{{ \Carbon\Carbon::parse($pendingApplication->first()->created_at)->format('Y/n/j') }}</td>
            <td><a href="{{ route('admin.approval.detail', ['work_id' => $pendingApplication->first()->work_id]) }}">詳細</a></td>
          </tr>
        @endforeach
      @endif

    @elseif($tab == 'approved')
      @if(isset($approvedApplicationsGrouped))
        @foreach ($approvedApplicationsGrouped as $workId =>  $approvedApplication)
          <tr>
            <td>承認済み</td>
            <td>{{ $approvedApplication->first()->staff->name }}</td>
            <td>{{ \Carbon\Carbon::parse($approvedApplication->first()->date)->format('Y/n/j') }}</td>
            <td>{{ $approvedApplication->first()->comment }}</td>
            <td>{{ \Carbon\Carbon::parse($approvedApplication->first()->created_at)->format('Y/n/j') }}</td>
            <td><a href="{{ route('admin.approval.complete', ['work_id' => $approvedApplication->first()->work_id]) }}">詳細</a></td>
          </tr>
        @endforeach
      @endif
    @endif
  </div>
    </table>
  </div>
</div>
@endsection