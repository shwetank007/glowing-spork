<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes';

    protected $fillable = ['subject', 'note', 'task_id', 'attachment'];

    protected $casts = [
        'attachment' => 'array',
    ];
}
