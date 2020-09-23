<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class RecordPomp extends BaseCommand
{

    function processCommand($par = false)
    {
        $record = Record::where('chat_id', $this->viber_parser::getChatId())->where('status', RecordStatusService::FILLING)->first();
        if ($this->viber_parser::getMessage() == 'pomp_yes' || $this->viber_parser::getMessage() == 'pomp_no') {
            if ($this->viber_parser::getMessage() == 'pomp_no') {
                $record->pomp = 1;
                $record->price = $record->price + 120;
                $record->save();
            }
            $this->triggerCommand(RecordAddress::class);
        } elseif ($this->user->status == UserStatusService::POMP) {
            $this->viber->sendMessageWithKeyboard($this->text['select_pomp'], [
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
                    'ActionBody' => 'pomp_yes',
                    'Text' => $this->text['yes']
                ], [
                    'Columns' => 6,
                    'Rows' => 1,
                    'ActionType' => 'reply',
                    'BgColor' => '#D1EDF2',
                    'TextOpacity' => 60,
                    'TextSize' => 'large',
                    'ActionBody' => 'pomp_no',
                    'Text' => $this->text['no']
                ],
            ]);
        }

    }

}