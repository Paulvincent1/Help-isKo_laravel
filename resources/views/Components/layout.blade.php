<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('styles/css/student.css') }}" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <script src="{{ asset('js/custom.js') }}"></script>
  </head>
  <body>
    <div class="layout_admin">
      <div class="sidebar">
        <div>
          <div class="sidebar_header">
            <p>Help, isKo</p>
          </div>

          <div class="sidebar_content">
            <div>
              <img
                src="https://scontent.fbag1-2.fna.fbcdn.net/v/t1.15752-9/456197328_2663990113779847_7195666657576319816_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeGtilhY5HzqSuG-WCHfXC448mkTGv_HHYfyaRMa_8cdhyw3p1kVqWfBh0pLD6oplBAdLnAhgQ__O6PP07BldOwr&_nc_ohc=YP-o142jCxMQ7kNvgFQ1iUb&_nc_ht=scontent.fbag1-2.fna&oh=03_Q7cD1QHeOwO420L_Gwzh3w-xreSZj2lzg0RlSCBoaFB0g0hufw&oe=66F37392"
                alt=""
              />
            </div>
            <p>Hannah Laurice De Villena</p>
          </div>
          <nav>
            <ul>
              <a href="{{route('index')}}" class="{{Request::is('/') ? 'selected' : ''}}"
                ><li><i class="fa-solid fa-chart-line"></i> Dashboard</li></a
              >
              <a href="{{route('student')}}"  class="{{Request::is('student') ? 'selected' : ''}}"
                ><li><i class="fa-solid fa-graduation-cap"></i> Student</li></a
              >
          <a href="{{route('employee')}}" class="{{Request::is('employee') ? 'selected' : ''}}"
                ><li>
                  <i class="fa-solid fa-chalkboard-user"></i> Employee
                </li></a
              >
              <a href="{{route('announcement')}}" class="{{Request::is('announcement') ? 'selected' : ''}}"
                ><li><i class="fa-solid fa-scroll"></i> Announcement</li></a
              >
            </ul>
          </nav>
        </div>
        <div class="sidebar_logout">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            fill="currentColor"
            class="bi bi-box-arrow-left"
            viewBox="0 0 16 16"
          >
            <path
              fill-rule="evenodd"
              d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"
            />
            <path
              fill-rule="evenodd"
              d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"
            />
          </svg>
          <p>Logout</p>
        </div>
      </div>

      <div class="main_content">
        {{$slot}}
      </div>
      
    </div>
  </body>
</html>
