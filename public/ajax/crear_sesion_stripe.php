<?php
require_once __DIR__ . '/../../vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51RTVszDmtgP8bOAl8awJdhSXxdUsiSWbHsDa0C21KSXMtX68VfChVN6TwPhqxvzLaOozwyTYh93bCqkL5uQ0F1vD00fiGl7dFa'); // tu clave secreta real de pruebas

header('Content-Type: application/json');

$plan = $_GET['plan'] ?? 'mensual';
$precio = ($plan === 'anual') ? 9999 : 999;

$YOUR_DOMAIN = 'http://localhost:3000'; 

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Suscripción Premium - ' . ucfirst($plan),
                ],
                'unit_amount' => $precio,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/premium_exito?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $YOUR_DOMAIN . '/premium_cancelado',
    ]);

    echo json_encode(['id' => $checkout_session->id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
