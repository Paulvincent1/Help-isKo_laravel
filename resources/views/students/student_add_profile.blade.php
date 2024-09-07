<x-layout>
  <div class="main_content_nav">
    <ul>
      <a href="{{Route('student')}}"><li>Student table</li></a>
      <a href="{{Route('students.student_add')}}" class="selected_main"><li>Manage Students</li></a>
    </ul>
  </div>
  <div class="add_form-student">
    <div class="guide_tab">
      <div class="guide_tab-content">
        <div class="guide_tab-register">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            fill="currentColor"
            class="bi bi-1-circle"
            viewBox="0 0 16 16"
          >
            <path
              d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M9.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z"
            />
          </svg>

          <p>Register</p>
        </div>
        <div class="hr"><hr /></div>
        <div class="guide_tab-register">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            fill="currentColor"
            class="bi bi-2-circle"
            viewBox="0 0 16 16"
          >
            <path
              d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306"
            />
          </svg>
          <p>Assigning HK Duty Hours</p>
        </div>
        <div class="hr"><hr /></div>
        <div class="guide_tab-register current-tab">
          
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            fill="currentColor"
            class="bi bi-3-circle"
            viewBox="0 0 16 16"
          >
            <path
              d="M7.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318"
            />
            <path
              d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8"
            />
          </svg>
          <p>Student Profile</p>
        </div>
      </div>
    </div>
  </div>

  <form action="student">
    <div class="form_layout">
      <div>
        <p>Student Information</p>
        <div class="form_layout-information">
          <div>
            <label for="">Student Number</label>
            <input
              type="text"
              id="student_number"
              placeholder="Student Number"
            />
          </div>
          <div>
            <label for="">College</label>
            <input type="text" placeholder="College" />
          </div>
          <div>
            <label for="">Course</label>
            <input type="text" placeholder="Course" />
          </div>

          <div>
            <label for="">Department</label>
            <input
              type="text"
              id="student_number"
              placeholder="Department"
            />
          </div>
          <div>
            <label for="">Semester</label>
            <input type="text" placeholder="Semester" />
          </div>
          <div>
            <label for="">Learning Modality</label>
            <input type="text" placeholder="Learning Modality" />
          </div>
          <div>
            <label for="">Profile Picture</label>
            <input type="file" placeholder="Profile Picture" />
          </div>
        </div>
      </div>
      <div>
        <p>Family Background</p>
        <div class="form_layout-information">
          <div>
            <label for="">Father name</label>
            <input
              type="text"
              id="student_number"
              placeholder="Father name"
            />
          </div>
          <div>
            <label for="">Father Contact Number</label>
            <input type="text" placeholder="Father Contact Number" />
          </div>
          <div>
            <label for="">Mother Name</label>
            <input type="text" placeholder="Mother Name" />
          </div>

          <div>
            <label for="">Mother Contact Number</label>
            <input
              type="text"
              id="student_number"
              placeholder="Department"
            />
          </div>
        </div>
      </div>
      <div>
        <p>Current and Permanent Address</p>
        <div class="form_layout-information">
          <div>
            <label for="">Current Address</label>
            <input
              type="text"
              id="student_number"
              placeholder="Current Address"
            />
          </div>
          <div>
            <label for="">Current Province</label>
            <input type="text" placeholder="Current Province" />
          </div>
          <div>
            <label for="">Current Country</label>
            <input type="text" placeholder="Current Country" />
          </div>

          <div>
            <label for="">Current City</label>
            <input
              type="text"
              id="student_number"
              placeholder="Current City"
            />
          </div>
          <div>
            <label for="">Permanent Address</label>
            <input
              type="text"
              id="student_number"
              placeholder="Permanent Address"
            />
          </div>
          <div>
            <label for="">Permanent Province</label>
            <input type="text" placeholder="Permanent Province" />
          </div>
          <div>
            <label for="">Permanent Country</label>
            <input type="text" placeholder="Permanent Country" />
          </div>

          <div>
            <label for="">Permanent City</label>
            <input
              type="text"
              id="student_number"
              placeholder="Permanent City"
            />
          </div>
        </div>
      </div>
      <div>
        <p>Emergency Contact Details</p>
        <div class="form_layout-information">
          <div>
            <label for="">Emergency Person Name</label>
            <input
              type="text"
              id="student_number"
              placeholder="Father name"
            />
          </div>
          <div>
            <label for="">Emergency Address</label>
            <input type="text" placeholder="Emergency Address" />
          </div>
          <div>
            <label for="">Relation</label>
            <input type="text" placeholder="Relation" />
          </div>

          <div>
            <label for="">Emergency Contact Number</label>
            <input
              type="text"
              id="student_number"
              placeholder="Emergency Contact Number"
            />
          </div>
        </div>
      </div>
      <div class="button-container">
        <button class="back-button" type="button" onclick="goBack()">
          Back
        </button>
        <input
          class="submit-button-add"
          type="submit"
          value="Submit"
          onclick="submitForm()"
        />
      </div>
  </form>
</x-layout>