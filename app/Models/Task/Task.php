<?php

namespace App\Models\Task;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'endTask',
        'ownerId',
        'assigneeId',
        'status',
        'finished_at'
    ];

    function owner()
    {
        return $this->belongsTo(User::class, 'ownerId');
    }
    
    function assignee()
    {
        return $this->belongsTo(User::class, 'assigneeId');
    }
}
