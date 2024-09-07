<x-layout>
  <div class="main_content_nav">
    <ul>
      <a href="{{route('student')}}" class="selected_main"
        ><li>Student table</li></a
      >
      <a href="{{Route('students.student_add')}}"><li>Manage Student</li></a>
    </ul>
    <div class="progress-tracking">
      <h2>Track Student Duty Hours</h2>
      <table>
        <thead>
          <tr>
            <th>Student Name</th>
            <th>Assigned Hours</th>
            <th>Completed Hours</th>
            <th>Progress</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>John Doe</td>
            <td>50</td>
            <td>30</td>
            <td>
              <div class="progress-bar">
                <div class="progress" style="width: 60%"></div>
              </div>
            </td>
          </tr>
          <!-- More rows as needed -->
        </tbody>
      </table>
    </div>
  </div>
</x-layout>