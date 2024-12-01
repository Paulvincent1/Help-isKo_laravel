<x-layout>
  <section class="main_content">
    <header>
      <p>Dashboard</p>
      <img
        src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
        alt=""
      />
    </header>

    <div class="employee-student_table">
      <div class="employee_table">
        <div class="employee-student_table-header">
          <p>Employee Table</p>
          <div>
            <a href="{{ route('employee')}}">See all</a>
          </div>
        </div>
        <div class="table_window tw-dashboard">
          <table>
            <thead>
              <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Employee ID</th>
                <th>View</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($employees as $employee)
              <tr>
                <td>
                  <img
                    src="{{ $employee->employeeProfile == null ||  $employee->employeeProfile->profile_img == '' ||  $employee->employeeProfile->profile_img == null  ? 'https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg' : $employee->employeeProfile->profile_img }}"
                    alt=""
                  />
                </td>
                <td>{{$employee->name}}</td>
                <td>{{$employee->employeeProfile->employee_number ?? 'No employee profile'}}</td>
                @if ($employee->employeeProfile === null)
                <td><a href="{{ route('employee.existing_employee_add_profile', ['id' => $employee->id] )}}">Add Profile</a></td>
                @else
                <td><a href="{{ route('employee.viewProfile', ['id' => $employee->id] )}}">View</a></td>
                @endif
              </tr>
              @endforeach
            
            </tbody>
          </table>
        </div>
      </div>
      <div class="student_table">
        <div class="employee-student_table-header">
          <p>Student Table</p>
          <div>
            <a href="{{ route('student')}}">See all</a>
          </div>
        </div>
        <div class="table_window tw-dashboard">
          <table>
            <thead>
              <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Student ID</th>
                <th>View</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($students as $student)      
              <tr>
                <td>
                  <img
                    src="{{ $student->studentProfile == null ||  $student->studentProfile->profile_img == '' || $student->studentProfile->profile_img == null ? 'https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg' : $student->studentProfile->profile_img}}"
                    alt=""
                  />
                </td>
                <td>{{$student->name}}</td>
                <td>{{ $student->studentProfile->student_number ?? 'No student profile'}}</td>
                @if ($student->studentProfile == null)
                <td><a href="{{ route('students.existing_student_add_profile', ['id' => $student->id])}}">Add Profile</a></td>
                @elseif ($student->hkStatus == null)
                <td><a href="{{ route('students.existing_student_add_profile', ['id' => $student->id])}}">Add HK Status</a></td>
                @else
                <td><a href="{{ route('student.viewProfile', ['id' => $student->id])}}">View</a></td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="main_content-data">
      <div class="main_content-data-card">
        <div class="employee-data">
          <div class="icon-employee">
            <span class="material-symbols-outlined"> badge </span>
          </div>
          <p class="title">Total Employee</p>
          <div class="row-count">
            <p class="count">{{$employees->count()}}</p>
            <a href="{{route('employee')}}">view all</a>
          </div>
        </div>
        <div class="student-data">
          <div class="icon-student">
            <span class="material-symbols-outlined"> groups </span>
          </div>
          <p class="title">Total Student</p>
          <div class="row-count">
            <p class="count">{{$students->count()}}</p>
            <a href="{{route('student')}}">view all</a>
          </div>
        </div>
        <div class="renewal-data">
          <div class="icon-renewal">
            <span class="material-symbols-outlined"> view_list </span>
          </div>
          <p class="title">Renewal Requests</p>
          <div class="row-count">
            <p class="count">400</p>
            <a href="{{route('renewal')}}">view all</a>
          </div>
        </div>
      </div>

      <div class="chart-1">
        <div class="chart">
          <div class="chart-header">
            <a href="{{route('duty')}}">See Duty Table</a>
          </div>
          <canvas id="myChart"></canvas>
        </div>
      </div>
      <!-- <div class="chart-2"></div> -->
    </div>
  </section>

  <script src="{{ asset('js/chart.js')}}"></script>
  <script>
    const ctx = document.getElementById("myChart");
    let totalDutiesPerWeek = {!! json_encode($totalDutiesPerWeek) !!}
    console.log(totalDutiesPerWeek);
    new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
        datasets: [
          {
            label: "# of Duty requests this week",
            data: totalDutiesPerWeek,
            borderWidth: 1,
            backgroundColor: '#002855',
          },
        ],
      },
      options: {
        // responsive: true
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  </script>
</x-layout>