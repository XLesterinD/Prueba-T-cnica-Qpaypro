<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('landing') }}">QpayFundation</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('donar') }}">Donar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
        </li>
      </ul>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class ="btn btn-danger" type="submit">Sesión</button>
      </form>
    </div>
  </div>
</nav>
