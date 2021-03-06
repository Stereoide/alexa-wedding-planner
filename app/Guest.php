<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'name', 'status', 'child_or_adult',
    ];

    /**
     * Relationships
     */

    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    public function notes()
    {
        return $this->hasMany('App\GuestNote');
    }

    /**
     * Scopes
     */

    public function scopeForEvent($query, int $eventId)
    {
        return $query->where('event_id', $eventId);
    }

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

    public function scopeChild($query)
    {
        return $query->where('child_or_adult', 'child');
    }

    public function scopeAdult($query)
    {
        return $query->where('child_or_adult', 'adult');
    }

    public function scopeNeitherChildNorAdult($query)
    {
        return $query->whereNull('child_or_adult');
    }
}
