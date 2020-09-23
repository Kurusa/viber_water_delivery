<?php
return [
    'create_record_start' => \App\Commands\RecordCity::class,
    'cancel' => \App\Commands\MainMenu::class,
    'bottle_yes' => \App\Commands\RecordBottle::class,
    'bottle_no' => \App\Commands\RecordBottle::class,
    'pomp_yes' => \App\Commands\RecordPomp::class,
    'pomp_no' => \App\Commands\RecordPomp::class,
];
