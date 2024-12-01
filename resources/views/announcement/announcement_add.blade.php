<x-layout>
  <section class="main_content-employee-student">
    <header>
      <p>Announcement / Add</p>
      <img
        src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg"
        alt=""
      />
    </header>
    <div class="employee-student__main-register">
      <div class="employee-student__main-register-header">
        <div
          class="employee-student__main-register-header-content annoucement-header"
        >
          <div class="current_active-page-indicator">
            <span class="material-symbols-outlined current_active-page">
              campaign
            </span>
            <p>Post an annoucement</p>
          </div>
        </div>
      </div>
      <form
        class="registration-layout"
        action="{{ route('announcement.post')}}"
        enctype="multipart/form-data"
        method="POST"
      >
      @csrf
        <div class="registration-layout-input">
          <div>
            <label for="">Heading</label>
            <input
              type="text"
              name="heading"
              class="employee-input"
              placeholder="Add a heading"
              value="{{ old('heading') }}"
            />
            @if ($errors->any())
                @foreach ($errors->get('heading') as $error)
                    <p style="color: red">{{$error}}</p>
                @endforeach
            @endif
          </div>
          <div>
            <label for="">Description</label>
            <input
              type="text"
              name="description"
              class="employee-input"
              placeholder="Add a description"
              value="{{old('description')}}"
            />
            @if ($errors->any())
            @foreach ($errors->get('description') as $error)
                <p style="color: red">{{$error}}</p>
            @endforeach
        @endif
          </div>
          <div>
            <label for="">Announcement Image</label>
            <input 
            type="file"
            name="announcement_image"
            class="employee-input" />
          </div>
          @if ($errors->any())
          @foreach ($errors->get('announcement_image') as $error)
              <p style="color: red">{{$error}}</p>
          @endforeach
      @endif
        </div>

        <div class="registration-layout-button">
          <input type="submit" value="Post annoucement" />
        </div>
      </form>
    </div>
    <!-- <div class="employee__main-duty-posted">Duty posted</div> -->
  </section>
</x-layout>