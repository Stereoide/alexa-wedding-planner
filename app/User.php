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
        return $this->hasOne('\App\Event', 'id', 'event_id');
    }
}
