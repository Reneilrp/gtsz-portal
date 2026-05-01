<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_number',
        'gender',
        'birth_date',
        'address',
        'guardian_name',
        'guardian_contact',
        'school_year_id',
        'enrolled_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    // Here is the magic for your pivot table!
    public function sections()
    {
        return $this->belongsToMany(Section::class)->withTimestamps();
    }
}