@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Formulario de Donación</h2>
    <form method="POST" action="{{ route('donar.store') }}">
        @csrf

        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Apellido</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Correo electrónico</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Monto de donación (Q)</label>
            <input type="number" name="amount" class="form-control" value="{{ old('amount') }}">
            @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Mensaje opcional</label>
            <textarea name="message" class="form-control">{{ old('message') }}</textarea>
        </div>
        
        <div class="mb-3">
            <label>País</label>
            <input type="text" name="country" class="form-control" value="{{ old('country') }}">
            @error('country') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Ciudad</label>
            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
            @error('city') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Estado</label>
            <input type="text" name="state" class="form-control" value="{{ old('state') }}">
            @error('state') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Dirección</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        
        <div class="mb-3">
            <label>Código Postal</label>
            <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}">
            @error('zip_code') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>NIT</label>
            <input type="text" name="nit" class="form-control" value="{{ old('nit') }}">
            @error('nit') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    
        <button type="submit" class="btn btn-success">Continuar con el pago</button>
    </form>
</div>
@endsection
