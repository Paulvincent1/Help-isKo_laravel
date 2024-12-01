{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title> 
</head>
<body>
    @if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
        
    @endif
    <form action="{{ route('loginAdmin')}}"
    method="POST">
    @csrf
    
        <label for="">Email</label>
        <input type="text" name="email">
        <label for="">password</label>
        <input type="password" name="password">
        <input type="submit">   
    </form>

   
</body>
</html>
 --}}


 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('styles/css/login.css')}}">
    <title>Login</title>
</head>
<body>
  <div class="main-container">
    <div class="background">
        <img class="birdy" src="{{ asset('asset/black_and_white_logo_high_quality.jpeg')}}" alt="">
    </div>
    <div class="login">
      <form action="{{ route('loginAdmin') }}" method="POST">
        @csrf

        <h1>Login</h1>
        
        <!-- Error Handling -->
        @if ($errors->any())
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
        @endif

        <!-- Email Input -->
        <div class="input-box">
          <input type="email" name="email" placeholder="Email" required>
          <i class='bx bxs-user'></i>
        </div>

        <!-- Password Input -->
        <div class="input-box">
          <input type="password" name="password" placeholder="Password" required>
          <i class='bx bxs-lock-alt'></i>
        </div>

        <!-- Remember Me and Forgot Password -->
        <div class="remember-forgot">
          <label><input type="checkbox">Remember me</label>
          <a href="">Forgot password?</a>
        </div>

        <!-- Submit Button -->
        <button class="button" type="submit">Login</button>

        <!-- Register Link -->
        <div class="register-link">
          <p>Don't have an account? <a href="">Register</a></p>
        </div>

      </form>
    </div>
  </div>
</body>
</html>
