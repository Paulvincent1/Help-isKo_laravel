<x-layout>
  <div class="main_content_nav">
    <ul>
      <a href="{{route('employee')}}"><li>Employee table</li></a>
      <a href="{{Route('employee.employee_add')}}" class="selected_main"
        ><li>Manage Employee</li></a
      >
    </ul>
  </div>
  <div class="add_form-student">
    <div class="guide_tab">
      <div class="guide_tab-content">
        <div class="guide_tab-register current-tab">
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
          <p>Employee Profile</p>
        </div>
      </div>
    </div>
  </div>

  <form
    action="{{route('employee.employee_add_post')}}"
    class="create_account"
    method="POST"
  >@csrf
    <p>Register</p>
    <div class="create_account-layout">
      <div class="create_account-content">
        <div class="">
          <label for="">Name</label>
          <input type="text" placeholder="Enter name" name="name"/>
        </div>
      </div>
      <div class="create_account-content">
        <div>
          <label for="">Phinmaed Email</label>
          <input type="text" placeholder="Enter phinmaed email" name="email"/>
        </div>
      </div>

      <div class="create_account-content">
        <div class="">
          <label for="">Password</label>
          <input type="password" placeholder="Password" name="password"/>
        </div>
      </div>
      <div class="create_account-content">
        <div>
          <label for="">Confirm Password</label>
          <input type="password" placeholder="Confirm Password" name="password_confirmation"/>
        </div>
      </div>
    </div>
    <div class="submit-button">
      <input type="submit" value="Next" />
    </div>
  </form>
  @if ($errors->any())

  <ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
  </ul>
      
  @endif
</x-layout>