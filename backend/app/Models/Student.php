<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];

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