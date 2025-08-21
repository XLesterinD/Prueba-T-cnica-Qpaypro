@extends('layouts.app')
@section('content')

<div class="login_container">
    <form class="form_login" method="POST" action="{{ route('login.post') }}">
        @csrf

        <h1>Iniciar sesión</h1>
        
        <label>Email</label>
        <input class="input-group-text" type="email" name="email" value="{{ old('email') }}" required>

        <label>Contraseña</label>
        <input class="input-group-text" type="password" name="password" required>

        <button class="btn btn-primary" type="submit">Ingresar</button>
    </form>
</div>

<style>
    .login_container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
    }

    .form_login {
        display: flex;
        flex-direction: column;
        width: 30%; 
        height: auto;
        border-radius: 20px;
        box-shadow: rgb(0, 0, 0) 0 0 8px;
        padding: 30px;
        background: white; 
        color: black;      
    }

    .form_login button {
        width: 100%;
        margin-top: 25px;
    }

    .form_login h1 {
        text-align: center;
    }
</style>
@endsection
