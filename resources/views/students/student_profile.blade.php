<x-layout>
    <section class="main_content-profile">
        <header>
          <p>Student / Profile</p>
          <img
            src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
            alt=""
          />
        </header>

        <div class="profile_info">
          <div class="display_profile-info">
            <div class="profile_info-personal-details">
              <p>Personal Details</p>
              <div class="profile_info-personal-details-header">
                <a href="{{route('student.edit',['id' => $user->id])}}">Edit Profile</a>
                <p class="profile_info-personal-details-studid">
                  Student ID: {{ $user->studentProfile->student_number }}
                </p>
              </div>
            </div>
            <div>
              <p>Basic Details</p>
              <p>First name: {{ $user->studentProfile->first_name }}</p>
              <p>Last name: {{ $user->studentProfile->last_name }}</p>
              <p>Birthday: {{ $user->studentProfile->birthday }}</p>
              <p>Contact number: {{ $user->studentProfile->contact_number }}</p>
            </div>
            <div>
              <p>Education</p>
              <p>Student ID: {{ $user->studentProfile->student_number }}</p>
              <p>College: {{ $user->studentProfile->college }}</p>
              <p>Course: {{ $user->studentProfile->course }}</p>
              <p>Department: {{ $user->studentProfile->department }}</p>
              <p>Semester: {{ $user->studentProfile->semester }}</p>
              <p>Learning modality: {{ $user->studentProfile->learning_modality }}</p>
            </div>
            <div>
              <p>Parents</p>
              <p>Father's name: {{ $user->studentProfile->father_name }}</p>
              <p>Father's contact number: {{ $user->studentProfile->father_contact_number }}</p>
              <p>Mother's name: {{ $user->studentProfile->mother_name }}</p>
              <p>Mother's contact number: {{ $user->studentProfile->mother_contact_number }}</p>
            </div>

            <div>
              <p>Address</p>
              <p>Current address: {{ $user->studentProfile->current_address }}</p>
              <p>Current province: {{ $user->studentProfile->current_province }}</p>

              <p>Current country: {{ $user->studentProfile->current_country }}</p>
              <p>Current city: {{ $user->studentProfile->current_city }}</p>

              <p>Permanent address: {{ $user->studentProfile->permanent_address }}</p>
              <p>Permanent province: {{ $user->studentProfile->permanent_province }}</p>
              <p>Permanent country: {{ $user->studentProfile->permanent_country }}</p>
              <p>Permanent city: {{ $user->studentProfile->permanent_city }}</p>
            </div>
            <div>
              <p>Emergency</p>
              <p>Emergency person contact name: {{ $user->studentProfile->emergency_person_name }}</p>
              <p>Emergency address: {{ $user->studentProfile->emergency_address }}</p>
              <p>Relation: {{ $user->studentProfile->relation }}</p>
              <p>Emergency Contact Number: {{ $user->studentProfile->emergency_contact_number }}</p>
            </div>
          </div>
        </div>
        <div class="recent_activities"><p>Recent Activities</p></div>
      </section>
</x-layout>