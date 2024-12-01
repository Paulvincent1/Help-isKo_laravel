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
          <div>
            <span class="material-symbols-outlined"> check_circle </span>
            <p>Personal Details</p>
          </div>
          <div class="current_active-page-indicator">
            <span class="material-symbols-outlined current_active-page">
              check_circle
            </span>
            <p>Employee Profile</p>
          </div>
        </div>
      </div>
      <form class="registration-layout" action="{{ request()->route('id') ? route('employee.existing_employee_add_profile_store',['id' => request()->route('id')]) : route('employee.employee_add_profile_store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="registration-layout-input">
          <div>
            <label for="">First name</label>
            <input
              type="text"
              name="first_name"
              class="employee-input"
              placeholder="ex. John Doe"
              value="{{ old('first_name')}}"
            />
            @if ($errors->any())

              @foreach ($errors->get('first_name') as $error)
                  <p style="color: red">{{$error}}</p>
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
              value="{{ old('last_name')}}"
            />
            @if ($errors->any())

            @foreach ($errors->get('last_name') as $error)
            <p style="color: red">{{$error}}</p>
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
              value="{{old('birthday')}}"
            />
            @if ($errors->any())

            @foreach ($errors->get('birthday') as $error)
            <p style="color: red">{{$error}}</p>
            @endforeach
              
          @endif
          </div>
          <div>
            <label for="">Contact number</label>
            <input
              type="number"
              name="contact_number"
              class="employee-input"
              placeholder="09516773935"
               value="{{old('contact_number')}}"
            />
            @if ($errors->any())

            @foreach ($errors->get('contact_number') as $error)
            <p style="color: red">{{$error}}</p>
            @endforeach
              
          @endif
          </div>
          <div>
            <label for="">Employee number</label>
            <input
              type="tel"
              name="employee_number"
              class="employee-input"
              placeholder="03-2324-035763"
               value="{{old('employee_number')}}"
            />
            @if ($errors->any())

            @foreach ($errors->get('employee_number') as $error)
            <p style="color: red">{{$error}}</p>
            @endforeach
              
          @endif
          </div>
          <div>
            <label for="">Profile image</label>
            <input
              type="file"
              name="profile_img"
              class="employee-input"
            />
            @if ($errors->any())

            @foreach ($errors->get('profile_img') as $error)
            <p style="color: red">{{$error}}</p>
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