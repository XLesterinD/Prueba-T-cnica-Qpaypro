<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\Http;

class DonationController extends Controller
{
   //Mostrar formulario de donaciones
    
    public function create()
    {
        return view('donar');
    }

     //Guardar donación y procesar pago
    
    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:20',
            'amount' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:500',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|numeric|min:5',
            'nit' => 'required|string|max:100'
        ]);

        // Guardar donación en BD con estado "pending" o pendiente
        $donation = Donation::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'amount' => $validated['amount'],
            'message' => $validated['message'] ?? null,
            'country' => $validated['country'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'address' => $validated['address'],
            'zip_code' => $validated['zip_code'], 
            'nit' => $validated['nit'],
            'status' => 'pending',
        ]);

        // Procesar pago en QPayPro
        return $this->procesarPago($donation);

    }

    // Proceso de integración con Qpaypro
    public function procesarPago(Donation $donation)
        {
            $QPAY_PUBLIC_KEY = "visanetgt_qpay";
            $QPAY_PRIVATE_KEY = "88888888888";
            $SANDBOX_MODE = true;
            $X_RELAY_URL = 'http://127.0.0.1:8000/';

            //Json solicitado por Qpaypro
            $payload = [
                "x_login" => $QPAY_PUBLIC_KEY,
                "x_api_key" => $QPAY_PRIVATE_KEY,
                "x_amount" => number_format($donation->amount, 2, '.', ''),
                "x_currency_code" => "GTQ",
                "x_first_name" => $donation->name,
                "x_last_name" => $donation->last_name,
                "x_phone" => $donation->phone,
                "x_ship_to_address" => $donation->address,
                "x_ship_to_city" => $donation->city,
                "x_ship_to_country" => $donation->country,
                "x_ship_to_state" => $donation->state,
                "x_ship_to_zip" => $donation->zip_code, 
                "x_ship_to_phone" => $donation->phone,
                "x_description" => "Donación",
                "x_url_success" => $X_RELAY_URL,
                "x_url_error" => $X_RELAY_URL,
                "x_url_cancel" => $X_RELAY_URL,
                "http_origin" => $X_RELAY_URL,
                "x_company" => $donation->nit,
                "x_address" => $donation->address,
                "x_city" => $donation->city,
                "x_country" => $donation->country,
                "x_state" => $donation->state,
                "x_zip" => $donation->zip_code,
                "products" => json_encode([
                    ["Donativo", $donation->amount, "", "1", "1", "1"]
                ]),
                "x_freight" => "0.00",
                "taxes" => "0.00",
                "x_email" => $donation->email,
                "x_type" => "AUTH_ONLY",
                "x_method" => "CC",
                "x_invoice_num" => "DON-" . $donation->id,
                "custom_fields" => json_encode([
                    "idSistema" => "1009",
                    "idCliente" => "2025",
                    "numerodeorden" => $donation->id
                ]),
                "x_visacuotas" => "si",
                "x_relay_url" => $X_RELAY_URL,
                "origen" => "PLUGIN",
                "store_type" => "hostedpage",
                "x_discount" => "0"
            ];

            //Se declara la URL a donde se enviará el Json para obtener el token
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

                
                if ($data['estado'] === 'success' && isset($data['data']['token'])) {
                    $token = $data['data']['token'];

                    // Cuando se obtiene el token se concatena a la URL correspondiente para redireccionar a la HP
                    $hostedpage = ($SANDBOX_MODE
                            ? 'https://sandboxpayments.qpaypro.com/checkout/store?token='
                            : 'https://payments.qpaypro.com/checkout/store?token='
                        ) . $token;

                    if (filter_var($hostedpage, FILTER_VALIDATE_URL)) {
                        return redirect()->away($hostedpage);
                    } 
                    else {
                        return response()->json(['error' => 'URL inválida', 'url' => $hostedpage]);
                    }

                } else {
                    return response()->json([
                        'error' => 'Token inválido',
                        'response' => $data
                    ], 400);
                }
            }

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Bad request',
                    'status' => $response->status(),
                    'body' => $response->body()
                ], $response->status());
            }
        }
    }
