@extends('layouts.app')

@section('content')
<div class="landing">
    <div class="donation-box">
        <h1>Apoya nuestra causa</h1>
        <p>Con tu donación, transformas vidas. Gracias por estar aquí.</p>
        <a href="{{ route('donar') }}" class="btn-donate">Donar Ahora</a>
    </div>
</div>
@endsection

