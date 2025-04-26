<header class="header">
  <div class="header__logo">
      <a href="/staff/login"><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
      <link rel="stylesheet" href="{{ asset('/css/header.css')  }}">
  </div>
  @if( !in_array(Route::currentRouteName(), ['admin/login']) )
  <nav class="header__nav">
      <ul>
        @if(Auth::check())
          <li><a href="{{ route('admin.index')}}">勤怠一覧</a></li>
          <li><a href="{{ route('staff.index')}}">スタッフ一覧</a></li>
          <li><a href="{{ route('admin.approval')}}">申請一覧</a></li>
          <li>
            <form action="{{route('admin.logout') }}" method="post">
              @csrf
              <button class="header__logout">ログアウト</button>
            </form>
          </li>
        @endif
      </ul>
  </nav>
  @endif
</header>