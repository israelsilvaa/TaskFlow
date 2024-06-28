<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title', 'description'];

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
}