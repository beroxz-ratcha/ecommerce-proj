<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public static function getValue($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            return self::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function setValue($key, $value)
    {
        Cache::forget("setting_{$key}");
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    // เคลียร์ Cache
    // Setting::setValue('PROMPTPAY_ID', '0888888888');
    // Cache::forget('setting_PROMPTPAY_ID');

}
