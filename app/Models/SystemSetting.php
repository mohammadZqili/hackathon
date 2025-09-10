<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'description'];

    /**
     * Get the value attribute casted to the correct type.
     */
    public function getValueAttribute($value)
    {
        // Return raw value without casting for boolean handling in controllers
        return $value;
    }

    /**
     * Set the value attribute.
     */
    public function setValueAttribute($value)
    {
        // Store as string for consistency
        $this->attributes['value'] = (string) $value;
    }

    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set($key, $value, $group = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group
            ]
        );
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup($group)
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
