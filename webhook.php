<?php
use App\Commands\MainMenu;
use App\Models\User;
use App\ViberHelpers\ViberParser;

require_once(__DIR__ . '/bootstrap.php');

$update = \json_decode(file_get_contents('php://input'), TRUE);
$handlers = include(__DIR__ . '/App/config/keyboard_commands.php');

$viber_parser = new ViberParser($update);
$viber = new \App\ViberHelpers\ViberApi();
$viber->sendMessage('test', $viber_parser::getChatId());
if ($handlers[$update['message']['text']]) {
    (new $handlers[$update['message']['text']]($update))->handle($update);
    exit;
} else {
    $handlers = include(__DIR__ . '/App/config/mode_commands.php');
    $viber_parser = new ViberParser($update);
    $user = User::firstOrCreate([
        'chat_id' => $viber_parser::getChatId(),
        'user_name' => $viber_parser::getUserName()
    ]);
    if ($user && $handlers[$user->status]) {
        (new $handlers[$user->status]($update))->handle($update);
        exit;
    }
}

(new MainMenu())->handle($update);
