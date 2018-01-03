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
        'name', 'status',
    ];

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
