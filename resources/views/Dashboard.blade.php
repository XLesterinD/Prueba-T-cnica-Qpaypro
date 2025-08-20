@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel de Donaciones</h1>

    <!-- Barra de búsqueda opcional -->
    <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="form-control w-25 d-inline">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Monto</th>
                <th>Estado del pago</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $donation)
                <tr>
                    <td>{{ $donation->name }}</td>
                    <td>{{ $donation->email }}</td>
                    <td>{{ $donation->phone }}</td>
                    <td>${{ number_format($donation->amount, 2) }}</td>
                    <td>
                        @if($donation->status === 'success')
                            <span class="badge bg-success">Exitoso</span>
                        @else
                            <span class="badge bg-danger">Fallido</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay donaciones registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
<br>

    <div class="container mb-5">
    <h3>Resumen general de donaciones por mes</h3>
    <canvas id="donationsChart" height="100"></canvas>

    <script>
    const ctx = document.getElementById('donationsChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Aprobadas',
                    data: @json($successData),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                },
                {
                    label: 'Fallidas o Pendientes',
                    data: @json($failData),
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    </script>



    <!-- Paginación -->
    {{ $donations->links() }}
</div>
@endsection
