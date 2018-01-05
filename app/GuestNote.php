<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuestNote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'guest_id', 'note',
    ];

    /**
     * Relationships
     */

    public function guest()
    {
        return $this->belongsTo('App\Guest');
    }

    /**
     * Scopes
     */

    public function scopeForEvent($query, int $eventId)
    {
        return $query
            ->join('guests', 'guest_notes.guest_id', '=', 'guest.id')
            ->where('guest.event_id', $eventId);
    }

    public function scopeForGuest($query, int $guestId)
    {
        return $query->where('guest_id', $guestId);
    }
}
