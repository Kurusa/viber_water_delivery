<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordDate extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::DATE) {
            $record = Record::where('chat_id', $this->viber_parser::getChatId())->where('status', RecordStatusService::FILLING)->first();
            $record->date = $this->viber_parser::getMessage();
            $record->save();
            $this->triggerCommand(RecordPhone::class);
        } elseif ($this->user->status == UserStatusService::ADDRESS) {
            $this->user->status = UserStatusService::DATE;
            $this->user->save();

            $this->viber->sendMessageWithKeyboard($this->text['write_date'], [
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