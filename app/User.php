<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'event_id'
    ];

    /**
     * Relationships
     */

    public function events()
    {
        return $this->hasMany('App\Event');
    }

    public function lastEvent()
    {
        return $this->hasOne('\App\Event');
    }

    /**
     * Scopes
     */

    public function scopeUndecided($query)
    {
        return $query->where('status', 'undecided');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeUnable($query)
    {
        return $query->where('status', 'unable');
    }
}
