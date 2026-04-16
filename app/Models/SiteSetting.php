<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

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
    public static function set($key, $value, $type = 'text', $description = null, $isPublic = false)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'is_public' => $isPublic,
            ]
        );

        // Clear cache if it's a public setting
        if ($isPublic) {
            static::clearPublicSettingsCache();
        }

        return $setting;
    }

    /**
     * Get all public settings with cache.
     */
    public static function getPublicSettings()
    {
        return \Illuminate\Support\Facades\Cache::remember('site_settings_public', 3600, function () {
            return static::where('is_public', true)->get();
        });
    }

    /**
     * Clear public settings cache.
     */
    public static function clearPublicSettingsCache()
    {
        \Illuminate\Support\Facades\Cache::forget('site_settings_public');
    }

    /**
     * Get settings by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get private settings.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Get the setting's formatted value.
     */
    public function getFormattedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return $this->value ? 'Sim' : 'Não';
            case 'number':
                return number_format($this->value);
            case 'currency':
                return 'R$ ' . number_format($this->value, 2, ',', '.');
            case 'date':
                return $this->value ? date('d/m/Y', strtotime($this->value)) : null;
            case 'datetime':
                return $this->value ? date('d/m/Y H:i', strtotime($this->value)) : null;
            default:
                return $this->value;
        }
    }

    /**
     * Get the setting's input type for forms.
     */
    public function getInputTypeAttribute()
    {
        return match($this->type) {
            'boolean' => 'checkbox',
            'number' => 'number',
            'email' => 'email',
            'url' => 'url',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'color' => 'color',
            'textarea' => 'textarea',
            'image' => 'file',
            default => 'text'
        };
    }
}
