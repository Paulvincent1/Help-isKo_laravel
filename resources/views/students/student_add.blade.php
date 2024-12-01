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
          <div class="current_active-page-indicator">
            <span class="material-symbols-outlined current_active-page">
              check_circle
            </span>
            <p>Personal Details</p>
          </div>
          <div>
            <span class="material-symbols-outlined"> check_circle </span>
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
        action="{{ route('students.student_add_post') }}"
        method="POST"
      >
      @csrf
        <div class="registration-layout-input">
          <div>
            <label for="">Name</label>
            <input
              type="text"
              class="employee-input"
              name="name"
              placeholder="ex. John Doe"
            />
            @if ($errors->any())
            @foreach ($errors->get('name') as $error)
            <p style="color: red">{{ $error }}</p> 
            @endforeach
                
            @endif
          </div>
          <div>
            <label for="">Email</label>
            <input
              type="text"
              class="employee-input"
              name="email"
              placeholder="ex. john@gmail.com"
            />
            @if ($errors->any())
            @foreach ($errors->get('email') as $error)
            <p style="color: red">{{ $error }}</p> 
            @endforeach
                
            @endif
          </div>
          <div>
            <label for="">Password</label>
            <input
              type="password"
              minlength="8"
              class="employee-input"
              name="password"
              placeholder="Type your password"
            />
            @if ($errors->any())
            @foreach ($errors->get('password') as $error)
            <p style="color: red">{{ $error }}</p> 
            @endforeach
                
            @endif
          </div>
          <div>
            <label for="">Confirm Password</label>
            <input
              type="password"
              minlength="8"
              class="employee-input"
              name="password_confirmation"
              placeholder="Retype your password"
            />
            @if ($errors->any())
            @foreach ($errors->get('password_confirmation') as $error)
                <p style="color: red">{{ $error }}</p> 
            @endforeach
                
            @endif
          </div>
        </div>

        <div class="registration-layout-button">
          <input type="submit" value="Create Account" />
        </div>
      </form>
    </div>
    <!-- <div class="employee__main-duty-posted">Duty posted</div> -->
  </section>
</x-layout>