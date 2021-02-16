<?php

namespace App\Http\Controllers;

use App\Libraries\GoogleService;
use App\Models\AutomationService;
use App\Models\Integration;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();

        return Inertia::render('Integrations', [
            "services" => AutomationService::all(),
            "integrations" => Integration::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id
            ])->with(['automations'])->get()
        ]);
    }

    public function google(Request $request)
    {
       return GoogleService::setTokens((object) $request->post('credentials'), $request->user()->id);
    }

    // public function listCalendars(Request $request, Response $response)
    // {
    //    $calendars = GoogleService::listCalendars($request->user()->id);
    //    return $response->setContent($calendars->getItems());
    // }

    public function listCalendars() {
        $sheetsService = GoogleService::getSheetsService(12);
        $sheetService = GoogleService::getSheetService(12);
        $id = "1mIGie61tDQjMToWxuygRVgzGa73rdWhPyGRMyMZZ0Wk";
        $gid = "152562081";
        $sheets = $sheetsService->spreadsheets->get($id)->getSheets();
        $theSheet = null;
        foreach ($sheets as $sheet) {
            $theSheet = $sheet->getPropertyByName('id') == $gid ? $sheet : $theSheet;
        }
        dump($theSheet->name);
        die();
        $response = $sheetsService->spreadsheets_values->get($id, "{$sheetName->name}!A1:G70");
        $values = $response->getValues();
        return ["data" => $values];
    }
}
