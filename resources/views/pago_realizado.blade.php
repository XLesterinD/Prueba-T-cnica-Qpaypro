<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Pago</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9fafb;
            text-align: center;
            padding: 50px;
        }
        .card {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: {{ $status === 'success' ? '#16a34a' : '#dc2626' }};
        }
        p {
            margin-top: 10px;
            font-size: 1.1rem;
            color: #374151;
        }
        .buttons {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            margin: 0 5px;
        }
        .btn-success { background-color: #16a34a; }
        .btn-dashboard { background-color: #2563eb; }
    </style>
</head>
<body>
    <div class="card">
        @if ($status === 'success')
            <h1>✅ ¡Pago realizado con éxito!</h1>
            <p>Muchas gracias por su donación. Su apoyo significa mucho para nosotros.</p>
        @else
            <h1>❌ Pago no completado</h1>
            <p>Hubo un problema al procesar su pago. Por favor, intente nuevamente.</p>
        @endif

        <div class="buttons">
            <a href="{{ route('landing') }}" class="btn btn-success">Ir al inicio</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-dashboard">Ir al Dashboard</a>
            @endauth
        </div>
    </div>
</body>
</html>
