<x-layout>
    <section class="main_content-employee-student">
        <header>
          <p>Duty</p>
          <img
            src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
            alt=""
          />
        </header>
        <div class="employee-student__main-table">
          <div class="employee-student__main-table-header">
            <h2>Duty table</h2>
            <a href="{{ route('duty.export')}}" class="duty">Export Duty Table</a>
          </div>
          <div class="table_window">
            <table id="myTable">
              <thead>
                <tr>
                  <th>Building</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Message</th>
                  <th>Max Scholar</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($duties as $duty)
                <tr>
                  
                  <td>{{ $duty->building }}</td>
                  <td>{{ $duty->date }}</td>
                  <td>{{ $duty->duty_status }}</td>
                  <td>{{ $duty->message }}</td>
                  <td>{{ $duty->max_scholars }}</td>
             
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <!-- <div class="employee__main-duty-posted">Duty posted</div> -->
      </section>
</x-layout>