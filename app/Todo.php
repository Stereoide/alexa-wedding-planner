<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'due_at'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'todo', 'status', 'due_at',
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

    public function scopeWithoutDueDate($query)
    {
        return $query->whereNull('due_at');
    }

    public function scopeWithDueDate($query)
    {
        return $query->whereDate('due_at', '>=', Carbon::today()->toDateString());
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_at', '=', Carbon::today()->toDateString());
    }

    public function scopeDueTomorrow($query)
    {
        return $query->whereDate('due_at', '=', Carbon::tomorrow()->toDateString());
    }

    public function scopeDueInTheNextFewDays($query, int $days = 5)
    {
        return $query->whereDate('due_at', '>=', Carbon::today()->addDay()->toDateString())->whereDate('due_at', '<=', Carbon::today()->addDays($days)->toDateString());
    }

    public function scopeOverdue($query)
    {
        return $query->whereDate('due_at', '<', Carbon::today()->toDateString());
    }
}
