<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'event_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'event_at',
    ];

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function guests()
    {
        return $this->hasMany('App\Guest');
    }

    public function guestNotes()
    {
        return $this->hasManyThrough('App\GuestNote', 'App\Guest');
    }

    public function todos()
    {
        return $this->hasMany('App\Todo');
    }

    /**
     * Scopes
     */

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('event_at', '=', Carbon::today()->format('Y-m-d'));
    }

    public function scopeTomorrow($query)
    {
        return $query->whereDate('event_at', '=', Carbon::tomorrow()->format('Y-m-d'));
    }

    public function scopeInPast($query)
    {
        return $query->whereDate('event_at', '<', Carbon::today()->format('Y-m-d'));
    }

    public function scopeInFuture($query)
    {
        return $query->whereDate('event_at', '>', Carbon::today()->format('Y-m-d'));
    }

    public function scopeNoDate($query)
    {
        return $query->whereNull('event_at');
    }
}
