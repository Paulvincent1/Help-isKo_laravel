<x-layout>
  <section class="main_content-employee-student">
    <header>
      <p>Employee</p>
      <img
        src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
        alt=""
      />
    </header>
    <div class="employee-student__main-table">
      <div class="employee-student__main-table-header">
        <h2>Employees table</h2>
        <a href="{{ route('employee.employee_add') }}" class="employee">Add Employee</a>
      </div>
      <div class="table_window">
        <table id="myTable">
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
                  src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
                  alt=""
                />
              </td>
              <td>{{$employee->name}}</td>
              <td>{{$employee->employeeProfile->employee_number}}</td>
              <td><a href="{{ route('employee.viewProfile', ['id' => $employee->id] )}}">View</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <!-- <div class="employee__main-duty-posted">Duty posted</div> -->
  </section>
</x-layout>