<?php
require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$data = [
    'auth_token' => env('VIBER_BOT_TOKEN'),
    'url' => env('VIBER_WEBHOOK_URL'),
    'event_types' => [
        'subscribed', 'unsubscribed', 'delivered', 'message', 'seen', 'conversation_started'
    ]
];

$ch = curl_init('https://chatapi.viber.com/pa/set_webhook');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

var_dump(curl_exec($ch));