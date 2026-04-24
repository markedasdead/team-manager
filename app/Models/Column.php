<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    use HasUuids;

    protected $fillable = ['team_id', 'title', 'order'];

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('order');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}