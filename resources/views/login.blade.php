<!DOCTYPE html>
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

