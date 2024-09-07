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

  <form action="announcement" class="create_account">
    <div class="form-header">
      <div class="target-selection">
        <label for="target">Target:</label>
        <select id="target" name="target">
          <option value="student">Student</option>
          <option value="professor">Professor</option>
        </select>
      </div>
      <div class="date-selection">
        <label for="announcement-date">Date:</label>
        <input
          type="date"
          id="announcement-date"
          name="announcement-date"
        />
      </div>
    </div>
    <div>
      <label class="label_layout" for="announcement">Announcement:</label
      ><br />
      <textarea
        id="announcement"
        class="announcement"
        name="announcement"
        rows="30"
        cols="100"
        placeholder="Write your announcement here..."
      ></textarea
      ><br /><br />
      <div class="footer">
        <p>From: Admin</p>
      </div>
      <div class="submit-button">
        <input type="submit" value="Create" />
      </div>
    </div>
  </form>
</x-layout>