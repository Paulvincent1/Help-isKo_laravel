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
                <a href="">Edit Profile</a>
                <p class="profile_info-personal-details-studid">
                  Student ID: {{ $user->student_number }}
                </p>
              </div>
            </div>
            <div>
              <p>Basic Details</p>
              <p>First name: {{ $user->first_name }}</p>
              <p>Last name: {{ $user->last_name }}</p>
              <p>Birthday: {{ $user->birthday }}</p>
              <p>Contact number: {{ $user->contact_number }}</p>
            </div>
            <div>
              <p>Education</p>
              <p>Student ID: {{ $user->student_number }}</p>
              <p>College: {{ $user->college }}</p>
              <p>Course: {{ $user->course }}</p>
              <p>Department: {{ $user->department }}</p>
              <p>Semester: {{ $user->semester }}</p>
              <p>Learning modality: {{ $user->learning_modality }}</p>
            </div>
            <div>
              <p>Parents</p>
              <p>Father's name: {{ $user->father_name }}</p>
              <p>Father's contact number: {{ $user->father_contact_number }}</p>
              <p>Mother's name: {{ $user->mother_name }}</p>
              <p>Mother's contact number: {{ $user->mother_contact_number }}</p>
            </div>

            <div>
              <p>Address</p>
              <p>Current address: {{ $user->current_address }}</p>
              <p>Current province: {{ $user->current_province }}</p>

              <p>Current country: {{ $user->current_country }}</p>
              <p>Current city: {{ $user->current_city }}</p>

              <p>Permanent address: {{ $user->permanent_address }}</p>
              <p>Permanent province: {{ $user->permanent_province }}</p>
              <p>Permanent country: {{ $user->permanent_country }}</p>
              <p>Permanent city: {{ $user->permanent_city }}</p>
            </div>
            <div>
              <p>Emergency</p>
              <p>Emergency person contact name: {{ $user->emergency_person_name }}</p>
              <p>Emergency address: {{ $user->emergency_address }}</p>
              <p>Relation: {{ $user->relation }}</p>
              <p>Emergency Contact Number: {{ $user->emergency_contact_number }}</p>
            </div>
          </div>
        </div>
        <div class="recent_activities"><p>Recent Activities</p></div>
      </section>
</x-layout>