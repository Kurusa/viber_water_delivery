<?php

namespace App\ViberHelpers;

use DateTime;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleClient {

    function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = 'token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = '4/zAFJW96nUmo4MKEH5WuRWSlgcPrbfudTPTGdx8S0h9ImrUnuaLeoNyQ';

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    function create($title, $start, $end)
    {
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event([
            'summary' => $title,
            'start' => [
                'dateTime' => date('c', $start),
            ],
            'end' => [
                'dateTime' => $end,
            ],
        ]);
        $result = $service->events->insert(env('GOOGLE_CALENDAR_ID'), $event);

        return $result->id;
    }

    function getRecords($start_search, $end_search)
    {
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);

        $start_search = date('c', $start_search);
        $end_search = date('c', $end_search);

        $optParams = [
            'maxResults' => 200,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $start_search,
            'timeMax' => $end_search,
        ];
        $results = $service->events->listEvents(env('GOOGLE_CALENDAR_ID'), $optParams);
        $events = $results->getItems();
        $event_list = [];
        if (empty($events)) {
            $event_list = [];
        } else {
            foreach ($events as $event) {
                $start = $event->start->dateTime;
                $end = $event->end->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                $event_list[] = [
                    'start' => $start,
                    'end' => $end
                ];
            }
        }

        return $this->getFree($start_search, $end_search, $event_list);
    }

    private function getFree($start, $end, $events)
    {
        $start = strtotime($start);

        $end = strtotime($end);
        $result = [];
        // Kick off first appt time at beginning of the day.
        $appt_start_time = $start;

        // Loop through each appt slot in the search range.
        while ($appt_start_time < $end) {
            // Add 29:59 to the appt start time so we know where the appt will end.
            $appt_end_time = ($appt_start_time + 1799);
            // For each appt slot, loop through the current appts to see if it falls in a slot that is already taken.
            $slot_available = true;
            foreach ($events as $event) {
                $event_start = strtotime($event['start']);
                $event_end = strtotime($event['end']);

                // If the appt start time or appt end time falls on a current appt, slot is taken.
                if (($appt_start_time >= $event_start && $appt_start_time < $event_end) ||
                    ($appt_end_time >= $event_start && $appt_end_time < $event_end)) {
                    $slot_available = false;
                    break; // No need to continue if it's taken.
                }
            }

            // If we made it through all appts and the slot is still available, it's an open slot.
            if ($slot_available) {
                $date = new DateTime();
                $date->setTimestamp($appt_start_time);
                if ($date->format('H') >= 9 && $date->format('H') <= 17) {
                    $result[] = $appt_start_time;
                }
            }

            $appt_start_time += 60 * 30;
        }

        $acc_result = [];
        foreach ($result as $time) {
            $acc_result[] = [
                'time' => date('H:i', $time),
                'timestamp' => $time
            ];
        }
        return $acc_result;
    }

}
