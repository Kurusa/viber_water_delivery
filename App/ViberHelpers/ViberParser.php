<?php

namespace App\ViberHelpers;

class ViberParser {

    private static $data;

    public function __construct($data)
    {
        self::$data = $data;
    }

    public static function getUserName()
    {
        return self::$data['sender']['name'] ?: self::$data['user']['name'];
    }

    public static function getMessage()
    {
        return self::$data['message']['text'];
    }

    public static function getChatId()
    {
        return self::$data['user']['id'] ?: self::$data['sender']['id'];
    }

}