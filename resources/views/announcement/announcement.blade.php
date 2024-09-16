<x-layout>
    <div class="main_content_nav">
      <ul>
        <a href="{{ route('announcement') }}" class="selected_main"><li>List of Announcement</li></a>
        <a href="{{ route('announcement.announcement_add') }}"><li>Manage Announcement</li></a>
      </ul>
    </div>
    <div class="main-container">
      <div class="list-container">
          @foreach($announcements as $announcement)
              <div class="announcement-container">
                  <a href="#" class="edit" onclick="openEditModal({{ $announcement->id }}, '{{ $announcement->heading }}', '{{ $announcement->description }}', '{{ $announcement->announcement_image }}')">Edit</a>
                  <form action="{{ route('announcements.delete', $announcement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="delete">Delete</button>
                  </form>
                  <p class="heading">{{ $announcement->heading }}</p>
                  <p class="description">{{ $announcement->description }}</p>
              </div>
          @endforeach
      </div>
    </div>

    <!-- Modal Form -->
    <div id="editModal" class="modal" style="display: none;">
      <div class="modal-content create_account">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Announcement</h2>
        <form id="editForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="announcement_id" id="announcement_id">

            <label for="heading">Heading:</label>
            <input
                type="text"
                id="heading"
                name="heading"
                placeholder="Enter the heading"
                required
            /><br /><br />

            <label for="description">Description:</label>
            <textarea
                id="description"
                name="description"
                rows="10"
                placeholder="Enter the description here..."
                required
            ></textarea><br /><br />

            <label for="announcement_image">Upload File:</label>
            <input
                type="file"
                id="announcement_image"
                name="announcement_image"
            /><br /><br />

            <div class="footer">
                <p>From: Admin</p>
            </div>
            <div class="submit-button">
                <input type="submit" value="Update" />
            </div>
        </form>
      </div>
    </div>

    <!-- Add CSS for Modal -->
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      /* Modal background */
      .modal {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
      }

      /* Modal content */
      .modal-content {
        background: white;
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        padding: 20px;
        position: relative;
      }

      /* Close button */
      .close {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        font-size: 20px;
        color: #333;
      }

      /* Styling the form container */
      .create_account {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #f9f9f9;
        font-family: Arial, sans-serif;
      }

      /* Styling labels */
      .create_account label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
      }

      /* Styling text input and textarea */
      .create_account input[type="text"],
      .create_account textarea,
      .create_account input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
      }

      /* Specific styling for textarea */
      .create_account textarea {
        resize: vertical;
      }

      /* Styling the submit button */
      .create_account .submit-button input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
      }

      .create_account .submit-button input[type="submit"]:hover {
        background-color: #45a049;
      }

      /* Styling the footer section */
      .create_account .footer {
        margin-top: 20px;
        font-size: 14px;
        color: #555;
      }
    </style>

    <!-- Add JavaScript for Modal Handling -->
    <script>
     function openEditModal(id, heading, description, image) {
    document.getElementById('announcement_id').value = id;
    document.getElementById('heading').value = heading;
    document.getElementById('description').value = description;
    document.getElementById('editForm').action = `/announcements/${id}`; // This will update the form action URL
    document.getElementById('editModal').style.display = 'flex';
}


      function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
      }
    </script>
  </x-layout>
