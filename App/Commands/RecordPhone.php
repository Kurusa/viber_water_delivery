<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;
use PHPMailer\PHPMailer\PHPMailer;

class RecordPhone extends BaseCommand
{

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::PHONE) {
            $record = Record::where('chat_id', $this->viber_parser::getChatId())->where('status', RecordStatusService::FILLING)->first();
            $record->phone = $this->viber_parser::getMessage();
            $record->status = RecordStatusService::DONE;
            $record->save();
            $this->triggerCommand(MainMenu::class, $this->text['record_done']);

            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = TRUE;
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;
            $mail->Username = env('GMAIL_FROM');
            $mail->Password = env('GMAIL_PASSWORD');
            $mail->Host = "smtp.gmail.com";
            $mail->Mailer = "smtp";
            $mail->SetFrom(env('GMAIL_FROM'), "from name");
            $mail->AddAddress(env('GMAIL_TO'));
            $mail->Subject = "Test email using PHP mailer";
            $mail->WordWrap = 80;
            $content = "Заказ {$record->id}

- {$record->address},

- {$record->phone}

- {$record->date}";
            $mail->MsgHTML($content);
            $mail->IsHTML(true);
        } elseif ($this->user->status == UserStatusService::DATE) {
            $this->user->status = UserStatusService::PHONE;
            $this->user->save();

            $this->viber->sendMessageWithKeyboard($this->text['write_phone'], [
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