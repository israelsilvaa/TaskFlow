<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'status_id', 'due_date'];
    protected $dates = ['created_at', 'updated_at', 'due_date'];

    /**
     * Get the user that owns the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The users that are assigned to the task.
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_assignments');
    }

    /**
     * Get the status of the task.
     */
    public function status()
    {
        return $this->belongsTo(Statuses::class, 'status_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i');
    }

    public function getDueDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i');
    }
}
