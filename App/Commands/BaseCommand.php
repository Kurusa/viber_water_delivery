<?php

namespace App\Commands;

use App\Models\User;
use App\ViberHelpers\ViberApi;
use App\ViberHelpers\ViberParser;

abstract class BaseCommand
{

    /**
     * @var ViberParser
     */
    protected $viber_parser;

    /**
     * @var ViberApi
     */
    protected $viber;

    protected $text;
    protected $user;

    private $update;

    function handle(array $update, $par = false)
    {
        $this->update = $update;
        $this->viber = new ViberApi();
        $this->viber_parser = new ViberParser($update);
        $this->viber->chat_id = $this->viber_parser::getChatId();
        $this->text = require(__DIR__.'/../config/text.php');

        $this->user = User::firstOrCreate([
            'chat_id' => $this->viber_parser::getChatId(),
            'user_name' => $this->viber_parser::getUserName()
        ]);

        $this->processCommand($par);
    }

    function triggerCommand($class, $par = false)
    {
        (new $class())->handle($this->update, $par);
    }

    abstract function processCommand($par = false);

}