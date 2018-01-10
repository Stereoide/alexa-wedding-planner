<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'todo', 'status',
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

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
