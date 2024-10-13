<x-layout>
  <section class="main_content-employee-student">
    <header>
      <p>Student / Register</p>
      <img
        src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
        alt=""
      />
    </header>
    <div class="employee-student__main-register">
      <div class="employee-student__main-register-header">
        <div class="employee-student__main-register-header-content">
          <div>
            <span class="material-symbols-outlined"> check_circle </span>
            <p>Personal Details</p>
          </div>
          <div>
            <span class="material-symbols-outlined"> check_circle </span>
            <p>Duty Hours</p>
          </div>
          <div class="current_active-page-indicator">
            <span class="material-symbols-outlined current_active-page">
              check_circle
            </span>
            <p>Student Profile</p>
          </div>
        </div>
      </div>
      <form
        class="registration-layout"
        action="{{ request()->route('id') ? route('students.exisitng_student_add_profile_post_store', ['id' => request()->route('id')]) : route('students.student_add_profile_post') }}"
        enctype="multipart/form-data"
        method="POST"
      >
      @csrf
        <p class="registration-layout-header">Basic Details</p>
        <div class="registration-layout-input">
          <div>
            <label for="">First name</label>
            <input
              type="text"
              name="first_name"
              class="employee-input"
              placeholder="ex. John Doe"
              value="{{ old('first_name') }}"
            />
            @if ($errors->any())
                @foreach ($errors->get('first_name') as $error)
                    <p style="color: red">{{ $error }}</p>
                @endforeach
            @endif
          </div>
          <div>
            <label for="">Last name</label>
            <input
              type="text"
              name="last_name"
              class="employee-input"
              placeholder="ex. john@gmail.com"
                 value="{{ old('last_name') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('last_name') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Birthday</label>
            <input
              type="text"
              name="birthday"
              class="employee-input"
              placeholder="ex. August 5, 2004"
              value="{{ old('birthday') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('birthday') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Contact number</label>
            <input
              type="number"
              name="contact_number"
              class="employee-input"
              placeholder="ex. 09xxxxxxxxx"
              value="{{ old('contact_number') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('contact_number') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="student_profile-img">Profile image</label>
            <input
              type="file"
              name="profile_img"
              id="student_profile-img"
              class="employee-input"
              value="{{ old('profile_img') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('profile_img') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
        </div>
        <p class="registration-layout-header">Education</p>
        <div class="registration-layout-input">
          <div>
            <label for="">Student ID</label>
            <input
              type="tel"
              name="student_number"
              class="employee-input"
              placeholder="ex. 03-2323-xxxxxx"
              value="{{ old('student_number') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('student_number') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">College</label>
            <input
              type="text"
              name="college"
              class="employee-input"
              placeholder="ex. UPANG"
              value="{{ old('college') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('college') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Course</label>
            <input
              type="text"
              name="course"
              class="employee-input"
              placeholder="ex. BSIT"
              value="{{ old('course') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('college') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Department</label>
            <input type="text" name="department" class="employee-input" placeholder="CITE" value="{{ old('department')}}" />
            @if ($errors->any())
            @foreach ($errors->get('first_name') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Semester</label>
            <input
              type="text"
              name="semester"
              class="employee-input"
              placeholder="ex. Y3S1"
              value="{{ old('semster') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('semester') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Learning modality</label>
            <input
              type="text"
              name="learning_modality"
              class="employee-input"
              placeholder="ex. Flex"
              value="{{ old('learning_modality') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('learning_modality') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
        </div>
        <p class="registration-layout-header">Parents</p>
        <div class="registration-layout-input">
          <div>
            <label for="">Father's name</label>
            <input
              type="text"
              name="father_name"
              class="employee-input"
              placeholder="ex. John Doe"
              value="{{ old('father_name') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('father_name') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Father's contact number</label>
            <input
              type="number"
              name="father_contact_number"
              class="employee-input"
              placeholder="ex. 09xxxxxxxxx"
              value="{{ old('father_contact_number') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('father_contact_number') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Mother's name</label>
            <input
              type="text"
              name="mother_name"
              class="employee-input"
              placeholder="ex. Angel Doe"
              value="{{ old('mother_name') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('mother_name') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Mother's contact number</label>
            <input
              type="number"
              name="mother_contact_number"
              class="employee-input"
              placeholder="ex. 09xxxxxxxxx"
              value="{{ old('mother_contact_number') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('mother_contact_number') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
        </div>
        <p class="registration-layout-header">Address</p>
        <div class="registration-layout-input">
          <div>
            <label for="">Current address</label>
            <input
              type="text"
              name="current_address"
              class="employee-input"
              placeholder="ex. 1xx Amansabina Mangaldan"
              value="{{ old('current_address') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('current_address') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Current province</label>
            <input
              type="text"
              name="current_province"
              class="employee-input"
              placeholder="ex. Pangasinan"
              value="{{ old('current_province') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('current_province') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Current country</label>
            <input
              type="text"
              name="current_country"
              class="employee-input"
              placeholder="ex. Philippines"
              value="{{ old('current_country') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('current_country') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Current city</label>
            <input
              type="text"
              name="current_city"
              class="employee-input"
              placeholder="ex. Dagupan"
              value="{{ old('current_city') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('current_city') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Permanent address</label>
            <input
              type="text"
              name="permanent_address"
              class="employee-input"
              placeholder="ex. 1xx Amansabina Mangaldan"
              value="{{ old('permanent_address') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('permanent_address') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Permanent province</label>
            <input
              type="text"
              name="permanent_province"
              class="employee-input"
              placeholder="ex. Pangasinan"
              value="{{ old('permanent_province') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('permanent_province') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Permanent country</label>
            <input
              type="text"
              name="permanent_country"
              class="employee-input"
              placeholder="ex. Philippines"
              value="{{ old('permanent_country') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('permanent_country') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Permanent city</label>
            <input
              type="text"
              name="permanent_city"
              class="employee-input"
              placeholder="ex. Dagupan"
              value="{{ old('permanent_city') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('permanent_city') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
        </div>
        <p class="registration-layout-header">Emergency</p>
        <div class="registration-layout-input">
          <div>
            <label for="">Emergency person's contact name</label>
            <input
              type="text"
              name="emergency_person_name"
              class="employee-input"
              placeholder="ex. Angel Doe"
              value="{{ old('emergency_person_name') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('emergency_person_name') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Emergency person's address</label>
            <input
              type="text"
              name="emergency_address"
              class="employee-input"
              placeholder="ex. 1xx Amansabina Mangaldan"
              value="{{ old('emergency_address') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('emergency_address') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Relation</label>
            <input
              type="text"
              name="relation"
              class="employee-input"
              placeholder="ex. Guardian"
              value="{{ old('relation') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('relation') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Emergency contact number</label>
            <input
              type="number"
              name="emergency_contact_number"
              class="employee-input"
              placeholder="ex. 09xxxxxxxxx"
              value="{{ old('emergency_contact_number') }}"
            />
            @if ($errors->any())
            @foreach ($errors->get('emergency_contact_number') as $error)
                <p style="color: red">{{ $error }}</p>
            @endforeach
        @endif
          </div>
        </div>

        <div class="registration-layout-button">
          <input type="submit" value="Create Profile" />
        </div>
      </form>
    </div>
    <!-- <div class="employee__main-duty-posted">Duty posted</div> -->
  </section>
</x-layout>