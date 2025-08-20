<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\Http;

class DonationController extends Controller
{
    
     //Muestra la LandingPage.
     //Es la pantalla inicial del sitio.
    
    public function landing()
    {
        return view('LandingPage');
    }

    
    // Muestra el formulario para donar.
    
    public function create()
    {
        return view('donar');
    }

    
    // Guarda la donación como "pending" y redirige a la pasarela QPayPro.
    
    public function store(Request $request)
    {
        // Validación de datos del formulario
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:150',
            'phone'      => 'required|string|max:20',
            'amount'     => 'required|numeric|min:1',
            'message'    => 'nullable|string|max:500',
            'country'    => 'required|string|max:100',
            'city'       => 'required|string|max:100',
            'state'      => 'required|string|max:100',
            'address'    => 'required|string|max:255',
            'zip_code'   => 'required|numeric|min:5',
            'nit'        => 'required|string|max:100'
        ]);

        // Crear registro de donación
        $donation = Donation::create([
            ...$validated,
            'status' => 'pending',
        ]);

        // Redirigir a la ruta que inicia el pago en QPayPro
        return redirect()->route('qpaypro.checkout', ['donation' => $donation->id]);
    }

    
    //Envía los datos a QPayPro y redirige al checkout seguro.
   
    public function procesarPago(Donation $donation)
    {
        // Llaves de QPayPro
        $QPAY_PUBLIC_KEY  = "visanetgt_qpay";
        $QPAY_PRIVATE_KEY = "88888888888";
        $SANDBOX_MODE     = true;

        // URL de retorno/callback
        $X_RELAY_URL = route('qpaypro.callback');

        // Armado de payload según documentación de QPayPro jeje
        $payload = [
            "x_login"           => $QPAY_PUBLIC_KEY,
            "x_api_key"         => $QPAY_PRIVATE_KEY,
            "x_amount"          => number_format($donation->amount, 2, '.', ''),
            "x_currency_code"   => "GTQ",
            "x_first_name"      => $donation->name,
            "x_last_name"       => $donation->last_name,
            "x_phone"           => $donation->phone,
            "x_ship_to_address" => $donation->address,
            "x_ship_to_city"    => $donation->city,
            "x_ship_to_country" => $donation->country,
            "x_ship_to_state"   => $donation->state,
            "x_ship_to_zip"     => $donation->zip_code, 
            "x_ship_to_phone"   => $donation->phone,
            "x_description"     => "Donación",
            "x_url_success"     => $X_RELAY_URL,
            "x_url_error"       => $X_RELAY_URL,
            "x_url_cancel"      => $X_RELAY_URL,
            "http_origin"       => $X_RELAY_URL,
            "x_company"         => $donation->nit,
            "x_address"         => $donation->address,
            "x_city"            => $donation->city,
            "x_country"         => $donation->country,
            "x_state"           => $donation->state,
            "x_zip"             => $donation->zip_code,
            "products"          => json_encode([
                ["Donativo", $donation->amount, "", "1", "1", "1"]
            ]),
            "x_freight"         => "0.00",
            "taxes"             => "0.00",
            "x_email"           => $donation->email,
            "x_type"            => "AUTH_ONLY",
            "x_method"          => "CC",
            "x_invoice_num"     => "DON-" . $donation->id,
            "custom_fields"     => json_encode([
                "idSistema"     => "1009",
                "idCliente"     => "2025",
                "numerodeorden" => $donation->id
            ]),
            "x_visacuotas"      => "si",
            "x_relay_url"       => $X_RELAY_URL,
            "origen"            => "PLUGIN",
            "store_type"        => "hostedpage",
            "x_discount"        => "0"
        ];

        // Endpoint según entorno
        $url = $SANDBOX_MODE
            ? 'https://api-sandboxpayments.qpaypro.com/checkout/register_transaction_store'
            : 'https://api-payments.qpaypro.com/checkout/register_transaction_store';

        try {
            $response = Http::withoutVerifying()->post($url, $payload);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la petición: ' . $e->getMessage()], 500);
        }

        if ($response->successful()) {
            $data = $response->json();

            // Si el registro fue exitoso y hay token
            if (($data['estado'] ?? null) === 'success' && !empty($data['data']['token'])) {
                $token      = $data['data']['token'];
                $hostedpage = ($SANDBOX_MODE
                        ? 'https://sandboxpayments.qpaypro.com/checkout/store?token='
                        : 'https://payments.qpaypro.com/checkout/store?token='
                    ) . $token;

                // Redirigir al checkout de QPayPro
                if (filter_var($hostedpage, FILTER_VALIDATE_URL)) {
                    return redirect()->away($hostedpage);
                }

                return response()->json(['error' => 'URL inválida generada', 'url' => $hostedpage]);
            }

            return response()->json(['error' => 'Token inválido', 'response' => $data], 400);
        }

        return response()->json([
            'error'  => 'Petición rechazada por QPayPro',
            'status' => $response->status(),
            'body'   => $response->body()
        ], $response->status());
    }

    
    //Recibe el callback (GET o POST) de QPayPro y actualiza la donación.
     
    public function callback(Request $request)
{
    // Obtener el ID de la donación
    $donationId = $request->input('reference')
        ?? $request->input('numerodeorden')
        ?? $request->input('x_invoice_num');

    // Determinar el estado del pago
    $status = $request->input('status')
        ?? (($request->input('x_response_status') == 1) ? 'success' : 'failed');

    // Buscar la donación y actualizarla
    if ($donation = Donation::find($donationId)) {
        $donation->status         = ($status === 'success') ? 'success' : 'failed';
        $donation->response_data  = json_encode($request->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $donation->save();
    }

    // Mostrar la vista de confirmación
    return view('pago_realizado', ['status' => $status]);

}


    public function dashboard(Request $request)
    {
        // Filtros/búsqueda
        $query = Donation::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        }

        $donations = $query->orderBy('created_at', 'desc')->paginate(10);

        // Resumen por mes para el gráfico
        $summary = Donation::selectRaw('MONTH(created_at) as mes, status, COUNT(*) as total')
    ->groupBy('mes', 'status')
    ->orderBy('mes')
    ->get();

        // Reorganizamos para Chart.js
        $labels = [];
        $successData = [];
        $failData = [];

        foreach ($summary->groupBy('mes') as $mes => $items) {
            $labels[] = $mes;
            $successData[] = $items->where('status', 'success')->sum('total');
            $failData[]    = $items->where('status', '!=', 'success')->sum('total');
        }

        return view('Dashboard', [
            'donations'   => $donations,
            'labels'      => $labels,
            'successData' => $successData,
            'failData'    => $failData,
        ]);

    }
}


