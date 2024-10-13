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
          <div class="current_active-page-indicator">
            <span class="material-symbols-outlined current_active-page">
              check_circle
            </span>
            <p>Duty Hours</p>
          </div>
          <div>
            <span class="material-symbols-outlined"> check_circle </span>
            <p>Student Profile</p>
          </div>
        </div>
      </div>
      <form
        class="registration-layout"
        action="{{ request()->route('id') ? route('students.hkDutyQuotaExistingStore', ['id' => request()->route('id')]) : route('students.hkDutyQuota_post') }}"
        method="POST"
      >
      @csrf
        <div class="registration-layout-input">
          <div>
            <label for="">Hk Hours</label>
            <input
              type="number"
              name="duty_hours"
              class="employee-input"
              placeholder="ex. 80"
            />
            @if ($errors->any())
            @foreach ($errors->get('duty_hours') as $error)
              <p style="color: red">{{ $error }}</p>
            @endforeach
                
            @endif
          </div>
        </div>

        <div class="registration-layout-button">
          <input type="submit" value="Create Duty Hours" />
        </div>
      </form>
    </div>
    <!-- <div class="employee__main-duty-posted">Duty posted</div> -->
  </section>
</x-layout>