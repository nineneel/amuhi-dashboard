<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'notification_email',
        'notification_app',
        'language',
        'theme',
        'privacy_settings',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'notification_email' => 'boolean',
            'notification_app' => 'boolean',
            'privacy_settings' => 'array',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
