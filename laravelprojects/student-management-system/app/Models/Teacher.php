<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'birthday',
        'address',
        'phone',
        'email',
        'department',
        'subject_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}