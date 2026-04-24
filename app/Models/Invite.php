<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invite extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        "inviter_id",
        'status',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }   
}