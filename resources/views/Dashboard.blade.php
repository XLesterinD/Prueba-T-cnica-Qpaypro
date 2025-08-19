@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Dashboard de Donaciones</h2>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donations as $donation)
            <tr>
                <td>{{ $donation->full_name }}</td>
                <td>{{ $donation->email }}</td>
                <td>{{ $donation->phone }}</td>
                <td>Q {{ number_format($donation->amount, 2) }}</td>
                <td>
                    @if($donation->status === 'success')
                        <span class="badge bg-success">Exitoso</span>
                    @elseif($donation->status === 'failed')
                        <span class="badge bg-danger">Fallido</span>
                    @else
                        <span class="badge bg-warning">Pendiente</span>
                    @endif
                </td>
                <td>{{ $donation->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
