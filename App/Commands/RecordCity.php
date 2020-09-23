<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\UserStatusService;

class RecordCity extends BaseCommand
{

    function processCommand($par = false)
    {
        $buttons = [];
        $buttons[] = [
            'Columns' => 6,
            'Rows' => 1,
            'ActionType' => 'reply',
            'BgColor' => '#D1EDF2',
            'TextOpacity' => 60,
            'TextSize' => 'large',
            'ActionBody' => 'cancel',
            'Text' => $this->text['cancel']
        ];

        if ($this->user->status == UserStatusService::CITY) {
            Record::create([
                'chat_id' => $this->viber_parser::getChatId(),
                'city' => $this->viber_parser::getMessage()
            ]);
            $data = $this->text['city_list'][$this->viber_parser::getMessage()]['data'];
            foreach ($data as $key => $item) {
                $buttons[] = [
                    'Columns' => 6,
                    'Rows' => 1,
                    'ActionType' => 'reply',
                    'BgColor' => '#D1EDF2',
                    'TextOpacity' => 60,
                    'TextSize' => 'large',
                    'ActionBody' => $key,
                    'Text' => $item['name'] . ' ' . $item['price'].'грн'
                ];
            }
            $this->viber->sendMessageWithKeyboard($this->text['select_water'], $buttons);
            $this->user->status = UserStatusService::BOTTLE;
            $this->user->save();
        } else {
            foreach ($this->text['city_list'] as $key => $city) {
                $buttons[] = [
                    'Columns' => 6,
                    'Rows' => 1,
                    'ActionType' => 'reply',
                    'BgColor' => '#D1EDF2',
                    'TextOpacity' => 60,
                    'TextSize' => 'large',
                    'ActionBody' => $key,
                    'Text' => $city['name']
                ];
            }
            $this->viber->sendMessageWithKeyboard($this->text['select_city'], $buttons);

            $this->user->status = UserStatusService::CITY;
            $this->user->save();
        }
    }

}