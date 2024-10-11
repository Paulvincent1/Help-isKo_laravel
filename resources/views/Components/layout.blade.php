<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"
    />
    <!-- for data tables -->
    <link
      rel="stylesheet"
      href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css"
    />

    <link rel="stylesheet" href="{{ asset('styles/css/employee-student.css')}}" />
    <link rel="stylesheet" href="{{ asset('styles/css/styles.css')}}" />
    <title>Document</title>
  </head>
  <body>
    <div class="layout_admin">
      <section class="sidebar">
        <div class="logo">
          <img src="{{asset('asset/logo-removebg-preview.png')}}" alt="">
          <p>Boaf</p>
        </div>

        <nav>
          <ul>
            <li class="{{ request()->is('/') ? 'active' : ''}} {{  request()->is('duty*') ? 'active' : ''}}">
              <span class="material-symbols-outlined"> dashboard </span
              ><a href="{{ route('index') }}" class="{{ request()->is('/') ? 'active-link' : ''}} {{ request()->is('duty*') ? 'active-link' : ''}}">Dashboard </a>
            </li>
            <li class="{{ request()->is('employee*') ? 'active' : ''}}">
              <span class="material-symbols-outlined"> badge </span
              ><a href="{{ route('employee')}}" class="{{ request()->is('employee*') ? 'active-link' : ''}}">Employee </a>
            </li>
            <li class="{{ request()->is('student*') ? 'active' : ''}}">
              <span class="material-symbols-outlined"> groups </span>
              <a href="{{ route('student')}}" class="{{ request()->is('student*') ? 'active-link' : ''}}">Student </a>
            </li>
            <li class="{{ request()->is('announcement*') ? 'active' : ''}}">
              <span class="material-symbols-outlined"> campaign </span>
              <a href="{{ route('announcement') }}" class="{{ request()->is('announcement*') ? 'active-link' : ''}}">Announcement </a>
            </li>
            <li class="{{ request()->is('renewal*') ? 'active' : ''}}">
              <span class="material-symbols-outlined"> view_list </span>
              <a href="{{ route('renewal') }}"  class="{{ request()->is('renewal*') ? 'active-link' : ''}}">Renewal Requests </a>
            </li>
          </ul>
        </nav>
      </section>


      {{ $slot }}


    </div>
    <script
      src="https://code.jquery.com/jquery-3.7.1.min.js"
      integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
      crossorigin="anonymous"
    ></script>

    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    
    <script src="{{ asset('js/index.js')}}"></script>
  </body>
</html>
