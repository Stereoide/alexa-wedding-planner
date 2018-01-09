<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name',
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
}
