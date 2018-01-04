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
        'event_id', 'name', 'status',
    ];

    /**
     * Relationships
     */

    public function event()
    {
        return $this->belongsTo('App\Event');
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
}
