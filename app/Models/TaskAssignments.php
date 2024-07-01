<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignments extends Model
{
    use HasFactory;

    protected $fillable = ['task_id','user_id'];


     /**
     * Get the task that is assigned.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user that is assigned.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
