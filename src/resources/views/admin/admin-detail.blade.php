@extends('layouts.default')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css') }}">
@endsection

@section('content')
@include('components.admin-header')

<div class="container">
  <h1>勤怠詳細</h1>
  @if (session('success'))
    <div class="alert-success">
      {{ session('success') }}
    </div>
  @endif

  <form action="{{ route('admin.edit', ['work_id' => $work->id])}}" method="post">
    @csrf
    @method('PUT')
  <div class="table">
    <table>
      <tr>
        <th>名前</th>
        <td>{{ $work->staff->name }}</td>
      </tr>

      <input type="hidden" name="work_id" value="{{ $work->id }}">
      <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($work->date)->format('Y-m-d') }}">
      <tr>
        <th>日付</th>
        <td colspan="2">
          {{ \Carbon\Carbon::parse($work->date)->isoFormat('Y年M月D日') }}
        </td>
      </tr>

      <tr>
        <th>出勤・退勤</th>
        <td><input type="text" name="clock_in" value="{{ \Carbon\Carbon::parse($work->clock_in)->format('H:i') }}"></td>
        <td>~</td>
        <td><input type="text" name="clock_out" value="{{ \Carbon\Carbon::parse($work->clock_out)->format('H:i') }}"></td>
      </tr>

      @foreach ($rests as $index => $rest)
      <tr>
        <th>休憩{{ $index + 1 }}</th>
        <td><input type="text" name="rest_in[]" value="{{ \Carbon\Carbon::parse($rest->rest_in)->format('H:i') }}"></td>
        <td>~</td>
        <td><input type="text" name="rest_out[]" value="{{ \Carbon\Carbon::parse($rest->rest_out)->format('H:i') }}"></td>
      </tr>
      @endforeach

      <tr>
        <th>備考</th>
        <td colspan="3">
          <input class="comment" type="text" name="comment" value="{{ $work->comment }}">
        </td>
      </tr>
    </table>
  </div>

    <div class="form__error">
      @error('clock_in')
        <span class="invalid-feedback">{{ $message }}</span>
      @enderror
      @error('clock_out')
        <span class="invalid-feedback">{{ $message }}</span>
      @enderror
      @error('rest_in')
        <span class="invalid-feedback">{{ $message }}</span>
      @enderror
      @error('rest_out')
        <span class="invalid-feedback">{{ $message }}</span>
      @enderror
      @error('comment')
        <span class="invalid-feedback">{{ $message }}</span>
      @enderror
    </div>

    <div class="form__button">
      <button type="submit" class="edit">修正</button>
    </div>
  </form>
</div>
@endsection
