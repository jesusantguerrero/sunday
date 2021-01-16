<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use HasFactory;
    protected $guarded = [];


    public static function stopRunningEntries(int $userId, int $teamId, string $endDate) {
        self::where([
            "user_id" => $userId,
            "team_id" => $teamId,
        ])->whereNull('end')->update([
            "end" => $endDate,
            "status" => 1
        ]);
    }
}
