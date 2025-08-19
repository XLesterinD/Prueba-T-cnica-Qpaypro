@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h1>Bienvenido a QpayFundation</h1>
    <p class="lead">Nuestra misión es transformar vidas a través de la educación y el apoyo comunitario.</p>
    <a href="{{ route('donar') }}" class="btn btn-primary btn-lg mt-4">Donar Ahora</a>
</div>
@endsection
