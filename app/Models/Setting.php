<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'team_id', 'name', 'value'];

    public static function getFormatted($where) {
        $settings = Setting::where($where)->get();

        $formattedSettings = [];

        foreach ($settings as $setting) {
            $formattedSettings[$setting->name] = $setting['value'];
        }

        return $formattedSettings;
    }
}
