<x-layout>
    <section class="main_content-profile">
        <header>
          <p>Employee / Profile</p>
          <img
            src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
            alt=""
          />
        </header>

        <div class="profile_info">
          <div class="display_profile-info">
            <div class="profile_info-personal-details">
              <p>Personal Details</p>
              <p class="profile_info-personal-details-empid">
                Employee ID: {{$user->employee_number}}
              </p>
            </div>
            <div>
              <p>Basic Details</p>
              <p>First name: {{ $user->first_name }}</p>
              <p>Last name: {{ $user->last_name }}</p>
              <p>Birthday: {{ $user->birthday }}</p>
              <p>Contact Number: {{ $user->contact_number }}</p>
              <p>Employee ID: {{ $user->employee_number }}</p>
            </div>
          </div>
        </div>
        <div class="recent_activities"><p>Recent Activities</p></div>
      </section>
</x-layout>