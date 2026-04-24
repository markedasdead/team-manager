<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description', 'user_id'];

    public function columns()
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }
    
    public function members()
    {
        return $this->belongsToMany(User::class, 'members')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}