<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

class MainMenu extends BaseCommand {

    function processCommand($par = false)
    {
        // delete possible undone record
        $filling_record = Record::where('chat_id', $this->viber_parser::getChatId())->where('status', RecordStatusService::FILLING)->first();
        if ($filling_record) {
            $filling_record->delete();
        }

        $this->user->status = UserStatusService::DONE;
        $this->user->save();

        $this->viber->sendMessageWithKeyboard($par ?: $this->text['main_menu'], [
            [
                'Columns' => 6,
                'Rows' => 1,
                'ActionType' => 'reply',
                'BgColor' => '#D1EDF2',
                'TextOpacity' => 60,
                'TextSize' => 'large',
                'ActionBody' => 'create_record_start',
                'Text' => $this->text['create_record']
            ],
        ]);
    }
}
