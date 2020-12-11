<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Setting;

class SettingController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = Setting::where([
            'user_id' =>  $request->user()->id,
            'team_id' => $request->user()->current_team_id
        ])->get();
        return $response->send($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Response $response)
    {
        $settings = $request->post();
        $entryData = [
            'user_id' =>  $request->user()->id,
            'team_id' => $request->user()->current_team_id
        ];

        foreach ($settings as $settingName => $setting) {

            $setting = array_merge($entryData, [
                "value" => $setting,
                "name" => $settingName
            ]);
            $resource = Setting::where([
                'user_id' =>  $request->user()->id,
                'team_id' => $request->user()->current_team_id,
                'name' => $settingName
            ])->limit(1)->get();

            if (count($setting)) {
                $resource[0]->update($setting);
            } else {
                $resource = Setting::create($setting);
            }
        }

        $res = Setting::getFormatted([
            'user_id' =>  $request->user()->id,
            'team_id' => $request->user()->current_team_id
        ]);

        return $response->setContent($res);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $resource = Setting::find($id);
        $resource->update($request->post());
        return $resource;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $resource = Setting::find($id);
        $resource->delete();
        return $resource;
    }
}
