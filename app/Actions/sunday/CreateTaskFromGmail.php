<?php

namespace App\Actions\Sunday;

use App\Events\AutomationCompleted;
use App\Libraries\GoogleService;
use App\Models\Automation;
use App\Models\Board;
use App\Models\Item;
use App\Models\Stage;
use Google\Service\Gmail;
use PhpMimeMailParser\Parser as EmailParser;

class CreateTaskFromGmail
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  Automation  $automation
     * @param  Google_Calendar_Events  $calendarEvents
     * @return void
     */
    public static function create(Automation $automation)
    {
        $maxResults = 50;
        $track = json_decode($automation->track, true);
        $track['historyId'] = $track['historyId'] ?? 0;
        $config = json_decode($automation->config);
        $client = GoogleService::getClient($automation->integration_id);
        $service = new Gmail($client);
        $condition = isset($config->condition) && $config->value ? "$config->condition($config->value)" : "";
        if (!$condition) {
            $condition = $config->value ?? "";
        }
        $results = $service->users_threads->listUsersThreads("me", ['maxResults' => $maxResults, 'q' => "$condition"]);

        foreach ($results->getThreads() as $index => $thread) {
            $theadResponse = $service->users_threads->get("me", $thread->id, ['format' => 'MINIMAL']);
            $message = $theadResponse->getMessages()[0];
            if ($message) {
                $raw = $service->users_messages->get("me", $message->id, ['format' => 'raw']);
                $parser = self::parseEmail($raw);

                $body = $parser->getMessageBody('html');
                $mail = [
                    'index' => $index,
                    'from' => $parser->getHeader('from'),
                    'subject' => $parser->getHeader('subject'),
                    'messageId' => $parser->getHeader('message-id'),
                    'id' => $message->id,
                    'threadId' => $message->threadId,
                    'historyId' => $message->historyId
                ];

                if ($index == 0) {
                    $automation->track = json_encode($mail);
                    $automation->save();
                }

                $mail['message'] = $body;
                $board = Board::find($automation->board_id);
                $stage = !empty($config->stage_id) ? Stage::find($config->stage_id) : $board->stages[0];
                $item = [
                    'title' => $mail['subject'],
                    'board_id' => $stage->board_id,
                    'user_id' => $stage->user_id,
                    'stage_id' => $stage->id,
                    'team_id' => $stage->team_id,
                    "resource_id" => $mail['id'],
                    "resource_origin" => 'message',
                    "resource_type" => 'gmail',
                    'fields' => [
                        ['name' => 'url_id', 'type' => 'url', 'value' => "https://mail.google.com/mail/#search/Rfc822msgid:{$mail['messageId']}", 'hide' => 1],
                        ['name' => 'url_subject', 'type'=> 'url', 'value' => "https://mail.google.com/mail/#search/subject:{$mail['subject']}", 'hide' => 1],
                        ['name' => 'snippet', 'type' => 'text', 'value' => $message->snippet],
                        ['name' => 'sender', 'type' => 'text', 'value' => $mail['from']],
                        ['name' => 'automation_id', 'value' => $automation->id, 'hide' => 1],
                    ]
                ];
                Item::createEvent($item, [
                    "resource_id" => $item['resource_id'],
                    "resource_origin" => 'gmail',
                    "stage_id" => $item['stage_id']
                ]);
            }
        };

        AutomationCompleted::dispasstch($automation);
    }

    public static function parseEmail($raw)
    {
        $switched = str_replace(['-', '_'], ['+', '/'], $raw['raw']);
        $raw = base64_decode($switched);
        return (new EmailParser)->setText($raw);
    }
}
