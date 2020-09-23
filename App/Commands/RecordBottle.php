<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordBottle extends BaseCommand
{

    function processCommand($par = false)
    {
        $record = Record::where('chat_id', $this->viber_parser::getChatId())->where('status', RecordStatusService::FILLING)->first();
        if ($this->viber_parser::getMessage() == 'bottle_yes' || $this->viber_parser::getMessage() == 'bottle_no') {
            if ($this->viber_parser::getMessage() == 'bottle_no') {
                $record->bottle = 1;
                $record->price = $record->price + 160;
                $record->save();
            }

            $this->user->status = UserStatusService::POMP;
            $this->user->save();
            $this->triggerCommand(RecordPomp::class);
        } elseif ($this->user->status == UserStatusService::BOTTLE) {
            $record->water = $this->text['city_list'][$record->city]['data'][$this->viber_parser::getMessage()]['name'];
            $record->price = $this->text['city_list'][$record->city]['data'][$this->viber_parser::getMessage()]['price'];
            $record->save();
            $this->viber->sendMessageWithKeyboard($this->text['select_bottle'], [
                [
                    'Columns' => 6,
                    'Rows' => 1,
                    'ActionType' => 'reply',
                    'BgColor' => '#D1EDF2',
                    'TextOpacity' => 60,
                    'TextSize' => 'large',
                    'ActionBody' => 'cancel',
                    'Text' => $this->text['cancel']
                ], [
                    'Columns' => 6,
                    'Rows' => 1,
                    'ActionType' => 'reply',
                    'BgColor' => '#D1EDF2',
                    'TextOpacity' => 60,
                    'TextSize' => 'large',
                    'ActionBody' => 'bottle_yes',
                    'Text' => $this->text['yes']
                ], [
                    'Columns' => 6,
                    'Rows' => 1,
                    'ActionType' => 'reply',
                    'BgColor' => '#D1EDF2',
                    'TextOpacity' => 60,
                    'TextSize' => 'large',
                    'ActionBody' => 'bottle_no',
                    'Text' => $this->text['no']
                ],
            ]);
        }

    }

}