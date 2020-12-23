<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutomationServices extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('automation_services')->insert([
            'name' => 'Calendar',
            'logo' => '/images/calendar.png',
        ]);

        DB::table('automation_recipes')->insert([
            'automation_service_id' => 1,
            'name' => 'createItemFromCalendar',
            'service_name' => 'Calendar',
            'mapper' => json_encode(["input" => ["board_id", "stage_id"]]),
            'sentence' => "When event created in {calendar} create an item in {stage} of {board}"
        ]);

        DB::table('automation_services')->insert([
            'name' => 'Gmail',
            'logo' => '/images/gmail.png',
        ]);

        DB::table('automation_recipes')->insert([
            'automation_service_id' => 2,
            'name' => 'createItemFromGmail',
            'service_name' => 'Gmail',
            'mapper' => json_encode(["input" => ["board_id", "stage_id"]]),
            'sentence' => "When email received with {conditions} create an item in {stage} of {board}"
        ]);
    }
}
