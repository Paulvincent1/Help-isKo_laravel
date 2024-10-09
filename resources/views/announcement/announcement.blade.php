<x-layout>
  <section class="main_content-employee-student">
    <header>
      <p>Announcement</p>
      <img
        src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
        alt=""
      />
    </header>
    <div class="employee-student__main-table">
      <div class="employee-student__main-table-header">
        <h2>Announcement table</h2>
        <a href="{{ route('announcement.add') }}" class="announcement"
          >Add Announcement</a
        >
      </div>
      <div class="table_window">
        <table id="myTable">
          <thead>
            <tr>
              <th>Photo</th>
              <th>Heading</th>
              <th>Description</th>
              <th>View</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($announcements as $announcement)
            <tr>
              <td>
                <img
                  src="{{ $announcement->announcement_image == ''? 'https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg' :  $announcement->announcement_image}}"
                  alt=""
                />
              </td>
              <td>{{ $announcement->heading }}</td>
              <td>{{ $announcement->description }}</td>
              <td>
                <form action="{{ route('announcement.delete', ['id' =>  $announcement->id ]) }}" method="POST">
                  @method('DELETE')
                  @csrf
                  <input
                  type="submit"
                  href="/profile/student-profile.html"
                  class="announcement-delete"
                  value="Delete"
                  />
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <!-- <div class="employee__main-duty-posted">Duty posted</div> -->
  </section>
</x-layout>