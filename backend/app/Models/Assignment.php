<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'description',
        'max_score',
        'due_date',
    ];
}
