<?php

namespace App\Models;

use App\MemberType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'member_type',
        'company_name',
        'phone',
        'address',
        'bio',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'member_type' => MemberType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
