<?php

namespace App\Actions\Sunday;

use App\Libraries\GoogleService;
use App\Models\Automation;
use App\Models\Board;
use Google_Service_Gmail;
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
    public static function create(Automation $automation, Array $gmailThreads)
    {
        $track = json_decode($automation->track, true);
        $track['historyId'] = $track['historyId'] ?? 0;
        $client = GoogleService::getClient($automation->user_id);
        $service = new Google_Service_Gmail($client);

        forEach($gmailThreads as $index => $thread) {
            $theadResponse = $service->users_threads->get("me", $thread->id, ['format' => 'MINIMAL']);
            $message = $theadResponse->getMessages()[0];
            if ($message && ($message->historyId > $track['historyId'])) {
                $raw = $service->users_messages->get("me", $message->id, ['format' => 'raw']);
                $parser = self::parseEmail($raw);

                $body = $parser->getMessageBody('html');
                $mail = [
                    'index' => $index,
                    'subject' => $parser->getHeader('subject'),
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
                $stage = $board->stages[0];
                $item = [
                    'title' => $mail['subject'],
                    'board_id' => $stage->board_id,
                    'user_id' => $stage->user_id,
                    'team_id' => $stage->team_id,
                ];
                $stage->items()->create($item);
                $messages[] = $message;

            }

        };
    }

    public static function parseEmail($raw) {
        $switched = str_replace(['-', '_'], ['+', '/'], $raw['raw']);
        $raw = base64_decode($switched);
        return (new EmailParser)->setText($raw);
    }
}
