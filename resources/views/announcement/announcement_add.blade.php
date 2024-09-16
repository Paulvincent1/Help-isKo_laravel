<x-layout>
  <div class="main_content_nav">
    <ul>
      <a href="{{route('announcement')}}"><li>List of Announcement</li></a>
      <a href="{{route('announcement.announcement_add')}}" class="selected_main"
        ><li>Manage Announcement</li></a
      >
    </ul>
  </div>
  <div class="add_form-student">
    <div class="guide_tab">
      <div class="guide_tab-content">
        <div class="guide_tab-register current-tab">
          <p>Create Announcement</p>
        </div>
      </div>
    </div>
  </div>

  <form action="{{route('announcements.store')}}" method="POST" class="create_account" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="heading">Heading:</label><br />
        <input
            type="text"
            id="heading"
            name="heading"
            placeholder="Enter the heading"
            required
        /><br /><br />

        <label for="description">Description:</label><br />
        <textarea
            id="description"
            name="description"
            rows="10"
            placeholder="Enter the description here..."
            required
        ></textarea><br /><br />

        <label for="announcement_image">Upload File:</label><br />
        <input
            type="file"
            id="announcement_image"
            name="announcement_image"
        /><br /><br />

        <div class="footer">
            <p>From: Admin</p>
        </div>
        <div class="submit-button">
            <input type="submit" value="Submit" />
        </div>
    </div>
</form>
</x-layout>
