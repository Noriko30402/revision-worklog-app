<header class="header">
  <div class="header__logo">
      <a href="/staff/login"><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
  </div>
  @if( !in_array(Route::currentRouteName(), ['register', 'staff/login']) )
  <nav class="header__nav">
      <ul>
        @if(Auth::check())
          <li><a href="{{  route('staff.attendance')  }}">勤怠</a></li>
          <li><a href="{{ route('index') }}">勤怠一覧</a></li>
          <li><a href="">申請</a></li>
          <li>
            <form action="{{ route('staff.logout') }}" method="post">
              @csrf
              <button class="header__logout">ログアウト</button>
            </form>
          </li>
        @endif
      </ul>
  </nav>
  @endif
</header>