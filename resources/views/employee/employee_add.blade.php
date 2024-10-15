<x-layout>
  <section class="main_content-employee-student">
    <header>
      <p>Employee / Register</p>
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
            <p>Employee Profile</p>
          </div>
        </div>
      </div>
      <form
        class="registration-layout"
        action="{{ route('employee.employee_add_post')}}"
        method="POST"
      >
      
      @csrf
        <div class="registration-layout-input">
          <div>
            <label for="">Name</label>
            <input
              type="text"
              name="name"
              class="employee-input"
              placeholder="ex. John Doe"
            />
            @if ($errors->any())
            @foreach ($errors->get('name') as $error)
            <p style="color: red">{{$error}}</p>
            @endforeach
          @endif
          </div>
          <div>
            <label for="">Email</label>
            <input
              type="text"
              name="email"
              class="employee-input"
              placeholder="ex. john@gmail.com"
            />
            @if ($errors->any())
            @foreach ($errors->get('email') as $error)
            <p style="color: red">{{$error}}</p>
            @endforeach
          @endif
          </div>
          <div>
            <label for="">Password</label>
            <input
              type="password"
              minlength="8"
              name="password"
              class="employee-input"
              placeholder="Type your password"
            />
            @if ($errors->any())
              @foreach ($errors->get('password') as $error)
              <p style="color: red">{{$error}}</p>
              @endforeach
            @endif
          </div>
          <div>
            <label for="">Confirm Password</label>
            <input
              type="password"
              minlength="8"
              name="password_confirmation"
              class="employee-input"
              placeholder="Retype your password"
            />
            @if ($errors->any())
              @foreach ($errors->get('password_confrimation') as $error)
              <p style="color: red">{{$error}}</p>
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