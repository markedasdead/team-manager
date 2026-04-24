<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasUuids;

    protected $fillable = [
        'column_id', 
        'team_id', 
        'name', 
        'description', 
        'deadline', 
        'order'
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];
}