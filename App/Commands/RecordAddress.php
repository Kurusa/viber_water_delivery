<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordAddress extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::ADDRESS) {
            $record = Record::where('chat_id', $this->viber_parser::getChatId())->where('status', RecordStatusService::FILLING)->first();
            $record->address = $this->viber_parser::getMessage();
            $record->save();
            $this->triggerCommand(RecordDate::class);
        } elseif ($this->user->status == UserStatusService::POMP) {
            $this->user->status = UserStatusService::ADDRESS;
            $this->user->save();

            $this->viber->sendMessageWithKeyboard($this->text['write_address'], [
                [
                    'Columns' => 6,
                    'Rows' => 1,
                    'ActionType' => 'reply',
                    'BgColor' => '#D1EDF2',
                    'TextOpacity' => 60,
                    'TextSize' => 'large',
                    'ActionBody' => 'cancel',
                    'Text' => $this->text['cancel']
                ],
            ]);
        }

    }

}