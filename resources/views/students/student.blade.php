<x-layout>
  <section class="main_content-employee-student">
    <header>
      <p>Student</p>
      <img
        src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
        alt=""
      />
    </header>
    <div class="employee-student__main-table">
      <div class="employee-student__main-table-header">
        <h2>Students table</h2>
        <a href="{{ route('students.student_add') }}" class="student">Add Student</a>
      </div>
      <div class="table_window">
        <table id="myTable">
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
                src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
                alt=""
                />
              </td>
              <td>{{ $student->name }}</td>
              <td>{{ $student->studentProfile->student_number ?? 'No student profile'}}</td>
              @if ($student->studentProfile == null)
              <td><a href="{{ route('students.existing_student_add_profile', ['id' => $student->id])}}">Add Profile</a></td>
              @else   
              <td><a href="{{ route('student.viewProfile', ['id' => $student->id])}}">View</a></td>
              @endif
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <!-- <div class="employee__main-duty-posted">Duty posted</div> -->
  </section>
</x-layout>