<?php

use App\Services\UserStatusService;

return [
    UserStatusService::CITY => \App\Commands\RecordCity::class,
    UserStatusService::BOTTLE => \App\Commands\RecordBottle::class,
    UserStatusService::ADDRESS => \App\Commands\RecordAddress::class,
    UserStatusService::DATE => \App\Commands\RecordDate::class,
    UserStatusService::PHONE => \App\Commands\RecordPhone::class,
];