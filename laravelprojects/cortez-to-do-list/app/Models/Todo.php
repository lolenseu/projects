<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task',
        'description',
        'status',
    ];

    // Optional: if you want default status
    protected $attributes = [
        'status' => 'Pending',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
